<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class UserFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        'first_name',
        'last_name',
        'email',
        'role_type',
        'mobile_number',
        'full_address',
        'province',
        'lgu',
        'barangay',
        'username'
    ];

    public function role_type($role_type)
    {
        if ($role_type !== null) {
            $this->builder->where('role_type', $role_type);
        }
        return $this;
    }
}
