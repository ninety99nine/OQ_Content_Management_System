<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Message;
use App\Models\Subscriber;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendSubscriptionSms implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subscriber;
    public $campaign;
    public $message;

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
        return $this->subscriber->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Subscriber $subscriber, Message $message, Campaign $campaign)
    {
        $this->subscriber = $subscriber;
        $this->campaign = $campaign;
        $this->message = $message;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //  Send the daily message to the subscriber
        try {

            //  Get the recipient mobile number
            $recipient = $this->subscriber->msisdn;

            //  Get the sms message
            $message = $this->message->content;

            //  Get connection configuration information
            $ip_address = config('app.sms_config.ip_address');
            $username = config('app.sms_config.username');
            $password = config('app.sms_config.password');
            $timeout = config('app.sms_config.timeout');
            $sender = config('app.sms_config.sender');
            $port = config('app.sms_config.port');

            /*
            (new \App\Services\SmsBuilder($sender, $ip_address, $port, $username, $password, $timeout))
                ->setRecipient($recipient, \smpp\SMPP::TON_INTERNATIONAL)
                ->sendMessage($message);
            */

            //  Record message sent
            DB::table('subscriber_messages')->insert([
                'project_id' => $this->message->project_id,
                'subscriber_id' => $this->subscriber->id,
                'message_id' => $this->message->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            //  Record the next message date and time
            DB::table('campaign_next_message_schedule')->insert([
                'next_message_date' => $this->campaign->nextMessageDate(),
                'project_id' => $this->message->project_id,
                'subscriber_id' => $this->subscriber->id,
                'campaign_id' => $this->campaign->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        } catch (\Throwable $th) {

            throw($th);

        } catch (\Exception $e) {

            throw($e);

        }
    }
}
