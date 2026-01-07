<?php

namespace App\Models;

use App\Filters\JobApplicationFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $fillable = [
        'job_id',
        'user_id',
        'cover_letter',
        'status',
        'applied_at'
    ];

    protected string $default_filters = JobApplicationFilter::class;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function job()
    {
        return $this->belongsTo(AvailableJob::class, 'job_id');
    }
}
