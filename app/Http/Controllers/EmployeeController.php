<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\CompanyResource;
use App\Mail\EmployeeCreated;
use App\Models\Employee;
use App\Models\Company;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

        return Inertia::render('Employees/Index', [
            'employees' => EmployeeResource::collection($employees),
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

        // Send email notification to the company
        Mail::to($company->email)->send(new EmployeeCreated($employee, $company));

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee created successfully. Notification email sent to ' . $company->email);
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

