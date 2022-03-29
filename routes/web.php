<?php

use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TopicController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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

Route::get('/', function () {

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::prefix('/projects')->group(function () {

        //  Projects
        Route::get('/', [ProjectController::class, 'index'])->name('projects');
        Route::post('/', [ProjectController::class, 'create'])->name('create-project');

        Route::prefix('{project}')->group(function () {

            Route::put('/', [ProjectController::class, 'update'])->name('update-project');
            Route::delete('/', [ProjectController::class, 'delete'])->name('delete-project');

            //  Messages
            Route::prefix('messages')->group(function () {
                Route::get('/', [MessageController::class, 'index'])->name('messages');
                Route::post('/', [MessageController::class, 'create'])->name('create-message');
                Route::prefix('{message}')->group(function () {
                    Route::get('/', [MessageController::class, 'show'])->name('show-message');
                    Route::put('/', [MessageController::class, 'update'])->name('update-message');
                    Route::delete('/', [MessageController::class, 'delete'])->name('delete-message');
                });
            });

            //  Campaigns
            Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns');
            Route::post('/campaigns', [CampaignController::class, 'create'])->name('create-campaign');
            Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('update-campaign');
            Route::delete('/campaigns/{campaign}', [CampaignController::class, 'delete'])->name('delete-campaign');

            //  Subscribers
            Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers');
            Route::post('/subscribers', [SubscriberController::class, 'create'])->name('create-subscriber');
            Route::put('/subscribers/{subscriber}', [SubscriberController::class, 'update'])->name('update-subscriber');
            Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'delete'])->name('delete-subscriber');

            //  Subscriptions
            Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
            Route::post('/subscriptions', [SubscriptionController::class, 'create'])->name('create-subscription');
            Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('update-subscription');
            Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'delete'])->name('delete-subscription');

            //  Subscription Plans
            Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->name('subscription-plans');
            Route::post('/subscription-plans', [SubscriptionPlanController::class, 'create'])->name('create-subscription-plan');
            Route::put('/subscription-plans/{subscription_plan}', [SubscriptionPlanController::class, 'update'])->name('update-subscription-plan');
            Route::delete('/subscription-plans/{subscription_plan}', [SubscriptionPlanController::class, 'delete'])->name('delete-subscription-plan');

            //  Topics
            Route::prefix('topics')->group(function () {
                Route::get('/', [TopicController::class, 'index'])->name('topics');
                Route::post('/', [TopicController::class, 'create'])->name('create-topic');
                Route::prefix('{topic}')->group(function () {
                    Route::get('/', [TopicController::class, 'show'])->name('show-topic');
                    Route::put('/', [TopicController::class, 'update'])->name('update-topic');
                    Route::delete('/', [TopicController::class, 'delete'])->name('delete-topic');
                });
            });

        });

    });

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


