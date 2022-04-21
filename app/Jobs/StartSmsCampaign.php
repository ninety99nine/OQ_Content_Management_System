<?php

namespace App\Jobs;

use Throwable;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\Campaign;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartSmsCampaign implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The campaign instance.
     *
     * @var \App\Models\Project
     */
    protected $project;

    /**
     * The campaign instance.
     *
     * @var \App\Models\Campaign
     */
    protected $campaign;

    /**
     * The campaign instance.
     *
     * @var int
     */
    protected $campaignBatchJobsCount;

    /**
     *  The unique ID of the job.
     *
     *  Sometimes, you may want to ensure that only one instance of a specific job is on
     *  the queue at any point in time. You may do so by implementing the ShouldBeUnique
     *  interface on your job class. So the current job will not be dispatched if another
     *  instance of the job is already on the queue and has not finished processing.
     *
     *  Refer: https://laravel.com/docs/8.x/queues#unique-jobs
     *
     *  @return string
     */
    public function uniqueId()
    {
        return $this->campaign->id;
    }

    /**
     * Create a new job instance.
     *
     * @param  App\Models\Project  $project
     * @param  App\Models\Campaign  $campaign
     * @return void
     */
    public function __construct(Project $project, Campaign $campaign, int $campaignBatchJobsCount)
    {
        info('Dispatch: StartSmsCampaign - construct()');

        $this->project = $project;
        $this->campaign = $campaign;
        $this->campaignBatchJobsCount = $campaignBatchJobsCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info('Dispatch: StartSmsCampaign - handle()');

        info('canStartSmsCampaign:' . ($this->campaign->canStartSmsCampaign() ? 'Yes' : 'No'));

        //  Check if this campaign can be started otherwise stop the campaign
        if( $this->campaign->canStartSmsCampaign() == false) return;

        info('Starting Campaign: ' . $this->campaign->name);

        /*******************************
         *  GET THE SENDABLE MESSAGES  *
         ******************************/

        $messages = [];

        //  If we have the message ids
        if( is_array($this->campaign->message_ids) ) {

            /*****************************
             *  GET SENDABLE MESSAGES    *
             ****************************/

            /**
             *  (1) Specific Message
             *  ---------------------
             *
             *  If the message_to_send is "Specific Message" then the
             *  message_ids will contain one array with a list of ids
             *  from the parent to the child message we want to send
             *  e.g
             *
             *  [ 1, 10, 20, 30 ]
             *
             *  In the case above we want the message with id of 30
             *  which is a descendant of message 1, 10, and 20
             *
             *  (1) Any Message
             *  ---------------
             *
             *  If the message_to_send is "Any Message" then the
             *  message_ids will contain one array with a list
             *  of arrays of ids from the parent to the child
             *  message we want to send e.g
             *
             *  [ [1, 10, 20, 30], [1, 10, 20, 35], .... , .e.t.c ]
             *
             *  In the case above we want the message with id of 30
             *  and message with id 35 which are both descendants
             *  of message 1, 10, and 20
             */

            if( $this->campaign->message_to_send == 'Specific Message' ) {

                //  Get the message ids, this is a single array of ids
                $message_ids = $this->campaign->message_ids;

                info( '$this->getQualifiedSendablesFromIdArray($message_ids)' );
                info( $this->getQualifiedSendablesFromIdArray($message_ids) );

                /**
                 *  Capture the qualified messages.
                 *  The result is a collection not
                 *  an array
                 */
                $messages = $this->getQualifiedSendablesFromIdArray($message_ids);

            //  Get the message ids, this is a multiple array of ids
            }elseif( $this->campaign->message_to_send == 'Any Message' ) {

                //  Extract the sendable messages
                foreach($this->campaign->message_ids as $message_ids) {

                    info('$messages count: ' . count($messages));

                    /**
                     *  Capture the qualified messages.
                     *  The result is an array not
                     *  an collection
                     */
                    $messages = array_merge(
                        $messages,
                        $this->getQualifiedSendablesFromIdArray($message_ids, $messages)->all()
                    );

                }

                //  Convert to collection
                $messages = collect($messages);
            }

        }

        info('Total messages: ' . $messages->count());

        //  Check if this campaign has messages to send otherwise stop the campaign
        if( $messages->count() == 0 ) return;

        /***************************************************
         *  GET THE SUBSCRIBERS READY FOR THE NEXT MESSAGE *
         **************************************************/

        // Get the ids of subscribers of this campaign (Those who have received content before but are now ready for the next content send)
        $subscriberIdsNotReadyForNextSms = $this->campaign->subscribersNotReadyForNextSms()->pluck('subscribers.id');

        //  Get the project subscribers (Those who have received or have not received content before)
        $subscribers = $this->project->subscribers()->with(['messages' => function($query) {

            //  Limit the loaded message to the message id and sent sms count to consume less memory
            return $query->select('messages.id', 'sent_sms_count')->orderBy('sent_sms_count');

        }])->select('subscribers.id', 'subscribers.msisdn');

        //  If this campaign has subscribers not ready for the next sms message
        if( count($subscriberIdsNotReadyForNextSms) ) {

            //  Exclude the subscribers that are not ready to receive the next sms message
            $subscribers = $subscribers->excludeIds($subscriberIdsNotReadyForNextSms);

        }

        //  If this campaign requires the subscribers to have an active subscription
        if( count($this->campaign->subcription_plan_ids ?? []) ) {

            //  Limit to subscribers with the given subscription plans
            $subscribers = $subscribers->hasActiveSubscription($this->campaign->subcription_plan_ids);

        }

        // Get the ids of subscribers of this campaign (Those who have received content before but are now ready for the next content send)
        $subscriberIdsNotReadyForNextSms = $this->campaign->subscribersNotReadyForNextSms()->pluck('subscribers.id');

        info('Total subscribers: ' . $subscribers->count());

        //  Check if this campaign has subscribers to send messages otherwise stop the campaign
        if( $subscribers->count() == 0 ) return;

        $jobs = [];

        //  Only query 1000 subscribers at a time
        $subscribers->chunk(1000, function ($chunked_subscribers) use ($messages, &$jobs) {

            //  Foreach subscriber we retrieved from the query
            foreach ($chunked_subscribers as $subscriber) {

                info('subscriber: ' . $subscriber);

                //  Determine if we can repeat messages
                $canRepeatMessage = true;

                //  Get the ids of the messages that have already been sent
                $sentMessageIds = collect($subscriber->messages)->pluck('id');

                info([
                    '$sentMessageIds->count()' => $sentMessageIds->count(),
                    '$messages->count()' => $messages->count(),
                    'gettype($messages)' => gettype($messages)
                ]);

                //  Check if the subscriber has seen every message
                $hasSeenEveryMessage = $sentMessageIds->count() == $messages->count();

                //  If we have seen every message
                if( $hasSeenEveryMessage == false ) {

                    info('hasSeenEveryMessage: No');

                    /**
                     *  Get the first message.
                     *
                     *  This message must be a message that the subscriber has not received
                     */
                    $message = $messages->whereNotIn('id', $sentMessageIds->all())->first();

                //  If we have not seen every message
                }elseif($hasSeenEveryMessage == true && $canRepeatMessage == true) {

                    info('hasSeenEveryMessage: Yes, we can repeat');

                    //  Get the message that has not been seen by the subscriber

                    /**
                     *  Get the first message with the lowest views
                     *
                     *  This message has the least views since we eager loaded the subscriber
                     *  messages relationship with the additional orderBy('sent_sms_count')
                     *  method.
                     */
                    $message = $messages->where('id', $subscriber->messages->first()->id)->first();

                //  If we don't have a new message to send
                }else{

                    info('hasSeenEveryMessage: Yes, but we cannot repeat');

                    $message = null;

                    //  Stop execution of futher code
                    return;

                }

                info('Message: ' . $message);

                //  If we have a message to send
                if( $message ) {

                    $sender = $this->project->settings['sms_sender'];
                    $username = $this->project->settings['sms_username'];
                    $password = $this->project->settings['sms_password'];

                    //  If the sms account information is provided
                    if( $sender && $username && $password ) {

                        //  Create a job to send this message
                        $jobs[] = new SendSubscriptionSms($subscriber, $message, $this->campaign, $sender, $username, $password);

                    }

                }

            }

        });

        info('Total jobs: ' . count($jobs));

        //  Check if this campaign has jobs to process otherwise stop the campaign
        if( count($jobs) == 0 ) return;

        $campaign = $this->campaign;

        info('$campaign');
        info($campaign);

        info('$this->campaignBatchJobsCount');
        info($this->campaignBatchJobsCount);

        //  Set the campaign name
        $campaignName = 'Campaign #' . ($this->campaignBatchJobsCount + 1);

        //  Create the batch to send
        $batch = Bus::batch($jobs
            )->then(function (Batch $batch) use ($campaign) {

                info('Job successful');

            })->catch(function (Batch $batch, Throwable $e) use ($campaign) {

                info('Job failed');

            })->finally(function (Batch $batch) use ($campaign) {

                info('All jobs completed');

            })->name($campaignName)->allowFailures()->dispatch();

        info('Batch id: ' . $batch->id);

        //  Send now
        if( false ) {

        //  Send later
        }elseif( false ) {

        //  Send recurring
        }elseif( false) {

        }

        //  Create a new campaign job record
        DB::table('campaign_job_batches')->insert([
            'campaign_id' => $campaign->id,
            'job_batch_id' => $batch->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

    }

    /**
     *  This function helps us to capture the qualified messages.
     *  These are the messages that we want to send to the
     *  subscribers
     */
    public function getQualifiedSendablesFromIdArray($ids, $selectedMessages = []) {

        //  Get the last id in the cascade of message ids
        $id = collect($ids)->last();

        info('Extract $id: ' . $id);

        /**
         *  Check if the item already exists in the list
         *  of previously qualified sendable messages.
         */
        $exists = collect($selectedMessages)->contains(function($selectedMessage) use ($id) {
            return $selectedMessage->id == $id;
        });

        info('Message exists: ' . ($exists ? 'Yes' : 'No'));

        //  If the message does not yet exist
        if( $exists == false ) {

            info('Get messages');

            //  Get the project message descendants that do not have nested children (Must be leaf messages i.e at the tips of the tree)
            $result = $this->project->messages()->whereDescendantOrSelf($id)->doesntHave('children')->get();

            info('Get messages type 1: ' .gettype($result));
            info('Get messages type 2: ' .gettype($result->all()));

            return $result;

        }

        //  Return nothing
        return collect([]);

    }

}
