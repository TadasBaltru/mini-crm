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
        
        $companies = $this->companyRepository->getPaginated(
            15,
            $search,
            $orderBy,
            $orderDirection
        );

        // Build pagination links
        $links = [];
        
        // Previous link
        $links[] = [
            'url' => $companies->previousPageUrl(),
            'label' => '&laquo; Previous',
            'active' => false,
        ];
        
        // Page number links
        foreach (range(1, $companies->lastPage()) as $page) {
            $links[] = [
                'url' => $companies->url($page),
                'label' => (string) $page,
                'active' => $page === $companies->currentPage(),
            ];
        }
        
        // Next link
        $links[] = [
            'url' => $companies->nextPageUrl(),
            'label' => 'Next &raquo;',
            'active' => false,
        ];
        
        $companiesData = collect($companies->items())->map(function ($company) {
            return (new CompanyResource($company))->toArray(request());
        })->values()->all();

        return Inertia::render('Companies/Index', [
            'companies' => [
                'data' => $companiesData,
                'links' => $links,
                'current_page' => $companies->currentPage(),
                'first_page_url' => $companies->url(1),
                'from' => $companies->firstItem() ?? 0,
                'last_page' => $companies->lastPage(),
                'last_page_url' => $companies->url($companies->lastPage()),
                'next_page_url' => $companies->nextPageUrl(),
                'path' => $companies->path(),
                'per_page' => $companies->perPage(),
                'prev_page_url' => $companies->previousPageUrl(),
                'to' => $companies->lastItem() ?? 0,
                'total' => $companies->total(),
            ],
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
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
                'website' => $company->website,
                'employees_count' => $company->employees()->count(),
                'created_at' => $company->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $company->updated_at->format('Y-m-d H:i:s'),
            ],
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
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
                'website' => $company->website,
            ],
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

