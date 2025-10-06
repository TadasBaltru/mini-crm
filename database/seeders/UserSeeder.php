<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin users
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@minicrm.com',
            'password' => Hash::make('password'),
        ]);

        $companies = Company::all();

        $companies->each(function ($company) {
            $userCount = rand(1, 3);
            
            User::factory()
                ->count($userCount)
                ->forCompany($company)
                ->create();
        });

        if ($companies->isNotEmpty()) {
            $firstCompany = $companies->first();

            User::factory()->forCompany($firstCompany)->create([
                'name' => 'Company Manager',
                'email' => 'manager@' . strtolower(str_replace(' ', '', $firstCompany->name)) . '.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Create some random company users
        User::factory()->count(10)->create();
    }
}
