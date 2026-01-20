<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class UserFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        'full_name',
        'first_name',
        'last_name',
        'email',
        'role_type',
        'mobile_number',
        'full_address',
        'province',
        'region',
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
        if (!empty($skills)) {

            $skillsArray = array_map(
                'trim',
                explode(',', $skills)
            );

            $this->builder->whereHas('skills', function ($query) use ($skillsArray) {
                $query->whereIn('name', $skillsArray);
            });
        }

        return $this;
    }


    public function preferred_positions($preferred_positions)
    {
        if (!empty($preferred_positions)) {

            $positions = array_map(
                'trim',
                explode(',', $preferred_positions)
            );

            $this->builder->whereHas('preferred_positions', function ($query) use ($positions) {
                $query->whereIn('name', $positions);
            });
        }

        return $this;
    }
}
