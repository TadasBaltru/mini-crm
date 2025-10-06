<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::factory()->count(20)->create();

        Company::factory()->create([
            'name' => 'Acme Corporation',
            'email' => 'info@acme-corp.com',
            'website' => 'https://www.acme-corp.com',
        ]);

        Company::factory()->create([
            'name' => 'Global Tech Solutions',
            'email' => 'contact@globaltech.com',
            'website' => 'https://www.globaltech.com',
        ]);

        Company::factory()->create([
            'name' => 'Innovation Labs',
            'email' => 'hello@innovationlabs.io',
            'website' => 'https://www.innovationlabs.io',
        ]);
    }
}
