<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users.
     */
    public function getAll(): Collection
    {
        return User::with('company')->get();
    }

    /**
     * Get paginated users.
     */
    public function getPaginated(int $perPage = 15, ?string $search = null, ?string $orderBy = 'name', string $orderDirection = 'asc', ?int $companyId = null): LengthAwarePaginator
    {
        $query = User::with('company');

        // Filter by company if user is not admin
        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply ordering
        $allowedOrderBy = ['name', 'email', 'created_at'];
        if ($orderBy === 'company') {
            $query->leftJoin('companies', 'users.company_id', '=', 'companies.id')
                  ->select('users.*')
                  ->orderBy('companies.name', $orderDirection);
        } elseif (in_array($orderBy, $allowedOrderBy)) {
            $query->orderBy($orderBy, $orderDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User
    {
        return User::with('company')->find($id);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a user.
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh(['company']);
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Get users with company relationship.
     */
    public function getWithCompany(): Collection
    {
        return User::with('company')->get();
    }
}

