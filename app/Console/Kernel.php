<?php

namespace App\Console;

use App\Jobs\StartSmsCampaign;
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
         *
         *  IMPORTANT NOTE:
         *  ---------------
         *
         *  If the job queue appears to dispatch the jobs, but no
         *  jobs are being saved on the databse for processing
         *  then do the following:
         *
         *  (1) Stop running the queue service (i.e stop running the php artisan queue:work)
         *  (2) Run: php artisan config:clear && php artisan cache:clear
         *  (3) Run: php artisan queue:work
         *  (4) Run: php artisan schedule:work on local server
         *      but use cron jobs on production server
         *
         *  Make sure you have set the "QUEUE_CONNECTION=database" in the .env file
         *  Remember to clear the cache after changes to the .env file. Consider
         *  running the following commands to reset:
         *
         *  (1) php artisan config:cache
         *  (2) php artisan config:clear
         *  (3) php artisan cache:clear
         */
        try {

            $schedule->call(function () {

                info('schedule->call');

                //  Get the projects that have campaigns
                $projects = Project::has('campaigns')->with(['campaigns' => function($query) {

                    //  Get the total campaign batch jobs
                    return $query->withCount('campaignBatchJobs');

                }])->get();

                info('Total projects: '. count($projects));

                //  Foreach project
                foreach($projects as $project) {

                    info('hasSmsCredentials:'. ($project->hasSmsCredentials() ? 'Yes' : 'No'));

                    //  If this project does not have the sms credentials then do not run any campaigns
                    if( $project->hasSmsCredentials() == false ) return;

                    /**
                     *  Foreach campaign
                     *  @var Campaign $campaign
                     */
                    foreach($project->campaigns as $campaign) {

                        info('Campaign ID:'. $campaign->id);

                        /**
                         *  It appears that the eager loaded withCount('campaignBatchJobs')
                         *  is not accessible using $campaign->campaign_batch_jobs_count
                         *  within the StartSmsCampaign job. Therefore we will pass the
                         *  total as a separate parameter
                         */
                        StartSmsCampaign::dispatch($project, $campaign, $campaign->campaign_batch_jobs_count)->onConnection('database');

                    }

                }

            //  Start sending from 06:00 to 18:00
            })->name('Send subscriber campaign messages')->everySecond()/* ->between('6:00', '18:00') */->withoutOverlapping();

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
