<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Skill;
use App\Models\Course;
use App\Models\PreferredPosition;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        $skills = Skill::pluck('id')->toArray();
        $courses = Course::pluck('id')->toArray();
        $positions = PreferredPosition::pluck('id')->toArray();

        DB::beginTransaction();

        try {
            for ($i = 1; $i <= 50; $i++) {
                $isEmployed = fake()->boolean(60);
                $isOfw = fake()->boolean(30);
                $isFormerOfw = !$isOfw ? fake()->boolean(20) : false;
                $isPwd = fake()->boolean(10);
                $is4ps = fake()->boolean(15);

                $user = User::create([
                    'first_name' => fake()->firstName(),
                    'middle_name' => fake()->optional(0.8)->lastName(),
                    'last_name' => fake()->lastName(),
                    'suffix' => fake()->optional(0.2)->randomElement(['Jr.', 'Sr.', 'II', 'III']),
                    'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'civil_status' => fake()->randomElement(['single', 'married', 'widowed', 'separated']),
                    'region' => 'REGION 3 (CENTRAL LUZON)',
                    'province' => 'PAMPANGA',
                    'city_municipality' => 'SANTA ANA, PAMPANGA',
                    'barangay' => fake()->randomElement(['San Agustin', 'San Bartolome', 'San Isidro', 'San Joaquin', 'San Jose']),
                    'street_address' => fake()->streetAddress(),
                    'telephone' => fake()->optional(0.4)->numerify('(045) ###-####'),
                    'mobile_number' => '+63' . fake()->numerify('9#########'),
                    'height' => fake()->numberBetween(150, 190),
                    'religion' => fake()->randomElement(['roman_catholic', 'islam', 'iglesia_ni_cristo', 'born_again', 'baptist', 'seventh_day_adventist']),
                    'resume' => null,
                    'employment_status' => $isEmployed ? 'employed' : 'unemployed',
                    'employment_type' => $isEmployed ? fake()->randomElement(['wage', 'unemployed', 'self_employed', 'full_time', 'freelance', 'contract', 'internship']) : null,
                    'months_looking' => fake()->numberBetween(0, 60),
                    'is_4ps' => $is4ps,
                    'is_pwd' => $isPwd,
                    'disability' => $isPwd ? 'Visual Impairment' : null,
                    'is_ofw' => $isOfw,
                    'work_experience' => fake()->numberBetween(0, 30) . ' years',
                    'is_former_ofw' => $isFormerOfw,
                    'country' => ($isOfw || $isFormerOfw) ? fake()->randomElement(['Saudi Arabia', 'UAE', 'Singapore']) : null,
                    'last_deployment' => ($isOfw || $isFormerOfw) ? fake()->year() : null,
                    'return_date' => $isFormerOfw ? fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d') : null,
                    'transaction_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                    'program_service' => fake()->randomElement(['PESO', 'TESDA', 'DOLE']),
                    'event' => fake()->randomElement(['Job Fair', 'Walk-in', 'Online Application']),
                    'transaction' => fake()->randomElement(['New Registration', 'Renewal', 'Update']),
                    'remarks' => fake()->optional(0.3)->sentence(),
                    'email' => fake()->unique()->safeEmail(),
                    'username' => 'user' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password123'),
                    'role_type' => 'user',
                ]);

                if (!empty($skills)) {
                    $user->skills()->attach(fake()->randomElements($skills, min(3, count($skills))));
                }

                if (!empty($courses)) {
                    $user->courses()->attach(fake()->randomElements($courses, min(2, count($courses))));
                }

                if (!empty($positions)) {
                    $user->preferred_positions()->attach(fake()->randomElements($positions, min(2, count($positions))));
                }

                if ($i % 100 == 0) echo "Created {$i} users...\n";
            }

            DB::commit();
            echo "Successfully created 1000 users!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
