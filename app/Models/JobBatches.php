<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;
use Illuminate\Database\Eloquent\Model;

/**
 *  This is a custom Model that we created to access the
 *  Laravel "job_batches" tables so that we can link the
 *  created Job Batches to the related campaign via
 *  the custom "campaign_job_batches" table that
 *  we also created
 */
class JobBatches extends Model
{
    use HasFactory, HasEagerLimit;

    //  Indicate that this Model points to the Laravel "job_batches" table
    protected $table = 'job_batches';

    protected $cast = [
        'finished_at' => 'datetime'
    ];

    protected $appends = ['processed_jobs', 'progress'];

    /**
     * Get the total number of jobs that have been processed by the batch thus far.
     *
     * @return int
     */
    public function getProcessedJobsAttribute()
    {
        return $this->total_jobs - $this->pending_jobs;
    }

    /**
     * Get the percentage of jobs that have been processed (between 0-100).
     *
     * @return int
     */
    public function getProgressAttribute()
    {
        return $this->total_jobs > 0 ? round(($this->processed_jobs / $this->total_jobs) * 100) : 0;
    }
}
