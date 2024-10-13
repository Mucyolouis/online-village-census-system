<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;
use Nette\Utils\Random;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Define insurance options
        $insuranceOptions = [
            'none',
            'mituweli',
            'rssb',
            'mmi',
            'uap',
            'radiant insurance',
            'mis ur'
        ];
        $education_level = [
            
                'illiteracy',
                'basic_education',
                'secondary_education',
                'vocational_technical_education',
                'associates_degree',
                'bachelors_degree',
                'masters_degree',
                'doctorate_phd',
            
        ];

        // Superadmin user
        $sid = Str::uuid();
        $superAdminFirstName = 'Super';
        $superAdminLastName = 'Admin';
        DB::table('users')->insert([
            'id' => $sid,
            'username' => 'superadmin',
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'name' => $superAdminFirstName . ' ' . $superAdminLastName,
            'email' => 'superadmin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('superadmin'),
            'phone_number' => $faker->phoneNumber,
            'gender' => 'Male',
            'national_ID' => '1120038006401234',
            'date_of_birth' => $faker->date('Y-m-d'),
            'occupation' => $faker->jobTitle,
            'nationality' => 'Rwanda',
            'insurance' => $faker->randomElement($insuranceOptions),
            'marital_status' => 'Married',
            'disability' => 'No',
            'religion' => 'Muslim',
            'education_level' => $faker->randomElement($education_level),
            'village_id' => '4',
            'created_at' => now(),
            'updated_at' => now(),
            'is_approved' => 1,
            'approved_at' => now(),
            
        ]);

        // Bind superadmin user to FilamentShield
        Artisan::call('shield:super-admin', ['--user' => $sid]);

        $roles = 3;
        for ($i = 0; $i < 10; $i++) {
            $userId = Str::uuid();
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            
            DB::table('users')->insert([
                'id' => $userId,
                'username' => $faker->unique()->userName,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'name' => $firstName . ' ' . $lastName,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone_number' => $faker->phoneNumber,
                'gender' => $faker->randomElement(['Male', 'Female']),
                'national_ID' => '1120038006401234',
                'date_of_birth' => $faker->date('Y-m-d'),
                'occupation' => $faker->jobTitle,
                'nationality' => $faker->country,
                'insurance' => $faker->randomElement($insuranceOptions),
                'marital_status' => 'Married',
                'disability' => 'No',
                'religion' => 'Muslim',
                'education_level' => $faker->randomElement($education_level),
                'village_id' => rand(1, 16),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('model_has_roles')->insert([
                'role_id' => $roles,
                'model_type' => 'App\Models\User',
                'model_id' => $userId,
            ]);
        }
    }
}