<?php

namespace App\Models;

use App\Filters\CourseFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $fillable = [
        'education_level',
        'name'
    ];

    protected string $default_filters = CourseFilter::class;
}
