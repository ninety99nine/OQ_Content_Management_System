<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'project_id'];

    /**
     * Get the project associated with the language.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the messages associated with the language.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

}
