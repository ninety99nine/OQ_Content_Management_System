<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Message;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    public function index(Project $project)
    {
        //  Count the messages
        $totalMessages = $project->messages()->count();

        //  Get the subscribers
        $subscribers = $project->subscribers()->with(['latestSubscriptions', 'latestMessages'])->withCount(['messages', 'subscriptions'])->latest()->paginate(10);

        //  Modify each subscriber by limiting the lastest subscriptions and lastest messages
        $subscribersTransformed = $subscribers->getCollection()->map(function($subscriber) {

            $subscriber = collect($subscriber)->toArray();

            //  Limit the lastest subscriptions to one record (if any)
            $subscriber['latest_subscriptions'] = collect($subscriber['latest_subscriptions'])->take(1)->all();

            //  Limit the lastest messages to one record (if any)
            $subscriber['latest_messages'] = collect($subscriber['latest_messages'])->take(1)->all();

            // Return the subscriber
            return $subscriber;

        });

        $subscribersTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $subscribersTransformed,
            $subscribers->total(),
            $subscribers->perPage(),
            $subscribers->currentPage(), [
                'path' => request()->url(),
                'query' => [
                    'page' => $subscribers->currentPage()
                ]
            ]
        );

        //  Render the subscribers view
        return Inertia::render('Subscribers/List/Main', [
            'subscribersPayload' => $subscribersTransformedAndPaginated,
            'totalMessages' => $totalMessages,
        ]);
    }

    public function create(Request $request, Project $project)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'msisdn' => ['required', 'string', 'min:11', Rule::unique('subscribers')->where(function ($query) use ($request, $project) {

                //  Make sure that this project does not already have this subscriber msisdn
                return $query->where('msisdn', $request->input('msisdn'))->where('project_id', $project->id);

            })]
        ], [
            'msisdn.required' => 'The mobile number is required.',
            'msisdn.min' => 'The mobile number must be at least 11 characters',
            'msisdn.unique' => 'A subscriber with the given mobile number already exists.',
            'msisdn.string' => 'Enter a valid mobile number and extension e.g 26772000001.',
        ])->validate();

        //  Set msisdn
        $msisdn = $request->input('msisdn');

        //  Create new subscriber
        Subscriber::create([
            'msisdn' => $msisdn,
            'project_id' => $project->id
        ]);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function update(Request $request, Project $project, Subscriber $subscriber)
    {
        //  Validate the request inputs
        Validator::make($request->all(), [
            'msisdn' => ['required', 'string', 'min:11', Rule::unique('subscribers')->where(function ($query) use ($request, $project) {

                //  Make sure that this project does not already have this subscriber msisdn
                return $query->where('msisdn', $request->input('msisdn'))->where('project_id', $project->id);

            })]
        ], [
            'msisdn.required' => 'The mobile number is required.',
            'msisdn.min' => 'The mobile number must be at least 11 characters',
            'msisdn.unique' => 'A subscriber with the given mobile number already exists.',
            'msisdn.string' => 'Enter a valid mobile number and extension e.g 26772000001.',
        ])->validate();

        //  Set msisdn
        $msisdn = $request->input('msisdn');

        //  Update subscriber
        $subscriber->update([
            'msisdn' => $msisdn,
            'project_id' => $project->id
        ]);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function delete(Project $project, Subscriber $subscriber)
    {
        //  Delete subscriber
        $subscriber->delete();

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
