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

    public function skills($skills)
    {
        if ($skills !== null) {
            $this->builder->whereHas('skills', function ($query) use ($skills) {
                $query->whereIn('name', (array)$skills);
            });
        }
        return $this;
    }

    public function preferred_positions($preferred_positions)
    {
        if ($preferred_positions !== null) {
            $this->builder->whereHas('preferred_positions', function ($query) use ($preferred_positions) {
                $query->whereIn('name', (array)$preferred_positions);
            });
        }
        return $this;
    }
}
