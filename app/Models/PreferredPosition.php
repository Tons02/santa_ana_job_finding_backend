<?php

namespace App\Models;

use App\Filters\PreferredPositionFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreferredPosition extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $fillable = [
        'name',
    ];

    protected string $default_filters = PreferredPositionFilter::class;
}
