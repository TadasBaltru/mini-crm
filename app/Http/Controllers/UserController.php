<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    use AuthorizesRequests;

    protected UserRepositoryInterface $userRepository;
    protected CompanyRepositoryInterface $companyRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        $users = $this->userRepository->getPaginated(15);

        return Inertia::render('Users/Index', [
            'users' => UserResource::collection($users),
            'can' => [
                'create' => $currentUser->can('create', User::class),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        $companies = $this->companyRepository->getAll();

        return Inertia::render('Users/Create', [
            'companies' => $companies->map(fn($company) => [
                'id' => $company->id,
                'name' => $company->name,
            ])->toArray(),
        ]);
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        
        // Hash the password
        $data['password'] = Hash::make($data['password']);

        // Ensure admin users don't have company_id
        if ($data['role'] === 'admin') {
            $data['company_id'] = null;
        }

        $this->userRepository->create($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user): Response
    {
        $this->authorize('view', $user);

        $userData = $this->userRepository->findById($user->id);

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $userData->id,
                'name' => $userData->name,
                'email' => $userData->email,
                'role' => $userData->role,
                'company_id' => $userData->company_id,
                'company' => $userData->company ? [
                    'id' => $userData->company->id,
                    'name' => $userData->company->name,
                    'email' => $userData->company->email,
                ] : null,
                'created_at' => $userData->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $userData->updated_at->format('Y-m-d H:i:s'),
            ],
            'can' => [
                'update' => $currentUser->can('update', $user),
                'delete' => $currentUser->can('delete', $user),
            ],
        ]);
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        $companies = $this->companyRepository->getAll();

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'company_id' => $user->company_id,
            ],
            'companies' => $companies->map(fn($company) => [
                'id' => $company->id,
                'name' => $company->name,
            ])->toArray(),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Ensure admin users don't have company_id
        if (isset($data['role']) && $data['role'] === 'admin') {
            $data['company_id'] = null;
        }

        $this->userRepository->update($user, $data);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->userRepository->delete($user);

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}

