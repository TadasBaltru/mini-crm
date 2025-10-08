<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Employee;
use App\Policies\CompanyPolicy;
use App\Policies\EmployeePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(
            \App\Repositories\Contracts\CompanyRepositoryInterface::class,
            \App\Repositories\CompanyRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\EmployeeRepositoryInterface::class,
            \App\Repositories\EmployeeRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Register policies
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
    }
}
