<?php

namespace App\Console;

use App\Models\Project;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         *  We want to send subscribers with active subscriptions
         *  a message every day until their subscription ends.
         */
        try {

            $schedule->call(function () {

                info('$schedule->call');

                //  Get the projects
                $projects = Project::with('campaigns')->all();

                //  Foreach project
                foreach($projects as $project){

                    info('Project #: ' . $project->id);

                    //  Foreach campaign
                    foreach($project->campaigns as $campaign){

                        info('Campaign #: ' . $campaign->id);

                        /*******************************
                         *  GET THE SENDABLE MESSAGES  *
                         ******************************/

                        $messages = [];

                        //  If we have the message ids
                        if( is_array($campaign->message_id_cascade) ) {

                            /**
                             *  (1) Specific Message
                             *  ---------------------
                             *
                             *  If the content_to_send is "Specific Message" then the
                             *  message_id_cascade will contain one array with a list
                             *  of ids from the parent to the child message we want
                             *  to send e.g
                             *
                             *  [ 1, 10, 20, 30 ]
                             *
                             *  In the case above we want the message with id of 30
                             *  which is a descendant of message 1, 10, and 20
                             *
                             *  (1) Any Message
                             *  ---------------
                             *
                             *  If the content_to_send is "Any Message" then the
                             *  message_id_cascade will contain one array with a
                             *  list of arrays of ids from the parent to the
                             *  child message we want to send e.g
                             *
                             *  [ [1, 10, 20, 30], [1, 10, 20, 35], .... , .e.t.c ]
                             *
                             *  In the case above we want the message with id of 30
                             *  and message with id 35 which are both descendants
                             *  of message 1, 10, and 20
                             */

                            if( $campaign->content_to_send == 'Specific Message' ) {

                                //  Get the message ids, this is a single array of ids
                                $message_ids = $campaign->message_id_cascade;

                                //  Capture the qualified messages
                                array(
                                    $messages,
                                    ...getQualifiedSendablesFromIdArray('message', $project, $message_ids, $messages)
                                );

                            //  Get the message ids, this is a multiple array of ids
                            }elseif( $campaign->content_to_send == 'Any Message' ){

                                //  Extract the sendable messages
                                foreach($campaign->message_id_cascade as $message_ids){

                                    //  Capture the qualified messages
                                    array(
                                        $messages,
                                        ...getQualifiedSendablesFromIdArray($project, $message_ids, $messages)
                                    );

                                }
                            }

                        }

                        /**************************************************
                         *  FUNCTION TO GET SENDABLE MESSAGES / TOPICS    *
                         *************************************************/

                        /**
                         *  This function helps us to capture the qualified messages / messages
                         */
                        function getQualifiedSendablesFromIdArray($project, $ids, $selectedItems) {

                            //  Get the last id in the cascade of message ids
                            $id = collect($ids)->last();

                            /**
                             *  Check if the item already exists in the list
                             *  of previously qualified sendable items.
                             */
                            $exists = collect($selectedItems)->contains(function($selectedItem) use ($id) {
                                return $selectedItem->id == $id;
                            });

                            //  If the message does not yet exist
                            if( $exists == false ) {

                                //  Get the project message descendants that do not have nested children (Must be leaf messages i.e at the tips of the tree)
                                return $project->messages()->whereDescendantOrSelf($id)->doesntHave('children')->get();

                            }

                            //  Return nothing
                            return [];

                        }

                        /***************************************************
                         *  GET THE SUBSCRIBERS READY FOR THE NEXT MESSAGE *
                         **************************************************/

                         // Get the subscribers of this campaign (Those who have received or have not received content)
                        $subscribers = $campaign->subscribers()->with(['messages' => function($query) {
                            return $query->orderBy('sent_sms_count');
                        }]);

                        // Get the ids of subscribers of this campaign (Those who have received content before but are now ready for the next content send)
                        $subscriberIdsNotReadyForNextSms = $campaign->subscribersNotReadyForNextSms()->pluck('campaign_next_message_schedule.subscriber_id');

                        info('messages: ' . count($messages));
                        info('subscribers: ' . count($subscribers));
                        info('subscriberIdsNotReadyForNextSms: ' . count($subscriberIdsNotReadyForNextSms));

                        if(  $subscribers->count() && (!empty($messages) ) ) {

                            //  Only query 1000 subscribers at a time
                            $subscribers->chunk(1000, function ($chunked_subscribers) use ($subscriberIdsNotReadyForNextSms, $messages){

                                //  Foreach subscriber we retrieved from the query
                                foreach ($chunked_subscribers as $subscriber) {

                                    //  Check if the subscriber can receive the content sms
                                    $canSendSms = collect($subscriberIdsNotReadyForNextSms)->contains(function($subscriberId) use ($subscriber) {
                                        return ($subscriberId == $subscriber->id);
                                    }) == false;

                                    //  If we can send the content sms
                                    if( $canSendSms ) {

                                        //  Get the ids of the messages that have already been sent (We should avoid sending them)
                                        $sent_messages = collect($subscriber->messages)->pluck('id')->toArray();

                                        //  Get the selected message to send
                                        $message = collect($messages)->whereNotIn('id', $sent_messages)->first();

                                        //  If we have a message to send
                                        if( $message ) {

                                            //  Create a job to send this message to the subscriber
                                            //  \App\Jobs\SendSubscriptionSms::dispatch($subscriber, $message, $campaign);

                                        }else{

                                            info('Message not found');

                                        }

                                    }

                                }

                            });

                        }

                    }

                    info('---------------------');

                }

                info('--------------------------------------------------------------------------------');

            //  Start sending from 06:00 to 18:00
            })->name('send_subscriber_messages')->everySecond()/* ->between('6:00', '18:00') */->withoutOverlapping();

        } catch (\Exception $e) {

            //  Log::error('ERROR Updating company: '.$e->getMessage());

        }

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
