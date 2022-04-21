<?php

use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Jobs\StartSmsCampaign;
use App\Models\Campaign;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login', 301);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::prefix('/projects')->group(function () {

        //  Projects
        Route::get('/', [ProjectController::class, 'index'])->name('projects');
        Route::post('/', [ProjectController::class, 'create'])->name('create-project');

        Route::prefix('{project}')->group(function () {

            //  Project
            Route::middleware(['project.permission:Manage project settings'])->group(function () {
                Route::get('/', [ProjectController::class, 'show'])
                        ->withoutMiddleware(['project.permission:Manage project settings'])
                        ->middleware(['project.permission:View project settings'])
                        ->name('show-project');
                Route::put('/', [ProjectController::class, 'update'])->name('update-project');
                Route::delete('/', [ProjectController::class, 'delete'])->name('delete-project');
            });

            //  Users
            Route::prefix('users')->group(function () {
                Route::get('/', [UserController::class, 'index'])->middleware(['project.permission:View users'])->name('users');
                Route::post('/', [UserController::class, 'create'])->middleware(['project.permission:Manage users'])->name('create-user');

                Route::prefix('{user}')->middleware(['project.permission:Manage users'])->group(function () {
                    //  Route::get('/', [UserController::class, 'show'])->name('show-user');
                    Route::put('/', [UserController::class, 'update'])->name('update-user');
                    Route::delete('/', [UserController::class, 'delete'])->name('delete-user');
                });
            });

            //  Messages
            Route::prefix('messages')->group(function () {
                Route::get('/', [MessageController::class, 'index'])->middleware(['project.permission:View messages'])->name('messages');
                Route::post('/', [MessageController::class, 'create'])->middleware(['project.permission:Manage messages'])->name('create-message');

                Route::prefix('{message}')->middleware(['project.permission:Manage messages'])->group(function () {
                    Route::get('/', [MessageController::class, 'show'])
                        ->withoutMiddleware(['project.permission:Manage messages'])
                        ->middleware(['project.permission:View messages'])
                        ->name('show-message');
                    Route::put('/', [MessageController::class, 'update'])->name('update-message');
                    Route::delete('/', [MessageController::class, 'delete'])->name('delete-message');
                });
            });

            //  Campaigns
            Route::prefix('campaigns')->group(function () {
                Route::get('/', [CampaignController::class, 'index'])->middleware(['project.permission:View campaigns'])->name('campaigns');
                Route::post('/', [CampaignController::class, 'create'])->middleware(['project.permission:Manage campaigns'])->name('create-campaign');

                Route::prefix('{campaign}')->middleware(['project.permission:Manage campaigns'])->group(function () {
                    Route::get('/', [CampaignController::class, 'show'])->name('show-campaign');
                    Route::put('/', [CampaignController::class, 'update'])->name('update-campaign');
                    Route::delete('/', [CampaignController::class, 'delete'])->name('delete-campaign');
                    Route::get('/job-batches', [CampaignController::class, 'jobBatches'])
                            ->withoutMiddleware(['project.permission:Manage campaigns'])
                            ->middleware(['project.permission:View campaigns'])
                            ->name('show-campaign-job-batches');
                });
            });

            //  Subscribers
            Route::prefix('subscribers')->group(function () {
                Route::get('/', [SubscriberController::class, 'index'])->middleware(['project.permission:View subscribers'])->name('subscribers');
                Route::post('/', [SubscriberController::class, 'create'])->middleware(['project.permission:Manage subscribers'])->name('create-subscriber');

                Route::prefix('{subscriber}')->middleware(['project.permission:Manage subscribers'])->group(function () {
                    Route::put('/', [SubscriberController::class, 'update'])->name('update-subscriber');
                    Route::delete('/', [SubscriberController::class, 'delete'])->name('delete-subscriber');
                });
            });

            //  Subscriptions
            Route::prefix('subscriptions')->group(function () {
                Route::get('/', [SubscriptionController::class, 'index'])->middleware(['project.permission:View subscriptions'])->name('subscriptions');
                Route::post('/', [SubscriptionController::class, 'create'])->middleware(['project.permission:Manage subscriptions'])->name('create-subscription');

                Route::prefix('{subscription}')->middleware(['project.permission:Manage subscriptions'])->group(function () {
                    Route::put('/', [SubscriptionController::class, 'update'])->name('update-subscription');
                    Route::delete('/', [SubscriptionController::class, 'delete'])->name('delete-subscription');
                });
            });

            //  Subscription Plans
            Route::prefix('subscription-plans')->group(function () {
                Route::get('/', [SubscriptionPlanController::class, 'index'])->middleware(['project.permission:View subscription plans'])->name('subscription-plans');
                Route::post('/', [SubscriptionPlanController::class, 'create'])->middleware(['project.permission:Manage subscription plans'])->name('create-subscription-plan');

                Route::prefix('{subscription_plan}')->middleware(['project.permission:Manage subscription plans'])->group(function () {
                    Route::put('/{subscription_plan}', [SubscriptionPlanController::class, 'update'])->name('update-subscription-plan');
                    Route::delete('/{subscription_plan}', [SubscriptionPlanController::class, 'delete'])->name('delete-subscription-plan');
                });
            });

            //  Topics
            Route::prefix('topics')->group(function () {
                Route::get('/', [TopicController::class, 'index'])->middleware(['project.permission:View topics'])->name('topics');
                Route::post('/', [TopicController::class, 'create'])->middleware(['project.permission:Manage topics'])->name('create-topic');

                Route::prefix('{topic}')->middleware(['project.permission:Manage topics'])->group(function () {
                    Route::get('/', [TopicController::class, 'show'])
                        ->withoutMiddleware(['project.permission:Manage topics'])
                        ->middleware(['project.permission:View topics'])
                        ->name('show-topic');
                    Route::put('/', [TopicController::class, 'update'])->name('update-topic');
                    Route::delete('/', [TopicController::class, 'delete'])->name('delete-topic');
                });
            });

        });

    });

});


