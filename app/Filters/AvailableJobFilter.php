<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class AvailableJobFilter extends QueryFilters
{
    protected array $columnSearch = [
        'title',
        'description',
    ];

    protected array $relationSearch = [
        'skills' => ['name'],
    ];

    public function location($location)
    {
        if ($location !== null) {
            $this->builder->where('location', $location);
        }
        return $this;
    }
}
