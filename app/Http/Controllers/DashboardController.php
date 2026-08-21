<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Contract;
use App\Models\Service;
use App\Models\Response;
use App\Services\AreaScheduleService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(AreaScheduleService $scheduleService): View
    {
        $upcomingPpms = Contract::where('status', 'upcoming')->count();
        $overduePpms = Contract::where('status', 'overdue')->count();
        $expiringContracts = Contract::where('status', 'expiring')->count();
        $noContract = Customer::where('status', 'nocontract')->count();

        $totalCustomers = Customer::count();
        $totalContracts = Contract::count();
        $totalServices = Service::count();

        $pipelineScheduled = Contract::count();
        $pipelineReminderSent = Contract::whereIn('status', ['upcoming', 'overdue', 'expiring'])->count();
        $pipelineAccepted = Response::where('response', 'accepted')->count();
        $pipelineOverdue = Contract::where('status', 'overdue')->count();
        $pipelineCompletedYtd = Service::where('status', 'completed')->count();

        $upcomingContracts = Contract::whereIn('status', ['upcoming', 'overdue'])
            ->orderBy('next_ppm_due')
            ->limit(5)
            ->get();

        $recentResponses = Response::latest()->limit(4)->get();

        $reminderInternal = 18;
        $reminderCustomer = 18;
        $reminderResponded = Response::where('response', '!=', 'awaiting')->count();

        $areaSchedule = $scheduleService->upcomingSchedule(3);
        $areaSummary = $areaSchedule->groupBy('area')->map(fn ($items, $area) => [
            'area' => $area,
            'ppm_count' => $items->sum('ppm_count'),
        ])->sortBy('area')->values();

        $chainCustomers = Customer::where('category', 'chain')->count();
        $newCustomers = Customer::where('category', 'new')->count();

        return view('dashboard', compact(
            'upcomingPpms',
            'overduePpms',
            'expiringContracts',
            'noContract',
            'totalCustomers',
            'totalContracts',
            'totalServices',
            'pipelineScheduled',
            'pipelineReminderSent',
            'pipelineAccepted',
            'pipelineOverdue',
            'pipelineCompletedYtd',
            'upcomingContracts',
            'recentResponses',
            'reminderInternal',
            'reminderCustomer',
            'reminderResponded',
            'areaSchedule',
            'areaSummary',
            'chainCustomers',
            'newCustomers',
        ));
    }
}
