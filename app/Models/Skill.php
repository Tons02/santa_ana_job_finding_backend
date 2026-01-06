<?php

namespace App\Models;

use App\Filters\SkillFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $fillable = [
        'name',
    ];

    protected string $default_filters = SkillFilter::class;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills');
    }

    public function jobs()
    {
        return $this->belongsToMany(AvailableJob::class, 'job_skills');
    }

}
