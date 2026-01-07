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
        'landline',
        'mobile_number',
        'civil_status',
        'height',
        'religion',
        'resume',
        'full_address',
        'province',
        'lgu',
        'barangay',
        'employment_status',
        'employment_type',
        'months_looking',
        'is_ofw',
        'is_former_ofw',
        'last_deployment',
        'return_date',
        'email',
        'username',
        'password',
        'return_date',
        'role_type',
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
}
