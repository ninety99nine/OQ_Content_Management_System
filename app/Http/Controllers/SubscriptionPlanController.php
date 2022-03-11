<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Validator;

class SubscriptionPlanController extends Controller
{
    public function index(Project $project){

        if( $project ){

            //  Get the subscriptions
            $totalSubscriptions = $project->subscriptions()->count();

            //  Get the subscription plans
            $subscriptionPlans = $project->subscriptionPlans()->latest()->withCount('subscriptions')->paginate(10);

            //  Render the subscription plans view
            return Inertia::render('SubscriptionPlans/List/Main', [
                'totalSubscriptions' => $totalSubscriptions,
                'subscriptionPlansPayload' => $subscriptionPlans
            ]);

        }

    }

    public function create(Request $request, Project $project){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:3', 'max:20', Rule::unique('subscription_plans')->where(function ($query) use ($request, $project) {

                    //  Make sure that this project does not already have this language
                    return $query->where('name', $request->input('name'))->where('project_id', $project->id);

                })],
                'price' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'frequency' => ['required', 'string'],
                'duration' => ['required', 'integer']
            ])->validate();

            //  Set name
            $name = $request->input('name');

            //  Set price
            $price = $request->input('price');

            //  Set frequency
            $duration = $request->input('duration');

            //  Set frequency
            $frequency = $request->input('frequency');

            //  Create new subscription plan
            SubscriptionPlan::create([
                'name' => $name,
                'price' => $price,
                'duration' => $duration,
                'frequency' => $frequency,
                'project_id' => $project->id,
            ]);

            return redirect()->back()->with('message', 'Created Successfully');

        }

    }

    public function update(Request $request, Project $project, $subscription_plan_id){

        if( $project ){

            //  Validate the request inputs
            Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:3', 'max:20', Rule::unique('subscription_plans')->where(function ($query) use ($request, $project) {

                    //  Make sure that this project does not already have this subscription plan name
                    return $query->where('name', $request->input('name'))->where('project_id', $project->id);

                })],
                'price' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'frequency' => ['required', 'string'],
                'duration' => ['required', 'integer']
            ])->validate();

            //  Set name
            $name = $request->input('name');

            //  Set price
            $price = $request->input('price');

            //  Set frequency
            $duration = $request->input('duration');

            //  Set frequency
            $frequency = $request->input('frequency');

            //  Update existing subscription plan
            SubscriptionPlan::findOrFail($subscription_plan_id)->update([
                'name' => $name,
                'price' => $price,
                'duration' => $duration,
                'frequency' => $frequency,
                'project_id' => $project->id,
            ]);

            return redirect()->back()->with('message', 'Updated Successfully');

        }

    }

    public function delete(Project $project, $subscription_plan_id){

        if( $project ){

            //  Delete subscription plan
            SubscriptionPlan::findOrFail($subscription_plan_id)->delete();

            return redirect()->back()->with('message', 'Deleted Successfully');

        }

    }
}
