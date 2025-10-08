<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

 
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCompanyUser();
    }

  
    public function view(User $user, Company $company): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isCompanyUser() && $user->company_id === $company->id;
    }

 
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Company $company): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isCompanyUser() && $user->company_id === $company->id;
    }

  
    public function delete(User $user, Company $company): bool
    {
        return $user->isAdmin();
    }


}


