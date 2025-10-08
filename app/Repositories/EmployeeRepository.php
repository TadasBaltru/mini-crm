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


    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Employee::with('company')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
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

