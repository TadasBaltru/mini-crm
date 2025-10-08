<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{

    public function getAll(): Collection
    {
        return Company::all();
    }


    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Company::withCount('employees')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

 
    public function getByUserRole(int $userId, bool $isAdmin, ?int $companyId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Company::withCount('employees');

        // If not admin, only show their own company
        if (!$isAdmin && $companyId) {
            $query->where('id', $companyId);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }


    public function findById(int $id): ?Company
    {
        return Company::with(['employees', 'users'])->find($id);
    }


    public function create(array $data): Company
    {
        return Company::create($data);
    }


    public function update(Company $company, array $data): Company
    {
        $company->update($data);
        return $company->fresh();
    }


    public function delete(Company $company): bool
    {
        return $company->delete();
    }

 
    public function getWithEmployeesCount(): Collection
    {
        return Company::withCount('employees')->get();
    }
}

