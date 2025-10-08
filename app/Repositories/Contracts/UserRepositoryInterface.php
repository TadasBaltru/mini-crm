<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{

    public function getAll(): Collection;

    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;

    public function getWithCompany(): Collection;
}

