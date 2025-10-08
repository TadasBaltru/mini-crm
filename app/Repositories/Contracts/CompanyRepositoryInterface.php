<?php

namespace App\Repositories\Contracts;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CompanyRepositoryInterface
{

    public function getAll(): Collection;

  
    public function getPaginated(int $perPage = 15, ?string $search = null, ?string $orderBy = 'name', string $orderDirection = 'asc'): LengthAwarePaginator;


    public function getByUserRole(int $userId, bool $isAdmin, ?int $companyId = null, int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Company;


    public function create(array $data): Company;


    public function update(Company $company, array $data): Company;


    public function delete(Company $company): bool;


    public function getWithEmployeesCount(): Collection;
}

