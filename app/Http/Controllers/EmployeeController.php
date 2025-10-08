<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\CompanyResource;
use App\Models\Employee;
use App\Models\Company;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    use AuthorizesRequests;

    protected EmployeeRepositoryInterface $employeeRepository;
    protected CompanyRepositoryInterface $companyRepository;

    public function __construct(
        EmployeeRepositoryInterface $employeeRepository,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->companyRepository = $companyRepository;
    }

  
    public function index(): Response
    {
        $this->authorize('viewAny', Employee::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $search = request('search');
        $orderBy = request('order_by', 'first_name');
        $orderDirection = request('order_direction', 'asc');
        
        $employees = $this->employeeRepository->getPaginated(
            15,
            $search,
            $orderBy,
            $orderDirection
        );

        // Build pagination links
        $links = [];
        
        // Previous link
        $links[] = [
            'url' => $employees->previousPageUrl(),
            'label' => '&laquo; Previous',
            'active' => false,
        ];
        
        // Page number links
        foreach (range(1, $employees->lastPage()) as $page) {
            $links[] = [
                'url' => $employees->url($page),
                'label' => (string) $page,
                'active' => $page === $employees->currentPage(),
            ];
        }
        
        // Next link
        $links[] = [
            'url' => $employees->nextPageUrl(),
            'label' => 'Next &raquo;',
            'active' => false,
        ];
        
        $employeesData = collect($employees->items())->map(function ($employee) {
            return (new EmployeeResource($employee))->toArray(request());
        })->values()->all();

        return Inertia::render('Employees/Index', [
            'employees' => [
                'data' => $employeesData,
                'links' => $links,
                'current_page' => $employees->currentPage(),
                'first_page_url' => $employees->url(1),
                'from' => $employees->firstItem() ?? 0,
                'last_page' => $employees->lastPage(),
                'last_page_url' => $employees->url($employees->lastPage()),
                'next_page_url' => $employees->nextPageUrl(),
                'path' => $employees->path(),
                'per_page' => $employees->perPage(),
                'prev_page_url' => $employees->previousPageUrl(),
                'to' => $employees->lastItem() ?? 0,
                'total' => $employees->total(),
            ],
            'filters' => [
                'search' => $search,
                'order_by' => $orderBy,
                'order_direction' => $orderDirection,
            ],
            'can' => [
                'create' => $user->can('create', Employee::class),
            ],
        ]);
    }

  
    public function create(): Response
    {
        $this->authorize('create', Employee::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $companies = $user->isAdmin()
            ? $this->companyRepository->getAll()
            : collect([$user->company]);

        return Inertia::render('Employees/Create', [
            'companies' => $companies->map(fn($company) => [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
            ])->toArray(),
        ]);
    }


    public function store(EmployeeStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Employee::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $request->validated();

        if ($user->isCompanyUser()) {
            $data['company_id'] = $user->company_id;
        }

        $company = Company::find($data['company_id']);
        if (!$company || (!$user->isAdmin() && $user->company_id !== $company->id)) {
            return redirect()
                ->back()
                ->withErrors(['company_id' => 'You do not have permission to add employees to this company.']);
        }

        $employee = $this->employeeRepository->create($data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }


    public function show(Employee $employee): Response
    {
        $this->authorize('view', $employee);

        $employee = $this->employeeRepository->findById($employee->id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Inertia::render('Employees/Show', [
            'employee' => [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'company_id' => $employee->company_id,
                'company' => $employee->company ? [
                    'id' => $employee->company->id,
                    'name' => $employee->company->name,
                    'email' => $employee->company->email,
                ] : null,
                'created_at' => $employee->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $employee->updated_at->format('Y-m-d H:i:s'),
            ],
            'can' => [
                'update' => $user->can('update', $employee),
                'delete' => $user->can('delete', $employee),
            ],
        ]);
    }


    public function edit(Employee $employee): Response
    {
        $this->authorize('update', $employee);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $companies = $user->isAdmin()
            ? $this->companyRepository->getAll()
            : collect([$user->company]);

        return Inertia::render('Employees/Edit', [
            'employee' => [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'company_id' => $employee->company_id,
            ],
            'companies' => $companies->map(fn($company) => [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
            ])->toArray(),
        ]);
    }


    public function update(EmployeeUpdateRequest $request, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $employee);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $request->validated();

        // If company user, force their company_id
        if ($user->isCompanyUser() && isset($data['company_id'])) {
            $data['company_id'] = $user->company_id;
        }

        // Verify company exists and user has access to it if company_id is being changed
        if (isset($data['company_id']) && $data['company_id'] !== $employee->company_id) {
            $company = Company::find($data['company_id']);
            if (!$company || (!$user->isAdmin() && $user->company_id !== $company->id)) {
                return redirect()
                    ->back()
                    ->withErrors(['company_id' => 'You do not have permission to move employees to this company.']);
            }
        }

        $this->employeeRepository->update($employee, $data);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }


    public function destroy(Employee $employee): RedirectResponse
    {
        $this->authorize('delete', $employee);

        $this->employeeRepository->delete($employee);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}

