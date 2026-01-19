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
        // Get existing skills, courses, and positions for relationships
        $skills = Skill::pluck('id')->toArray();
        $courses = Course::pluck('id')->toArray();
        $positions = PreferredPosition::pluck('id')->toArray();

        // From your validation rules
        $genders = ['male', 'female'];
        $civilStatuses = ['single', 'married', 'widowed', 'separated'];
        $religions = ['roman_catholic', 'islam', 'iglesia_ni_cristo', 'born_again', 'baptist', 'seventh_day_adventist'];
        $employmentStatuses = ['employed', 'unemployed'];
        $employmentTypes = ['full_time', 'part_time', 'contract', 'internship'];
        $countries = ['Saudi Arabia', 'UAE', 'Singapore', 'Hong Kong', 'Japan', 'Qatar', 'Kuwait'];
        $events = ['Job Fair', 'Walk-in', 'Online Application', 'Referral', 'Social Media'];
        $programServices = ['PESO', 'TESDA', 'DOLE', 'PhilJobNet', 'JobStreet'];
        $educationLevels = ['Elementary', 'High School', 'Senior High School', 'Vocational', 'College', 'Post Graduate'];

        // Fixed values from validation
        $region = 'REGION 3 (CENTRAL LUZON)';
        $province = 'PAMPANGA';
        $cityMunicipality = 'SANTA ANA, PAMPANGA';

        // Sample barangays in Santa Ana, Pampanga
        $barangays = [
            'San Agustin',
            'San Bartolome',
            'San Isidro',
            'San Joaquin',
            'San Jose',
            'San Juan',
            'San Nicolas',
            'San Pablo',
            'San Pedro',
            'Santa Lucia'
        ];

        DB::beginTransaction();

        try {
            for ($i = 1; $i <= 10; $i++) {
                $isOfw = fake()->boolean(30); // 30% chance of being OFW
                $isFormerOfw = !$isOfw ? fake()->boolean(20) : false; // 20% chance if not current OFW
                $isPwd = fake()->boolean(10); // 10% chance
                $is4ps = fake()->boolean(15); // 15% chance
                $isEmployed = fake()->boolean(60); // 60% employed

                $user = User::create([
                    'first_name' => fake()->firstName(),
                    'middle_name' => fake()->optional(0.8)->lastName(),
                    'last_name' => fake()->lastName(),
                    'suffix' => fake()->optional(0.2)->randomElement(['Jr.', 'Sr.', 'II', 'III', 'IV']),
                    'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
                    'gender' => fake()->randomElement($genders),
                    'civil_status' => fake()->randomElement($civilStatuses),
                    'region' => $region,
                    'province' => $province,
                    'city_municipality' => $cityMunicipality,
                    'barangay' => fake()->randomElement($barangays),
                    'street_address' => fake()->streetAddress(),
                    'telephone' => fake()->optional(0.4)->numerify('(045) ###-####'), // Pampanga area code
                    'mobile_number' => '+63' . fake()->numerify('9#########'), // Format: +63XXXXXXXXXX
                    'height' => fake()->numberBetween(150, 190), // numeric value only
                    'religion' => fake()->randomElement($religions),
                    'resume' => null,
                    'employment_status' => $isEmployed ? 'employed' : 'unemployed',
                    'employment_type' => $isEmployed
                        ? fake()->randomElement($employmentTypes)
                        : null, // null if unemployed
                    'months_looking' => fake()->numberBetween(0, 60),
                    'is_4ps' => $is4ps,
                    'is_pwd' => $isPwd,
                    'disability' => $isPwd ? fake()->randomElement(['Visual Impairment', 'Hearing Impairment', 'Physical Disability', 'Mental/Psychosocial Disability', 'Speech Impairment']) : null,
                    'is_ofw' => $isOfw,
                    'work_experience' => fake()->optional(0.7)->numberBetween(0, 30) . ' years',
                    'is_former_ofw' => $isFormerOfw,
                    'country' => ($isOfw || $isFormerOfw) ? fake()->randomElement($countries) : null,
                    'last_deployment' => ($isOfw || $isFormerOfw) ? fake()->year() : null,
                    'return_date' => $isFormerOfw ? fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d') : null,
                    'transaction_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                    'program_service' => fake()->randomElement($programServices),
                    'event' => fake()->randomElement($events),
                    'transanction' => fake()->randomElement(['New Registration', 'Renewal', 'Update', 'Follow-up']),
                    'remarks' => fake()->optional(0.3)->sentence(),
                    'email' => fake()->unique()->safeEmail(),
                    'username' => 'user' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password123'),
                    'role_type' => 'user',
                ]);

                // Attach random skills (1-5 skills per user)
                if (!empty($skills)) {
                    $randomSkills = fake()->randomElements($skills, fake()->numberBetween(1, min(5, count($skills))));
                    $user->skills()->sync($randomSkills);
                }

                // Attach random courses (1-3 courses per user)
                if (!empty($courses)) {
                    $randomCourses = fake()->randomElements($courses, fake()->numberBetween(1, min(3, count($courses))));
                    $user->courses()->sync($randomCourses);
                }

                // Attach random preferred positions (1-3 positions per user)
                if (!empty($positions)) {
                    $randomPositions = fake()->randomElements($positions, fake()->numberBetween(1, min(3, count($positions))));
                    $user->preferred_positions()->sync($randomPositions);
                }

                // Output progress
                if ($i % 100 == 0) {
                    echo "Created {$i} users...\n";
                }
            }

            DB::commit();
            echo "Successfully created 1000 users!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
