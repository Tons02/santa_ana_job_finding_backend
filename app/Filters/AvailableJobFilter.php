<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class AvailableJobFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [];
}
