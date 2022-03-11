<?php

use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\LanguageController;
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
            Route::get('/messages', [MessageController::class, 'index'])->name('messages');
            Route::post('/messages', [MessageController::class, 'create'])->name('create-message');
            Route::put('/messages/{message_id}', [MessageController::class, 'update'])->name('update-message');
            Route::delete('/messages/{message_id}', [MessageController::class, 'delete'])->name('delete-message');

            //  Campaigns
            Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns');
            Route::post('/campaigns', [CampaignController::class, 'create'])->name('create-campaign');
            Route::put('/campaigns/{campaign_id}', [CampaignController::class, 'update'])->name('update-campaign');
            Route::delete('/campaigns/{campaign_id}', [CampaignController::class, 'delete'])->name('delete-campaign');

            //  Subscribers
            Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers');
            Route::post('/subscribers', [SubscriberController::class, 'create'])->name('create-subscriber');
            Route::put('/subscribers/{subscriber_id}', [SubscriberController::class, 'update'])->name('update-subscriber');
            Route::delete('/subscribers/{subscriber_id}', [SubscriberController::class, 'delete'])->name('delete-subscriber');

            //  Subscriptions
            Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
            Route::post('/subscriptions', [SubscriptionController::class, 'create'])->name('create-subscription');
            Route::put('/subscriptions/{subscription_id}', [SubscriptionController::class, 'update'])->name('update-subscription');
            Route::delete('/subscriptions/{subscription_id}', [SubscriptionController::class, 'delete'])->name('delete-subscription');

            //  Subscription Plans
            Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->name('subscription-plans');
            Route::post('/subscription-plans', [SubscriptionPlanController::class, 'create'])->name('create-subscription-plan');
            Route::put('/subscription-plans/{subscription_plan_id}', [SubscriptionPlanController::class, 'update'])->name('update-subscription-plan');
            Route::delete('/subscription-plans/{subscription_plan_id}', [SubscriptionPlanController::class, 'delete'])->name('delete-subscription-plan');

            //  Languages
            Route::get('/languages', [LanguageController::class, 'index'])->name('languages');
            Route::post('/languages', [LanguageController::class, 'create'])->name('create-language');
            Route::put('/languages/{language_id}', [LanguageController::class, 'update'])->name('update-language');
            Route::delete('/languages/{language_id}', [LanguageController::class, 'delete'])->name('delete-language');

            //  Topics
            Route::post('/topics', [TopicController::class, 'create'])->name('create-topic');
            Route::get('/topics/{topic?}', [TopicController::class, 'index'])->name('topics');

            Route::prefix('{topic_id}')->group(function () {
                Route::put('/', [TopicController::class, 'update'])->name('update-topic');
                Route::delete('/', [TopicController::class, 'delete'])->name('delete-topic');
                Route::delete('/sub-topics', [TopicController::class, 'getEducatedSubTopics'])->name('sub-topics');

            });

        });

    });

});




Route::get('/upload', function(){

    $projectId = 3;
    $languages = \App\Models\Language::all();

    /************************
     *  SELF HELP TOPICS    *
     ***********************/

    $template = [];

    //  English Parent topic
    $englishRootSelfHelpTopic = \App\Models\Topic::create([
        'title' => 'Self-Help Tips (English)',
        'project_id' => $projectId,
        'language_id' => collect($languages)->filter(function($language) {
            return $language->name == 'English';
        })->first()->id
    ]);

    //  Setswana Parent topic
    $setswanaRootSelfHelpTopic = \App\Models\Topic::create([
        'title' => 'Self-Help Tips (Setswana)',
        'project_id' => $projectId,
        'language_id' => collect($languages)->filter(function($language) {
            return $language->name == 'Setswana';
        })->first()->id
    ]);

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
        $template['parent_topic_id'] = trim($selfHelpTopic[2]) == 'English' ? $englishRootSelfHelpTopic->id : $setswanaRootSelfHelpTopic->id;
        $template['language_id'] = collect($languages)->filter(function($language) use ($selfHelpTopic) {
            return $language->name == $selfHelpTopic[2];
        })->first()->id;

        $selfHelpTopic = \App\Models\Topic::create($template);
        $selfHelpTopics[$key] = $selfHelpTopic;
    }

    /***************************
     *  GET EDUCATED TOPICS    *
     **************************/

    $template = [];

    //  English Parent topic
    $englishRootGetEducatedTopic = \App\Models\Topic::create([
        'title' => 'Get Educated (English)',
        'project_id' => $projectId,
        'language_id' => collect($languages)->filter(function($language) {
            return $language->name == 'English';
        })->first()->id
    ]);

    //  Setswana Parent topic
    $setswanaRootGetEducatedTopic = \App\Models\Topic::create([
        'title' => 'Get Educated (Setswana)',
        'project_id' => $projectId,
        'language_id' => collect($languages)->filter(function($language) {
            return $language->name == 'Setswana';
        })->first()->id
    ]);

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
        $template['parent_topic_id'] = trim($getEducatedTopic[3]) == 'English' ? $englishRootGetEducatedTopic->id : $setswanaRootGetEducatedTopic->id;
        $template['language_id'] = collect($languages)->filter(function($language) use ($getEducatedTopic) {
            return $language->name == $getEducatedTopic[3];
        })->first()->id;

        $getEducatedTopic = \App\Models\Topic::create($template);
        $getEducatedTopics[$key] = $getEducatedTopic;
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
        $template['parent_topic_id'] = collect($getEducatedTopics)->filter(function($getEducatedTopic) use ($getEducatedSubTopic) {
            return strtolower($getEducatedTopic->title) == strtolower(trim($getEducatedSubTopic[2]));
        })->first()->id;
        $template['language_id'] = collect($languages)->filter(function($language) use ($getEducatedSubTopic) {
            return $language->name == $getEducatedSubTopic[3];
        })->first()->id;

        $getEducatedSubTopic = \App\Models\Topic::create($template);

        $getEducatedSubTopics[$key] = $getEducatedSubTopic;

    }

    return $getEducatedSubTopics;

});


