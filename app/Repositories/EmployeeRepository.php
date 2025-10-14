<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EmployeeRepository implements EmployeeRepositoryInterface
{

    public function getAll(): Collection
    {
        return Employee::with('company')->get();
    }


    public function getPaginated(int $perPage = 15, ?string $search = null, ?string $orderBy = 'first_name', string $orderDirection = 'asc', ?int $companyId = null): LengthAwarePaginator
    {
        $query = Employee::with('company');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply ordering
        $allowedOrderBy = ['first_name', 'last_name', 'email', 'created_at'];
        if ($orderBy === 'company') {
            $query->join('companies', 'employees.company_id', '=', 'companies.id')
                  ->select('employees.*')
                  ->orderBy('companies.name', $orderDirection);
        } elseif (in_array($orderBy, $allowedOrderBy)) {
            $query->orderBy($orderBy, $orderDirection);
        } else {
            $query->orderBy('first_name', 'asc');
        }

        return $query->paginate($perPage)->withQueryString();
    }


    public function getByCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return Employee::with('company')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }


    public function getByUserRole(bool $isAdmin, ?int $companyId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Employee::with('company');

        // If not admin, only show employees from their company
        if (!$isAdmin && $companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }


    public function findById(int $id): ?Employee
    {
        return Employee::with('company')->find($id);
    }


    public function create(array $data): Employee
    {
        return Employee::create($data);
    }


    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee->fresh(['company']);
    }


    public function delete(Employee $employee): bool
    {
        return $employee->delete();
    }


    public function getWithCompany(): Collection
    {
        return Employee::with('company')->get();
    }
}

