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



                    }









                    //  Get the messages
                    $messages = $project->messages()->get();

                    //  Get subscribers that has active subscriptions (Also pull the past received message ids)
                    $subscribers = $project->subscribers()->hasActiveSubscription()->hasNotReceivedMessage()->with('messages:id')->oldest('created_at')->limit(10000);

                    if( !empty($messages) && $subscribers->count() ){

                        //  Only query 1000 subscribers at a time
                        $subscribers->chunk(1000, function ($chunked_subscribers) use ($messages){

                            //  Foreach subscriber we retrieved from the query
                            foreach ($chunked_subscribers as $subscriber) {

                                //  If we have a subscriber
                                if( $subscriber ){

                                    //  Get the ids of the messages that have already been sent (We should avoid sending them)
                                    $sent_messages = collect($subscriber->messages)->pluck('id')->toArray();

                                    //  Get the selected message to send
                                    $message = collect($messages)->whereNotIn('id', $sent_messages)->first();

                                    //  If we have a message to send
                                    if($message){

                                        //  Create a job to send this message to the subscriber
                                        \App\Jobs\SendSubscriptionSms::dispatch($subscriber, $message);

                                    }else{

                                        info('Message not found');

                                    }

                                }

                            }

                        });

                    }

                    info('---------------------');

                }

                info('--------------------------------------------------------------------------------');

            //  Start sending from 06:00 to 18:00
            })->name('send_subscriber_messages')->everySecond()->between('6:00', '18:00')->withoutOverlapping();

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
