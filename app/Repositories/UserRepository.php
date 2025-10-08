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
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('company')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
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

