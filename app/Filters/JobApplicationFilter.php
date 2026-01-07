<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class JobApplicationFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [];

    public function user_id($user_id)
    {
        if ($user_id !== null) {
            $this->builder->where('user_id', $user_id);
        }
        return $this;
    }

    public function job_status($job_status)
    {
        if ($job_status !== null) {
            $this->builder->where('status', $job_status);
        }
        return $this;
    }
}
