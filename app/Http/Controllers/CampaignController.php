<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\JobBatches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function index(Project $project)
    {
        $scheduleTypeOptions = Campaign::SCHEDULE_TYPE;
        $contentToSendOptions = Campaign::MESSAGE_TO_SEND;

        //  Get the subscription plans
        $subscriptionPlans = $project->subscriptionPlans()->get();

        //  Get the campaigns
        $campaignsPayload = $project->campaigns()->with(['subscriptionPlans:id', 'latestCampaignBatchJob' => function($query) {

            //  Seleted columns
            $selectedColumns = collect(Schema::getColumnListing('job_batches'))->reject(function ($name) {

                //  Exclude the following columns
                return in_array($name, ['options', 'failed_job_ids']);

            })->map(function ($name) {

                /**
                 *  Append the table name to each column to avoid clashing of ambiguous fields
                 *  e.g id, created_at, e.t.c
                 */
                return 'job_batches.'.$name;

            })->all();

            //  Limit the loaded message to the message id and sent sms count to consume less memory
            return $query->select(...$selectedColumns);

        }])->withCount('campaignBatchJobs')->latest()->paginate(10);

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
        /**
         *  Check whether or not the combination of the start date and start time
         *  produce a datetime that is in the future otherwise throw a validation
         *  error. The same validation rule is used for the "start_date" and the
         *  "start_time" fields.
         */
        $startDateTimeValidation = function ($attribute, $value, $fail) {

            if( ($date = request()->input('start_date') ) && ($time = request()->input('start_time') ) ) {

                $startDateTime = (new Campaign)->getCampaignStartDateTime($date, $time);

                if( $startDateTime->isFuture() ) {

                    return;

                }

            }

            $fail('The '.$attribute.' must be in the future.');

        };

        /**
         *  Check whether or not the combination of the end date and end time
         *  produce a datetime that is in the future of the given start date
         *  and end date otherwise throw a validation error. The same
         *  validation rule is used for the "end_date" and the
         *  "end_time" fields.
         */
        $endDateTimeValidation = function ($attribute, $value, $fail) {

            if( ($startDate = request()->input('start_date') ) && ($startTime = request()->input('start_time') ) &&
                ($endDate = request()->input('end_date') ) && ($endTime = request()->input('end_time') ) ) {

                $startDateTime = (new Campaign)->getCampaignStartDateTime($startDate, $startTime);
                $endDateTime = (new Campaign)->getCampaignStartDateTime($endDate, $endTime);

                if( $endDateTime->greaterThan($startDateTime) ) {

                    return;

                }

            }
            if( $attribute == 'end_date' ) {

                $fail('The '.$attribute.' must be a date after the start date');

            }else{

                $fail('The '.$attribute.' must be a time after the start date');

            }

        };

        //  Validate the request inputs
        $data = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:5', 'max:50'],
            'description' => ['required', 'string', 'min:5', 'max:500'],
            'schedule_type' => ['required', 'string', Rule::in(Campaign::SCHEDULE_TYPE)],
            'recurring_duration' => [
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
                'exclude_unless:schedule_type,Send Recurring'
            ],
            'recurring_frequency' => [
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
                'exclude_unless:schedule_type,Send Recurring'
            ],
            'start_date' => [
                'date', $startDateTimeValidation, 'exclude_if:schedule_type,Send Now',
                Rule::requiredIf(in_array($request->input('schedule_type'), ['Send Later', 'Send Recurring']) == true),
            ],
            'end_date' => [
                'date', $endDateTimeValidation, 'exclude_unless:schedule_type,Send Recurring',
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
            ],
            'start_time' => [
                Rule::requiredIf(in_array($request->input('schedule_type'), ['Send Later', 'Send Recurring']) == true),
                $startDateTimeValidation, 'exclude_if:schedule_type,Send Now',
            ],
            'end_time' => [
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
                $endDateTimeValidation, 'exclude_unless:schedule_type,Send Recurring'
            ],
            'days_of_the_week' => [
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
                'exclude_unless:schedule_type,Send Recurring'
            ],
            'message_to_send' => ['required', 'string'],
            'message_ids' => ['required', 'array'],
            'subcription_plan_ids' => ['sometimes', 'array']
        ])->validate();

        $data = array_merge($data, [
            'project_id' => $request->project->id
        ]);

        //  Create new campaign
        $campaign = Campaign::create($data);

        if( count( $request->input('subcription_plan_ids') ?? [] ) ) {

            //  Set subcription plan ids
            $subcription_plan_ids = $request->input('subcription_plan_ids');

            //  Sync the subscription plans
            $campaign->subscriptionPlans()->syncWithPivotValues($subcription_plan_ids, ['project_id' => $project->id]);

        }

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function update(Request $request, Project $project, Campaign $campaign)
    {
        /**
         *  Check whether or not the combination of the start date and start time
         *  produce a datetime that is in the future otherwise throw a validation
         *  error. The same validation rule is used for the "start_date" and the
         *  "start_time" fields.
         */
        $startDateTimeValidation = function ($attribute, $value, $fail) {

            if( ($date = request()->input('start_date') ) && ($time = request()->input('start_time') ) ) {

                $startDateTime = (new Campaign)->getCampaignStartDateTime($date, $time);

                if( $startDateTime->isFuture() ) {

                    return;

                }

            }

            $fail('The '.$attribute.' must be in the future.');

        };

        //  Validate the request inputs
        $data = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:5', 'max:50'],
            'description' => ['required', 'string', 'min:5', 'max:500'],
            'schedule_type' => ['required', 'string', Rule::in(Campaign::SCHEDULE_TYPE)],
            'recurring_duration' => [
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
                'exclude_unless:schedule_type,Send Recurring'
            ],
            'recurring_frequency' => [
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
                'exclude_unless:schedule_type,Send Recurring'
            ],
            'start_date' => [
                'date', $startDateTimeValidation, 'before:end_date', 'exclude_if:schedule_type,Send Now',
                Rule::requiredIf(in_array($request->input('schedule_type'), ['Send Later', 'Send Recurring']) == true),
            ],
            'end_date' => [
                'date', 'after:start_date', 'exclude_unless:schedule_type,Send Recurring',
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
            ],
            'start_time' => [
                Rule::requiredIf(in_array($request->input('schedule_type'), ['Send Later', 'Send Recurring']) == true),
                $startDateTimeValidation, 'exclude_if:schedule_type,Send Now',
            ],
            'end_time' => [
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
                'exclude_unless:schedule_type,Send Recurring'
            ],
            'days_of_the_week' => [
                Rule::requiredIf($request->input('schedule_type') == 'Send Recurring'),
                'exclude_unless:schedule_type,Send Recurring'
            ],
            'message_to_send' => ['required', 'string'],
            'message_ids' => ['required', 'array'],
            'subcription_plan_ids' => ['sometimes', 'array']
        ])->validate();

        $data = array_merge($data, [
            'project_id' => $request->project->id
        ]);

        //  Update campaign
        $campaign->update($data);

        //  If the campaign is sending recurring sms messages
        if( $campaign->schedule_type == 'Send Recurring' ) {

            /**
             *  Recalculate the next message date and times of the subscribers.
             *  This is so that the suggested date is insync with the current
             *  recurring schedule settings.
             */
            DB::table('campaign_subscriber')
                ->where('campaign_id', $campaign->id)
                ->whereNotNull('next_message_date')
                ->update([
                    'next_message_date' => $campaign->nextCampaignSmsMessageDate(),
                    'updated_at' => Carbon::now(),
                ]);

        }

        if( count( $request->input('subcription_plan_ids') ?? [] ) ) {

            //  Set subcription plan ids
            $subcription_plan_ids = $request->input('subcription_plan_ids');

            //  Sync the subscription plans
            $campaign->subscriptionPlans()->syncWithPivotValues($subcription_plan_ids, ['project_id' => $project->id]);

        }

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function delete(Project $project, Campaign $campaign)
    {
        //  Delete campaign
        $campaign->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }

    public function jobBatches(Project $project, Campaign $campaign)
    {
        //  Get the campaign job batches
        $campaignBatchJobsPayload = $campaign->campaignBatchJobs()->latest()->paginate(10);

        //  Render the campaigns view
        return Inertia::render('Campaigns/List/JobBatches/List/Main', [
            'campaign' => $campaign,
            'campaignBatchJobsPayload' => $campaignBatchJobsPayload
        ]);
    }
}
