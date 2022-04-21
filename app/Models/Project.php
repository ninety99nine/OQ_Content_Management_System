<?php

namespace App\Models;

use App\Models\Pivots\ProjectUserAsTeamMember;
use App\Traits\Models\ProjectTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory, ProjectTrait;

    const PERMISSIONS = [
        'View users', 'Manage users',
        'View topics', 'Manage topics',
        'View messages', 'Manage messages',
        'View campaigns', 'Manage campaigns',
        'View subscribers', 'Manage subscribers',
        'View subscriptions', 'Manage subscriptions',
        'View subscription plans', 'Manage subscription plans',
        'View project settings', 'Manage project settings',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'settings' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'settings'];

    /**
     *  Get the Users that have been assigned to this Project as a team member
     *
     *  @return Illuminate\Database\Eloquent\Concerns\HasRelationships::belongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_projects', 'project_id', 'user_id')
                    ->withPivot(ProjectUserAsTeamMember::VISIBLE_COLUMNS)
                    ->using(ProjectUserAsTeamMember::class);
    }

    /**
     * Get the topics associated with the project.
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * Get the topics associated with the project.
     */
    public function mainTopics()
    {
        return $this->topics()->whereNull('parent_topic_id');
    }

    /**
     * Get the messages associated with the project.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the messages associated with the project.
     */
    public function mainMessages()
    {
        return $this->messages()->whereNull('parent_message_id');
    }

    /**
     * Get the campaigns associated with the project.
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get the subscribers associated with the project.
     */
    public function subscribers()
    {
        return $this->hasMany(Subscriber::class);
    }

    /**
     * Get the subscriptions associated with the project.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the subscriptions associated with the project.
     */
    public function subscriptionPlans()
    {
        return $this->hasMany(SubscriptionPlan::class);
    }

    //  ON DELETE EVENT
    public static function boot()
    {
        try {

            parent::boot();

            //  before delete() method call this
            static::deleting(function ($project) {

                //  Delete all topics
                $project->topics()->delete();

                //  Delete all messages
                $project->messages()->delete();

                //  Delete all campaigns
                $project->campaigns()->delete();

                //  Delete all subscribers
                $project->subscribers()->delete();

                //  Delete all subscriptions
                $project->subscriptions()->delete();

                //  Delete all subscription plans
                $project->subscriptionPlans()->delete();

                //  Delete all records of users being assigned to this project
                DB::table('user_projects')->where(['project_id' => $project->id])->delete();

                // do the rest of the cleanup...
            });
        } catch (\Exception $e) {
            throw($e);
        }
    }

}