Route::get('testing', function() {

    //  return Campaign::find(1)->canStartSmsCampaign();
    //  return Campaign::find(1)->nextCampaignSmsMessageDate();

    //  Get the projects
    $projects = \App\Models\Project::with('campaigns')->get();

    //  Foreach project
    foreach($projects as $project) {

        /**
         *  Foreach campaign
         *  @var Campaign $campaign
         */
        foreach($project->campaigns as $campaign) {

            StartSmsCampaign::dispatch($project, $campaign);

        }

    }

});

Route::get('/upload', function(){

    dd(\Carbon\Carbon::now()->englishDayOfWeek);

    $projectId = 3;

    //  English Parent topic
    $englishLanguage = \App\Models\Topic::create([
        'title' => 'English',
        'project_id' => $projectId
    ]);

    //  Setswana Parent topic
    $setswanaLanguage = \App\Models\Topic::create([
        'title' => 'Setswana',
        'project_id' => $projectId
    ]);

    /************************
     *  SELF HELP TOPICS    *
     ***********************/

    $template = [];

    //  English Parent topic
    $englishRootSelfHelpTopic = \App\Models\Topic::create([
        'title' => 'Self-Help Tips',
        'project_id' => $projectId
    ]);

    //  Assign to language group
    $englishLanguage->prependNode($englishRootSelfHelpTopic);

    //  Setswana Parent topic
    $setswanaRootSelfHelpTopic = \App\Models\Topic::create([
        'title' => 'Self-Help Tips',
        'project_id' => $projectId
    ]);

    //  Assign to language group
    $setswanaLanguage->prependNode($setswanaRootSelfHelpTopic);




    //  Get file
    $selfHelpTopicsFile = file(storage_path('app').'/selfHelpTopics.csv');
    $selfHelpTopics = array_slice(array_map(fn($input) => json_encode(str_getcsv($input)), $selfHelpTopicsFile), 1);

    //  Clean fields
    foreach($selfHelpTopics as $key => $selfHelpTopic){
        $selfHelpTopics[$key] = json_decode(str_replace('\u202f', '', $selfHelpTopic));

        foreach($selfHelpTopics[$key] as $y => $selfHelpTopicString){
            $selfHelpTopics[$key][$y] = trim(preg_replace('/\s\s+/', ' ', $selfHelpTopicString));
        }
    }

    //  Create topics
    foreach($selfHelpTopics as $key => $selfHelpTopic){

        $template['title'] = trim($selfHelpTopic[0]);
        $template['content'] = trim($selfHelpTopic[1]);
        $template['project_id'] = $projectId;

        $selfHelpTopicModel = \App\Models\Topic::create($template);
        $selfHelpTopics[$key] = $selfHelpTopicModel;

        //  Set the English parent
        if( trim($selfHelpTopic[2]) == 'English' ){

            $englishRootSelfHelpTopic->prependNode($selfHelpTopicModel);

        //  Set the Setswana parent
        }else{

            $setswanaRootSelfHelpTopic->prependNode($selfHelpTopicModel);

        }
    }

    /***************************
     *  GET EDUCATED TOPICS    *
     **************************/

    $template = [];

    //  English Parent topic
    $englishRootGetEducatedTopic = \App\Models\Topic::create([
        'title' => 'Get Educated',
        'project_id' => $projectId
    ]);

    //  Assign to language group
    $englishLanguage->prependNode($englishRootGetEducatedTopic);

    //  Setswana Parent topic
    $setswanaRootGetEducatedTopic = \App\Models\Topic::create([
        'title' => 'Get Educated',
        'project_id' => $projectId
    ]);

    //  Assign to language group
    $setswanaLanguage->prependNode($setswanaRootGetEducatedTopic);







    $getEducatedTopicsFile = file(storage_path('app').'/getEducatedTopics.csv');
    $getEducatedTopics = array_slice(array_map(fn($input) => json_encode(str_getcsv($input)), $getEducatedTopicsFile), 1);

    foreach($getEducatedTopics as $key => $getEducatedTopic){
        $getEducatedTopics[$key] = json_decode(str_replace('\u202f', '', $getEducatedTopic));

        foreach($getEducatedTopics[$key] as $y => $getEducatedTopicString){
            $getEducatedTopics[$key][$y] = trim(preg_replace('/\s\s+/', ' ', $getEducatedTopicString));
        }
    }

    foreach($getEducatedTopics as $key => $getEducatedTopic){
        $template['title'] = trim($getEducatedTopic[0]);
        $template['project_id'] = $projectId;

        $getEducatedTopicModel = \App\Models\Topic::create($template);
        $getEducatedTopics[$key] = $getEducatedTopicModel;

        //  Set the English parent
        if( trim($getEducatedTopic[3]) == 'English' ){

            $englishRootGetEducatedTopic->prependNode($getEducatedTopicModel);

        //  Set the Setswana parent
        }else{

            $setswanaRootGetEducatedTopic->prependNode($getEducatedTopicModel);

        }
    }

    /*******************************
     *  GET EDUCATED SUB TOPICS    *
     ******************************/

    //  Get Educated Sub-topics
    $getEducatedSubTopicsFile = file(storage_path('app').'/getEducatedSubTopics.csv');
    $getEducatedSubTopics = array_slice(array_map(fn($input) => json_encode(str_getcsv($input)), $getEducatedSubTopicsFile), 1);

    foreach($getEducatedSubTopics as $key => $getEducatedSubTopic){
        $getEducatedSubTopics[$key] = json_decode(str_replace('\u202f', '', $getEducatedSubTopic));

        foreach($getEducatedSubTopics[$key] as $y => $getEducatedSubTopicString){
            $getEducatedSubTopics[$key][$y] = trim(preg_replace('/\s\s+/', ' ', $getEducatedSubTopicString));
        }
    }

    $template = [];

    foreach($getEducatedSubTopics as $key => $getEducatedSubTopic){
        $template['title'] = trim($getEducatedSubTopic[0]);
        $template['content'] = trim($getEducatedSubTopic[1]);
        $template['project_id'] = $projectId;

        $getEducatedSubTopicModel = \App\Models\Topic::create($template);

        $getEducatedSubTopics[$key] = $getEducatedSubTopicModel;

        //  Set the parent Topic
        $parentTopic = collect($getEducatedTopics)->filter(function($getEducatedTopic) use ($getEducatedSubTopic) {
            return strtolower(trim($getEducatedTopic->title)) == strtolower(trim($getEducatedSubTopic[2]));
        })->first();

        if( $parentTopic ){

            $parentTopic->prependNode($getEducatedSubTopicModel);

        }
    }

    /***************************
     *  DAILY QUOTES           *
     **************************/

    //  English Parent topic
    $englishLanguage = \App\Models\Message::create([
        'content' => 'English',
        'project_id' => $projectId
    ]);

    //  Setswana Parent topic
    $setswanaLanguage = \App\Models\Message::create([
        'content' => 'Setswana',
        'project_id' => $projectId
    ]);


    //  Get file
    $dailyQuotesFile = file(storage_path('app').'/dailyQuotes.csv');
    $dailyQuotes = array_slice(array_map(fn($input) => json_encode(str_getcsv($input)), $dailyQuotesFile), 1);

    //  Clean fields
    foreach($dailyQuotes as $key => $dailyQuote){
        $dailyQuotes[$key] = json_decode(str_replace('\u202f', '', $dailyQuote));

        foreach($dailyQuotes[$key] as $y => $dailyQuoteString){
            $dailyQuotes[$key][$y] = trim(preg_replace('/\s\s+/', ' ', $dailyQuoteString));
        }
    }

    //  Create Messages
    foreach($dailyQuotes as $key => $dailyQuote){

        $template['content'] = trim($dailyQuote[1]);
        $template['project_id'] = $projectId;

        $dailyQuoteModel = \App\Models\Message::create($template);
        $dailyQuotes[$key] = $dailyQuoteModel;

        //  Set the English parent
        if( trim($dailyQuote[2]) == 'English' ){

            $englishLanguage->prependNode($dailyQuoteModel);

        //  Set the Setswana parent
        }else{

            $setswanaLanguage->prependNode($dailyQuoteModel);

        }
    }

    return 'DONE!';

});


