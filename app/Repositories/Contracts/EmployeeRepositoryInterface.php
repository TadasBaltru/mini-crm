<?php

namespace App\Repositories\Contracts;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeRepositoryInterface
{
  
    public function getAll(): Collection;

    public function getPaginated(int $perPage = 15, ?string $search = null, ?string $orderBy = 'first_name', string $orderDirection = 'asc', ?int $companyId = null): LengthAwarePaginator;

    
    public function getByUserRole(bool $isAdmin, ?int $companyId = null, int $perPage = 15): LengthAwarePaginator;


    public function findById(int $id): ?Employee;


    public function create(array $data): Employee;


    public function update(Employee $employee, array $data): Employee;

  
    public function delete(Employee $employee): bool;


    public function getWithCompany(): Collection;
}

