<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EngineerActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request): View
    {
        $engineerId = $request->get('engineer_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $module = $request->get('module');

        $engineers = User::engineers()->active()->orderBy('name')->get();

        $activityQuery = ActivityLog::with('user')
            ->whereHas('user', fn ($q) => $q->role('engineer'))
            ->when($engineerId, fn ($q) => $q->where('user_id', $engineerId))
            ->when($module, fn ($q) => $q->where('module', $module))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest();

        $activities = (clone $activityQuery)->paginate(20)->withQueryString();

        $serviceStats = Service::query()
            ->when($engineerId, fn ($q) => $q->where('engineer_id', $engineerId))
            ->when($dateFrom, fn ($q) => $q->whereDate('visit_date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('visit_date', '<=', $dateTo))
            ->selectRaw('engineer_id, status, count(*) as total')
            ->whereNotNull('engineer_id')
            ->groupBy('engineer_id', 'status')
            ->get()
            ->groupBy('engineer_id');

        $engineerSummaries = $engineers->map(function (User $engineer) use ($serviceStats, $engineerId) {
            if ($engineerId && $engineer->id != $engineerId) {
                return null;
            }

            $stats = $serviceStats->get($engineer->id, collect());
            $completed = $stats->firstWhere('status', 'completed')?->total ?? 0;
            $scheduled = $stats->firstWhere('status', 'scheduled')?->total ?? 0;
            $inProgress = $stats->firstWhere('status', 'in-progress')?->total ?? 0;

            return [
                'engineer' => $engineer,
                'completed' => $completed,
                'scheduled' => $scheduled,
                'in_progress' => $inProgress,
                'total_jobs' => $completed + $scheduled + $inProgress,
                'last_login' => $engineer->last_login_at,
            ];
        })->filter()->values();

        return view('engineer-activity.index', [
            'engineers' => $engineers,
            'activities' => $activities,
            'engineerSummaries' => $engineerSummaries,
            'filters' => $request->only(['engineer_id', 'date_from', 'date_to', 'module']),
            'modules' => ActivityLog::query()
                ->whereNotNull('module')
                ->distinct()
                ->orderBy('module')
                ->pluck('module'),
        ]);
    }
}
