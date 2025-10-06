<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // Get all companies
        $companies = Company::all();

        $companies->each(function ($company) {
            $employeeCount = rand(3, 10);
            
            Employee::factory()
                ->count($employeeCount)
                ->forCompany($company)
                ->create();
        });

        if ($companies->isNotEmpty()) {
            $firstCompany = $companies->first();

            Employee::factory()->forCompany($firstCompany)->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1-555-123-4567',
            ]);

            Employee::factory()->forCompany($firstCompany)->create([
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+1-555-987-6543',
            ]);
        }
    }
}
