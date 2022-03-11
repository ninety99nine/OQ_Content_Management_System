<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{

    public function create(Request $request, Project $project){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'msisdn' => ['required', 'string', 'min:11'],
                'subscription_plan_id' => ['required', 'integer', 'min:1', 'exists:subscription_plans,id'],
            ], [
                'msisdn.required' => 'The mobile number is required.',
                'msisdn.min' => 'The mobile number must be at least 11 characters',
                'msisdn.exists' => 'The subscriber using the mobile number does not exist.',
                'msisdn.string' => 'Enter a valid mobile number and extension e.g 26772000001.',
            ])->validate();

            //  Set msisdn
            $msisdn = $request->input('msisdn');

            //  Set subscription plan id
            $subscription_plan_id = $request->input('subscription_plan_id');

            //  Get / Create the subscriber
            $subscriber = Subscriber::firstOrCreate(
                ['msisdn' => $msisdn],
                ['msisdn' => $msisdn, 'project_id' => $project->id]
            );

            //  Get the subscription plan
            $subscriptionPlan = SubscriptionPlan::find($subscription_plan_id);

            //  Set the start date
            $start_at = Carbon::now();

            //  Set the end date
            if( $subscriptionPlan->frequency == 'Years' ){

                $end_at = Carbon::now()->addYears( $subscriptionPlan->duration );

            }elseif( $subscriptionPlan->frequency == 'Months' ){

                $end_at = Carbon::now()->addMonths( $subscriptionPlan->duration );

            }elseif( $subscriptionPlan->frequency == 'Weeks' ){

                $end_at = Carbon::now()->addWeeks( $subscriptionPlan->duration );

            }elseif( $subscriptionPlan->frequency == 'Days' ){

                $end_at = Carbon::now()->addDays( $subscriptionPlan->duration );

            }else{

                $end_at = Carbon::now()->addDay();

            }

            //  Create new subscription
            $subscription = Subscription::create([
                'subscription_plan_id' => $subscription_plan_id,
                'subscriber_id' => $subscriber->id,
                'project_id' => $project->id,
                'start_at' => $start_at,
                'end_at' => $end_at
            ]);

            return response()->json($subscription, 201);

        }

        return response()->json(['message' => 'Project does not exist'], 400);

    }
}
