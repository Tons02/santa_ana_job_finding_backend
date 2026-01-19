<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Filters\UserFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'gender',
        'civil_status',
        'region',
        'province',
        'city_municipality',
        'barangay',
        'street_address',
        'telephone',
        'mobile_number',
        'height',
        'religion',
        'resume',
        'employment_status',
        'employment_type',
        'months_looking',
        'is_4ps',
        'is_pwd',
        'disability',
        'is_ofw',
        'work_experience',
        'is_former_ofw',
        'country',
        'last_deployment',
        'return_date',
        'program_service',
        'remarks',
        'email',
        'username',
        'password',
        'role_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected string $default_filters = UserFilter::class;

    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,
            'user_skills',
            'user_id',
            'skill_id'
        )->withTimestamps();
    }

    public function preferred_positions()
    {
        return $this->belongsToMany(
            PreferredPosition::class,
            'user_preferred_positions',
            'user_id',
            'preferred_position_id'
        )->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(
            Course::class,
            'user_courses',
            'user_id',
            'course_id'
        )->withTimestamps();
    }
}
