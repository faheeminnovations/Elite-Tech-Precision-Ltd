<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Response;
use App\Models\Service;
use App\Models\User;
use App\Services\AreaScheduleService;
use App\Services\CsvExporter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function customers(Request $request): StreamedResponse
    {
        $customers = Customer::query()
            ->filter($request->only(['search', 'status', 'category', 'region']))
            ->latest()
            ->get();

        return CsvExporter::download('customers-' . now()->format('Y-m-d') . '.csv', [
            'Name',
            'Category',
            'Job Ref',
            'Job Details',
            'Completion Date',
            'Address',
            'Phone',
            'Email',
            'Status',
            'Area',
            'Job Notes',
        ], $customers->map(fn (Customer $c) => [
            $c->name,
            Customer::CATEGORIES[$c->category] ?? $c->category,
            $c->job_ref,
            $c->job_details,
            CsvExporter::formatDate($c->completion_date),
            $c->address,
            $c->phone,
            $c->email,
            $c->status,
            $c->region,
            $c->job_notes,
        ]));
    }

    public function contracts(Request $request): StreamedResponse
    {
        $contracts = Contract::query()
            ->filter($request->only(['search', 'status', 'area', 'frequency', 'date_from', 'date_to']))
            ->orderBy('next_ppm_due')
            ->get();

        return CsvExporter::download('contracts-' . now()->format('Y-m-d') . '.csv', [
            'Customer',
            'Area',
            'Site',
            'Contract Ref',
            'Start Date',
            'Expiry Date',
            'Frequency',
            'Last PPM',
            'Next PPM Due',
            'Contract Value',
            'Email',
            'Status',
            'Notes',
        ], $contracts->map(fn (Contract $c) => [
            $c->customer_name,
            $c->area,
            $c->site_name,
            $c->contract_ref,
            CsvExporter::formatDate($c->start_date),
            CsvExporter::formatDate($c->expiry_date),
            $c->frequency,
            CsvExporter::formatDate($c->last_ppm_date),
            CsvExporter::formatDate($c->next_ppm_due),
            $c->contract_value,
            $c->customer_email,
            $c->status,
            $c->notes,
        ]));
    }

    public function services(Request $request): StreamedResponse
    {
        $services = Service::query()
            ->filter($request->only(['search', 'status', 'engineer_id', 'service_type', 'date_from', 'date_to']))
            ->latest()
            ->get();

        return CsvExporter::download('services-' . now()->format('Y-m-d') . '.csv', [
            'Job Ref',
            'Customer',
            'Service Type',
            'Engineer',
            'Visit Date',
            'Status',
            'Site',
            'Address',
            'Job Details',
            'Work Completed',
            'Recommendations',
            'Remedial Required',
            'Next PPM Due',
            'Notes',
        ], $services->map(fn (Service $s) => [
            $s->job_ref,
            $s->customer_name,
            $s->service_type,
            $s->engineer_name,
            CsvExporter::formatDate($s->visit_date),
            $s->status,
            $s->site_installation,
            $s->address,
            $s->job_details,
            $s->work_completed,
            $s->recommendations,
            $s->remedial_required,
            CsvExporter::formatDate($s->next_ppm_due),
            $s->job_notes,
        ]));
    }

    public function serviceHistory(Request $request): StreamedResponse
    {
        $request->merge(['status' => 'completed']);

        $services = Service::query()
            ->completed()
            ->filter($request->only(['search', 'engineer_id', 'date_from', 'date_to']))
            ->latest('visit_date')
            ->get();

        return CsvExporter::download('service-history-' . now()->format('Y-m-d') . '.csv', [
            'Job Ref',
            'Customer',
            'Site',
            'Engineer',
            'Completed Date',
            'Service Type',
            'Work Completed',
            'Recommendations',
            'Notes',
        ], $services->map(fn (Service $s) => [
            $s->job_ref,
            $s->customer_name,
            $s->site_installation,
            $s->engineer_name,
            CsvExporter::formatDate($s->visit_date),
            $s->service_type,
            $s->work_completed,
            $s->recommendations,
            $s->job_notes,
        ]));
    }

    public function responses(Request $request): StreamedResponse
    {
        $responses = Response::query()
            ->filter($request->only(['search', 'response', 'date_from', 'date_to']))
            ->when($request->get('search'), fn ($q) => $q->where('customer_name', $request->get('search')))
            ->latest()
            ->get();

        return CsvExporter::download('customer-responses-' . now()->format('Y-m-d') . '.csv', [
            'Customer',
            'Site',
            'PPM Due',
            'Reminder Sent',
            'Response',
            'Responded On',
            'Notes',
        ], $responses->map(fn (Response $r) => [
            $r->customer_name,
            $r->site_name,
            CsvExporter::formatDate($r->ppm_due),
            CsvExporter::formatDate($r->reminder_sent),
            $r->response,
            CsvExporter::formatDate($r->responded_on),
            $r->notes,
        ]));
    }

    public function reports(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'area-ppm-schedule');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $customer = $request->get('customer');
        $engineer = $request->get('engineer');
        $area = $request->get('area');
        $category = $request->get('category');

        // Resolve customer ID to name for filters
        $customerName = $this->resolveCustomerFilter($customer);

        // Resolve engineer filter
        $engineerId = null;
        if ($engineer && $engineer !== 'All' && is_numeric($engineer)) {
            $engineerId = (int) $engineer;
        }

        return match ($type) {
            'area-ppm-schedule' => $this->exportAreaSchedule($area, $category),

            'upcoming-ppms' => $this->exportContracts([
                'status' => 'upcoming',
                'area' => $area,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $customerName,
            ], 'upcoming-ppms', $category),

            'overdue-ppms' => $this->exportContracts([
                'status' => 'overdue',
                'area' => $area,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $customerName,
            ], 'overdue-ppms', $category),

            'expiring-contracts' => $this->exportContracts([
                'status' => 'expiring',
                'area' => $area,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $customerName,
            ], 'expiring-contracts', $category),

            'customers-no-contract' => $this->customers(new Request([
                'status' => 'nocontract',
                'category' => $category,
                'region' => $area,
                'search' => $customerName,
            ])),

            'customer-responses' => $this->responses(new Request([
                'search' => $customerName,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ])),

            'service-history' => $this->serviceHistory(new Request([
                'search' => $customerName,
                'engineer_id' => $engineerId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ])),

            'service-jobs' => $this->services(new Request([
                'search' => $customerName,
                'engineer_id' => $engineerId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ])),

            'contracts-register' => $this->contracts(new Request([
                'search' => $customerName,
                'area' => $area,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ])),

            'customer-register' => $this->customers(new Request([
                'search' => $customerName,
                'category' => $category,
                'region' => $area,
            ])),

            'engineer-performance' => $this->exportEngineerPerformance($engineerId, $dateFrom, $dateTo),

            'engineer-activity-log' => $this->exportEngineerActivityLog($engineerId, $dateFrom, $dateTo),

            default => $this->customers(new Request([
                'search' => $customerName,
                'category' => $category,
                'region' => $area,
            ])),
        };
    }

    private function resolveCustomerFilter(?string $customer): ?string
    {
        if (! $customer || $customer === '') {
            return null;
        }

        if (is_numeric($customer)) {
            return Customer::where('id', $customer)->value('name');
        }

        return $customer;
    }

    private function exportEngineerPerformance(?int $engineerId, ?string $dateFrom, ?string $dateTo): StreamedResponse
    {
        $engineers = User::engineers()->active()
            ->when($engineerId, fn ($q) => $q->where('id', $engineerId))
            ->orderBy('name')
            ->get();

        $rows = $engineers->map(function (User $eng) use ($dateFrom, $dateTo) {
            $jobs = Service::query()
                ->where('engineer_id', $eng->id)
                ->when($dateFrom, fn ($q) => $q->whereDate('visit_date', '>=', $dateFrom))
                ->when($dateTo, fn ($q) => $q->whereDate('visit_date', '<=', $dateTo))
                ->get();

            $completed = $jobs->where('status', 'completed')->count();
            $scheduled = $jobs->where('status', 'scheduled')->count();
            $inProgress = $jobs->where('status', 'in-progress')->count();

            return [
                'Engineer' => $eng->name,
                'Completed' => $completed,
                'Scheduled' => $scheduled,
                'In Progress' => $inProgress,
                'Total Jobs' => $jobs->count(),
                'Last Login' => $eng->last_login_at ? $eng->last_login_at->format('d/m/Y') : 'Never',
            ];
        });

        return CsvExporter::download('engineer-performance-' . now()->format('Y-m-d') . '.csv', [
            'Engineer',
            'Completed',
            'Scheduled',
            'In Progress',
            'Total Jobs',
            'Last Login',
        ], $rows);
    }

    private function exportEngineerActivityLog(?int $engineerId, ?string $dateFrom, ?string $dateTo): StreamedResponse
    {
        $logs = ActivityLog::with('user')
            ->whereHas('user', fn ($q) => $q->role('engineer'))
            ->when($engineerId, fn ($q) => $q->where('user_id', $engineerId))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->limit(100)
            ->get();

        return CsvExporter::download('engineer-activity-log-' . now()->format('Y-m-d') . '.csv', [
            'Engineer',
            'Action',
            'Module',
            'Description',
            'Date',
        ], $logs->map(fn ($log) => [
            $log->user?->name ?? '—',
            $log->action,
            $log->module ?? 'system',
            $log->description,
            $log->created_at->format('d/m/Y H:i'),
        ]));
    }

    private function exportAreaSchedule(?string $area, ?string $category): StreamedResponse
    {
        $schedule = app(AreaScheduleService::class)->upcomingSchedule(3)
            ->when($area, fn ($collection) => $collection->where('area', $area))
            ->when($category === 'chain', fn ($collection) => $collection->filter(fn ($row) => count($row['chain_customers']) > 0))
            ->when($category === 'new', fn ($collection) => $collection->filter(fn ($row) => count($row['new_customers']) > 0));

        return CsvExporter::download('area-ppm-schedule-' . now()->format('Y-m-d') . '.csv', [
            'Week',
            'Week Range',
            'Area',
            'Chain Customers',
            'New Customers',
            'PPM Count',
        ], $schedule->map(fn (array $row) => [
            $row['week_label'],
            $row['week_range'],
            $row['area'],
            implode(', ', $row['chain_customers']),
            implode(', ', $row['new_customers']),
            $row['ppm_count'],
        ]));
    }

    private function exportContracts(array $filters, string $slug, ?string $category = null): StreamedResponse
    {
        $contracts = Contract::query()
            ->filter($filters)
            ->orderBy('next_ppm_due')
            ->get();

        if ($category) {
            $customerNames = Customer::where('category', $category)->pluck('name');
            $contracts = $contracts->filter(fn (Contract $c) => $customerNames->contains($c->customer_name));
        }

        return CsvExporter::download($slug . '-' . now()->format('Y-m-d') . '.csv', [
            'Customer',
            'Area',
            'Site',
            'Contract Ref',
            'Next PPM Due',
            'Expiry Date',
            'Frequency',
            'Status',
            'Contract Value',
        ], $contracts->map(fn (Contract $c) => [
            $c->customer_name,
            $c->area,
            $c->site_name,
            $c->contract_ref,
            CsvExporter::formatDate($c->next_ppm_due),
            CsvExporter::formatDate($c->expiry_date),
            $c->frequency,
            $c->status,
            $c->contract_value,
        ]));
    }
}
