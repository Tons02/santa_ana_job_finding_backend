<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableJob extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $fillable = [
        'name',
    ];

    protected string $default_filters = BusinessTypeFilter::class;

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'users_business_types'
        );
    }
    
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills')
                    ->withTimestamps();
    }

}
