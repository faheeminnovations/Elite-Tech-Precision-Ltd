<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Response;
use App\Models\Service;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\AreaScheduleService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public const REPORT_TYPES = [
        'area-ppm-schedule' => 'Area PPM Schedule (3-Week Lookahead)',
        'upcoming-ppms' => 'All Upcoming PPMs',
        'overdue-ppms' => 'Overdue PPMs',
        'expiring-contracts' => 'Contracts Expiring Soon',
        'customers-no-contract' => 'Customers Without a Contract',
        'customer-responses' => 'Customer Responses',
        'service-history' => 'Completed PPM Records / Full Service History',
        'service-jobs' => 'Service Jobs',
        'contracts-register' => 'Contracts Register',
        'customer-register' => 'Customer Register',
        'engineer-performance' => 'Engineer Performance Summary',
        'engineer-activity-log' => 'Engineer Activity Log',
    ];

    public function index(Request $request, AreaScheduleService $scheduleService): View
    {
        $type = $request->get('type', 'area-ppm-schedule');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->addMonths(2)->endOfMonth()->format('Y-m-d'));
        $customer = $request->get('customer');
        $engineer = $request->get('engineer');
        $area = $request->get('area');
        $category = $request->get('category');
        $system = $request->get('system');

        $preview = $this->buildPreview($type, $dateFrom, $dateTo, $customer, $engineer, $area, $category, $system, $scheduleService);

        return view('reports', [
            'customers' => Customer::orderBy('name')->get(),
            'reportTypes' => self::REPORT_TYPES,
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'customer' => $customer,
            'engineer' => $engineer,
            'area' => $area,
            'category' => $category,
            'system' => $system,
            'preview' => $preview,
            'areas' => Customer::AREAS,
            'categories' => Customer::CATEGORIES,
            'engineers' => User::engineers()->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function resolveEngineerFilter(?string $engineer): ?int
    {
        if (! $engineer || $engineer === '' || $engineer === 'All') {
            return null;
        }

        // If it's already an ID
        if (is_numeric($engineer)) {
            return (int) $engineer;
        }

        // Try to find engineer by name
        $engineerUser = User::where('name', $engineer)->first();
        return $engineerUser ? $engineerUser->id : null;
    }

    private function resolveCustomerFilter(?string $customer): ?int
    {
        if (! $customer || $customer === '') {
            return null;
        }

        if (is_numeric($customer)) {
            return (int) $customer;
        }

        return Customer::where('name', $customer)->value('id');
    }

    private function buildPreview(
        string $type,
        ?string $dateFrom,
        ?string $dateTo,
        ?string $customer,
        ?string $engineer,
        ?string $area,
        ?string $category,
        ?string $system,
        AreaScheduleService $scheduleService,
    ): array {
        $customerFilter = $this->resolveCustomerFilter($customer);
        $search = $customerFilter ? Customer::where('id', $customerFilter)->value('name') : null;
        $engineerFilter = $this->resolveEngineerFilter($engineer);

        // For customer filtering in contracts, we need to filter by customer_name
        $customerName = $search;

        if ($type === 'engineer-performance') {
            $query = User::engineers()->active()->when($engineerFilter, fn ($q) => $q->where('id', $engineerFilter))->orderBy('name');

            $paginated = $query->paginate(20)->withQueryString();

            $rows = $paginated->map(function (User $eng) use ($dateFrom, $dateTo) {
                $jobs = Service::query()
                    ->where('engineer_id', $eng->id)
                    ->when($dateFrom, fn ($q) => $q->whereDate('visit_date', '>=', $dateFrom))
                    ->when($dateTo, fn ($q) => $q->whereDate('visit_date', '<=', $dateTo))
                    ->get();

                $completed = $jobs->where('status', 'completed')->count();
                $scheduled = $jobs->where('status', 'scheduled')->count();
                $inProgress = $jobs->where('status', 'in-progress')->count();

                return [
                    'report' => 'Engineer Performance',
                    'customer' => $eng->name,
                    'reference' => "{$completed} completed / {$scheduled} scheduled",
                    'date' => $eng->last_login_at,
                    'status' => $completed >= $scheduled ? 'completed' : 'scheduled',
                    'area' => "{$inProgress} in progress",
                ];
            });

            return [
                'rows' => $rows->values(),
                'count' => $paginated->total(),
                'pagination' => $paginated,
            ];
        }

        if ($type === 'engineer-activity-log') {
            $query = ActivityLog::with('user')
                ->whereHas('user', fn ($q) => $q->role('engineer'))
                ->when($engineerFilter, fn ($q) => $q->where('user_id', $engineerFilter))
                ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->latest();

            $paginated = $query->paginate(20)->withQueryString();

            $rows = $paginated->map(fn ($r) => [
                'report' => 'Engineer Activity',
                'customer' => $r->user?->name ?? '—',
                'reference' => $r->action,
                'date' => $r->created_at,
                'status' => $r->module ?? 'system',
                'area' => $r->description,
            ]);

            return [
                'rows' => $rows->values(),
                'count' => $paginated->total(),
                'pagination' => $paginated,
            ];
        }

        if ($type === 'area-ppm-schedule') {
            $collection = $scheduleService->upcomingSchedule(3)
                ->when($area, fn ($collection) => $collection->where('area', $area))
                ->when($category === 'chain', fn ($collection) => $collection->filter(fn ($row) => count($row['chain_customers']) > 0))
                ->when($category === 'new', fn ($collection) => $collection->filter(fn ($row) => count($row['new_customers']) > 0));

            $rows = $collection->map(fn ($row) => [
                'report' => $row['week_label'].' · '.$row['area'],
                'customer' => $this->formatScheduleCustomers($row, $category),
                'reference' => $row['ppm_count'].' PPMs',
                'date' => $row['week_range'],
                'status' => 'scheduled',
                'area' => $row['area'],
                'week' => $row['week_label'],
            ]);

            // For collections, use simple pagination
            $page = request()->get('page', 1);
            $perPage = 20;
            $paginatedRows = new \Illuminate\Pagination\LengthAwarePaginator(
                $rows->forPage($page, $perPage),
                $rows->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return [
                'rows' => $paginatedRows->values(),
                'count' => $rows->count(),
                'pagination' => $paginatedRows,
            ];
        }

        $pagination = null;

        $query = match ($type) {
            'upcoming-ppms' => Contract::filter([
                'status' => 'upcoming',
                'area' => $area,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $search,
            ])->when($customerName, fn ($q) => $q->where('customer_name', $customerName))->orderBy('next_ppm_due'),

            'overdue-ppms' => Contract::filter([
                'status' => 'overdue',
                'area' => $area,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $search,
            ])->when($customerName, fn ($q) => $q->where('customer_name', $customerName))->orderBy('next_ppm_due'),

            'expiring-contracts' => Contract::filter([
                'status' => 'expiring',
                'area' => $area,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $search,
            ])->when($customerName, fn ($q) => $q->where('customer_name', $customerName))->orderBy('expiry_date'),

            'customers-no-contract' => Customer::filter([
                'status' => 'nocontract',
                'category' => $category,
                'region' => $area,
                'search' => $search,
            ])->when($customerFilter, fn ($q) => $q->where('id', $customerFilter))->orderBy('name'),

            'customer-responses' => Response::filter([
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ])->when($customerName, fn ($q) => $q->where('customer_name', $customerName))->orderBy('ppm_due'),

            'service-history' => Service::completed()->filter([
                'search' => $search,
                'engineer_id' => $engineerFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ])->when($customerName || $system, fn ($q) => $q->when($customerName, fn ($q) => $q->where('customer_name', $customerName))->when($system, fn ($q) => $q->where('service_type', $system)))->orderBy('visit_date', 'desc'),

            'service-jobs' => Service::filter([
                'search' => $search,
                'engineer_id' => $engineerFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ])->when($customerName || $system, fn ($q) => $q->when($customerName, fn ($q) => $q->where('customer_name', $customerName))->when($system, fn ($q) => $q->where('service_type', $system)))->orderBy('visit_date', 'desc'),

            'customer-register' => Customer::filter([
                'search' => $search,
                'category' => $category,
                'region' => $area,
            ])->when($customerFilter, fn ($q) => $q->where('id', $customerFilter))->orderBy('name'),

            default => Contract::filter([
                'search' => $search,
                'area' => $area,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ])->when($customerName, fn ($q) => $q->where('customer_name', $customerName))->orderBy('next_ppm_due'),
        };

        $paginated = $query->paginate(20)->withQueryString();
        $pagination = $paginated;

        $rows = $paginated->map(fn ($r) => match ($type) {
            'upcoming-ppms' => [
                'report' => 'Upcoming PPM',
                'customer' => $r->customer_name,
                'reference' => $r->contract_ref,
                'date' => $r->next_ppm_due,
                'status' => $r->status,
                'area' => $r->area,
            ],
            'overdue-ppms' => [
                'report' => 'Overdue PPM',
                'customer' => $r->customer_name,
                'reference' => $r->contract_ref,
                'date' => $r->next_ppm_due,
                'status' => $r->status,
                'area' => $r->area,
            ],
            'expiring-contracts' => [
                'report' => 'Expiring Contract',
                'customer' => $r->customer_name,
                'reference' => $r->contract_ref,
                'date' => $r->expiry_date,
                'status' => $r->status,
                'area' => $r->area,
            ],
            'customers-no-contract' => [
                'report' => 'No Contract',
                'customer' => $r->name,
                'reference' => $r->job_ref,
                'date' => $r->completion_date,
                'status' => $r->status,
                'area' => $r->region,
            ],
            'customer-responses' => [
                'report' => 'Customer Response',
                'customer' => $r->customer_name,
                'reference' => $r->site_name,
                'date' => $r->ppm_due,
                'status' => $r->response,
                'area' => Customer::where('name', $r->customer_name)->value('region'),
            ],
            'service-history' => [
                'report' => 'Service History',
                'customer' => $r->customer_name,
                'reference' => $r->job_ref,
                'date' => $r->visit_date,
                'status' => $r->status,
                'area' => Customer::where('name', $r->customer_name)->value('region'),
            ],
            'service-jobs' => [
                'report' => 'Service Job',
                'customer' => $r->customer_name,
                'reference' => $r->job_ref,
                'date' => $r->visit_date,
                'status' => $r->status,
                'area' => Customer::where('name', $r->customer_name)->value('region'),
            ],
            'customer-register' => [
                'report' => 'Customer',
                'customer' => $r->name,
                'reference' => $r->job_ref,
                'date' => $r->completion_date,
                'status' => $r->status,
                'area' => $r->region,
            ],
            default => [
                'report' => 'Contract',
                'customer' => $r->customer_name,
                'reference' => $r->contract_ref,
                'date' => $r->next_ppm_due,
                'status' => $r->status,
                'area' => $r->area,
            ],
        });

        if ($category && in_array($type, ['upcoming-ppms', 'overdue-ppms', 'expiring-contracts', 'contracts-register'], true)) {
            $customerNames = Customer::where('category', $category)->pluck('name');
            $rows = $rows->filter(fn ($row) => $customerNames->contains($row['customer']));
        }

        return [
            'rows' => $rows->values(),
            'count' => $pagination->total(),
            'pagination' => $pagination,
        ];
    }

    private function formatScheduleCustomers(array $row, ?string $category): string
    {
        $parts = [];

        if ($category !== 'new' && count($row['chain_customers'])) {
            $parts[] = implode(', ', $row['chain_customers']);
        }

        if ($category !== 'chain' && count($row['new_customers'])) {
            $parts[] = implode(', ', $row['new_customers']);
        }

        return $parts ? implode(' · ', $parts) : '—';
    }
}
