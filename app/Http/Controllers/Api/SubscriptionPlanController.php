<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionPlanController extends Controller
{
    public function get(Request $request, Project $project)
    {
        return $project->subscriptionPlans()->paginate();
    }
}
