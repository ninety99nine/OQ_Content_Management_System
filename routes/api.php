<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SubscriptionPlanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('projects/{project}/topics', [TopicController::class, 'get'])->name('api.topics');
Route::get('projects/{project}/topics/{topic}', [TopicController::class, 'show'])->name('api.topic');
Route::get('projects/{project}/topics/{topic}/subtopics', [TopicController::class, 'get'])->name('api.subtopics');

Route::post('projects/{project}/subscriptions', [SubscriptionController::class, 'create'])->name('api.create.subscription');

Route::get('projects/{project}/subscription-plans', [SubscriptionPlanController::class, 'get'])->name('api.subscription_plans');
