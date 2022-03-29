<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function index(Project $project)
    {

        $scheduleTypeOptions = Campaign::SCHEDULE_TYPE;
        $contentToSendOptions = Campaign::CONTENT_TO_SEND;

        //  Get the subscription plans
        $subscriptionPlans = $project->subscriptionPlans()->get();

        //  Get the campaigns
        $campaignsPayload = $project->campaigns()->with('subscriptionPlans:id')->latest()->paginate(10);

        //  Render the campaigns view
        return Inertia::render('Campaigns/List/Main', [
            'contentToSendOptions' => $contentToSendOptions,
            'scheduleTypeOptions' => $scheduleTypeOptions,
            'subscriptionPlans' => $subscriptionPlans,
            'campaignsPayload' => $campaignsPayload,
        ]);
    }

    public function create(Request $request, Project $project)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:5', 'max:50'],
            'description' => ['required', 'string', 'min:5', 'max:500'],
            'duration' => ['required'],
            'frequency' => ['required'],
            'start_date' => ['required'],
            'start_time' => ['required'],
            'end_date' => ['required'],
            'end_time' => ['required'],
            'days_of_the_week' => ['required'],
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set description
        $description = $request->input('description');

        //  Set schedule type
        $schedule_type = $request->input('schedule_type');

        //  Set duration
        $duration = $request->input('duration');

        //  Set frequency
        $frequency = $request->input('frequency');

        //  Set start date
        $start_date = $request->input('start_date');

        //  Set start time
        $start_time = $request->input('start_time');

        //  Set end date
        $end_date = $request->input('end_date');

        //  Set end time
        $end_time = $request->input('end_time');

        //  Set days of the week
        $days_of_the_week = $request->input('days_of_the_week');

        //  Set subcription plan ids
        $subcription_plan_ids = $request->input('subcription_plan_ids');

        //  Create new campaign
        $campaign = Campaign::create([
            'name' => $name,
            'end_date' => $end_date,
            'end_time' => $end_time,
            'duration' => $duration,
            'frequency' => $frequency,
            'start_date' => $start_date,
            'start_time' => $start_time,
            'project_id' => $project->id,
            'description' => $description,
            'schedule_type' => $schedule_type,
            'has_end_date' => !empty($end_date),
            'has_start_date' => !empty($start_date),
            'days_of_the_week' => $days_of_the_week,
        ]);

        //  Sync the subscription plans
        $campaign->subscriptionPlans()->syncWithPivotValues($subcription_plan_ids, ['project_id' => $project->id]);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function update(Request $request, Project $project, Campaign $campaign)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:5', 'max:50'],
            'description' => ['required', 'string', 'min:5', 'max:500'],
            'duration' => ['required'],
            'frequency' => ['required'],
            'start_date' => ['required'],
            'start_time' => ['required'],
            'end_date' => ['required'],
            'end_time' => ['required'],
            'days_of_the_week' => ['required'],
        ])->validate();

        //  Set name
        $name = $request->input('name');

        //  Set description
        $description = $request->input('description');

        //  Set schedule type
        $schedule_type = $request->input('schedule_type');

        //  Set duration
        $duration = $request->input('duration');

        //  Set frequency
        $frequency = $request->input('frequency');

        //  Set start date
        $start_date = $request->input('start_date');

        //  Set start time
        $start_time = $request->input('start_time');

        //  Set end date
        $end_date = $request->input('end_date');

        //  Set end time
        $end_time = $request->input('end_time');

        //  Set days of the week
        $days_of_the_week = $request->input('days_of_the_week');

        //  Set subcription plan ids
        $subcription_plan_ids = $request->input('subcription_plan_ids');

        //  Update campaign
        $campaign->update([
            'name' => $name,
            'end_date' => $end_date,
            'end_time' => $end_time,
            'duration' => $duration,
            'frequency' => $frequency,
            'start_date' => $start_date,
            'start_time' => $start_time,
            'project_id' => $project->id,
            'description' => $description,
            'schedule_type' => $schedule_type,
            'has_end_date' => !empty($end_date),
            'has_start_date' => !empty($start_date),
            'days_of_the_week' => $days_of_the_week,
        ]);

        //  Sync the subscription plans
        $campaign->subscriptionPlans()->syncWithPivotValues($subcription_plan_ids, ['project_id' => $project->id]);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function delete(Project $project, Campaign $campaign)
    {
        //  Delete campaign
        $campaign->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
