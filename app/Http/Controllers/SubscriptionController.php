<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Language;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function index(Project $project){

        if( $project ){

            //  Get the subscription plans
            $subscriptionPlans = $project->subscriptionPlans()->get();

            //  Count the subscribers
            $totalSubscribers = $project->subscribers()->count();

            //  Get the subscriptions
            $subscriptions = $project->subscriptions()->with('subscriber:id,msisdn', 'subscriptionPlan:id,name')->latest()->paginate(10);

            //  Render the subscriptions view
            return Inertia::render('Subscriptions/List/Main', [
                'totalSubscribers' => $totalSubscribers,
                'subscriptionsPayload' => $subscriptions,
                'subscriptionPlans' => $subscriptionPlans
            ]);

        }

    }

    public function create(Request $request, Project $project){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'msisdn' => ['required', 'string', 'min:11', Rule::exists('subscribers')->where(function ($query) use ($request, $project) {

                    //  Make sure that this project has this subscriber msisdn
                    return $query->where('msisdn', $request->input('msisdn'))->where('project_id', $project->id);

                })],
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

            //  Get the subscriber
            $subscriber = Subscriber::where('msisdn', $msisdn)->first();

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
            Subscription::create([
                'subscription_plan_id' => $subscription_plan_id,
                'subscriber_id' => $subscriber->id,
                'project_id' => $project->id,
                'start_at' => $start_at,
                'end_at' => $end_at
            ]);

            return redirect()->back()->with('message', 'Created Successfully');

        }

    }

    public function update(Request $request, Project $project, $subscription_id){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'msisdn' => ['required', 'string', 'min:11', Rule::exists('subscribers')->where(function ($query) use ($request, $project) {

                    //  Make sure that this project has this subscriber msisdn
                    return $query->where('msisdn', $request->input('msisdn'))->where('project_id', $project->id);

                })],
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

            //  Get the subscriber
            $subscriber = Subscriber::where('msisdn', $msisdn)->first();

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

            //  Update subscription
            Subscription::findOrFail($subscription_id)->update([
                'subscription_plan_id' => $subscription_plan_id,
                'subscriber_id' => $subscriber->id,
                'project_id' => $project->id,
                'start_at' => $start_at,
                'end_at' => $end_at
            ]);

            return redirect()->back()->with('message', 'Updated Successfully');

        }
    }

    public function delete(Project $project, $subscription_id){

        if( $project ){

            //  Delete message
            Subscription::findOrFail($subscription_id)->delete();

            return redirect()->back()->with('message', 'Deleted Successfully');

        }

    }
}
