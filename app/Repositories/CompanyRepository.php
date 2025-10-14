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


    public function getPaginated(int $perPage = 15, ?string $search = null, ?string $orderBy = 'name', string $orderDirection = 'asc', ?int $companyId = null): LengthAwarePaginator
    {
        $query = Company::withCount('employees');

        // Filter by specific company if user is not admin
        if ($companyId !== null) {
            $query->where('id', $companyId);
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply ordering
        $allowedOrderBy = ['name', 'email', 'created_at'];
        if (in_array($orderBy, $allowedOrderBy)) {
            $query->orderBy($orderBy, $orderDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate($perPage)->withQueryString();
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

