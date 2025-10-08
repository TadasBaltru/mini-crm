<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCompanyUser();
    }


    public function view(User $user, Employee $employee): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isCompanyUser() && $user->company_id === $employee->company_id;
    }

    public function create(User $user): bool
    {
        // Both admins and company users can create employees
        return $user->isAdmin() || $user->isCompanyUser();
    }


    public function update(User $user, Employee $employee): bool
    {
        // Admins can update any employee
        if ($user->isAdmin()) {
            return true;
        }

        // Company users can only update employees in their own company
        return $user->isCompanyUser() && $user->company_id === $employee->company_id;
    }


    public function delete(User $user, Employee $employee): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isCompanyUser() && $user->company_id === $employee->company_id;
    }


   

}

