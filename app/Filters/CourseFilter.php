<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class CourseFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [];
}
