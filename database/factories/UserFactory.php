<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'company',
            'company_id' => Company::factory(),
        ];
    }


    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }


    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'company_id' => null,
        ]);
    }


    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'company',
            'company_id' => $company->id,
        ]);
    }
}
