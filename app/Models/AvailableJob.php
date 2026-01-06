<?php

namespace App\Models;

use App\Filters\AvailableJobFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvailableJob extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $fillable = [
        'title',
        'description',
        'location',
        'is_remote',
        'employment_type',
        'experience_level',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'hiring_status',
        'skills',
        'posted_at',
        'expires_at',
    ];

    protected string $default_filters = AvailableJobFilter::class;
    
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills')
                    ->withTimestamps();
    }

}
