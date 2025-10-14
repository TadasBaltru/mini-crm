<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    use AuthorizesRequests;

    protected CompanyRepositoryInterface $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

 
    public function index(): Response
    {
        $this->authorize('viewAny', Company::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $search = request('search');
        $orderBy = request('order_by', 'name');
        $orderDirection = request('order_direction', 'asc');
        
        $companyId = $user->isAdmin() ? null : $user->company_id;
        
        $companies = $this->companyRepository->getPaginated(
            15,
            $search,
            $orderBy,
            $orderDirection,
            $user->company_id
        );

        return Inertia::render('Companies/Index', [
            'companies' => $companies,
            'filters' => [
                'search' => $search,
                'order_by' => $orderBy,
                'order_direction' => $orderDirection,
            ],
            'can' => [
                'create' => $user->can('create', Company::class),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Company::class);

        return Inertia::render('Companies/Create');
    }


    public function store(CompanyStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Company::class);

        $company = $this->companyRepository->create($request->validated());

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company created successfully.');
    }


    public function show(Company $company): Response
    {
        $this->authorize('view', $company);

        $company = $this->companyRepository->findById($company->id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Inertia::render('Companies/Show', [
            'company' => new CompanyResource($company),
            'can' => [
                'update' => $user->can('update', $company),
                'delete' => $user->can('delete', $company),
            ],
        ]);
    }


    public function edit(Company $company): Response
    {
        $this->authorize('update', $company);

        return Inertia::render('Companies/Edit', [
            'company' => new CompanyResource($company)
        ]);
    }


    public function update(CompanyUpdateRequest $request, Company $company): RedirectResponse
    {
        $this->authorize('update', $company);

        $this->companyRepository->update($company, $request->validated());

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }


    public function destroy(Company $company): RedirectResponse
    {
        $this->authorize('delete', $company);

        $this->companyRepository->delete($company);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}

