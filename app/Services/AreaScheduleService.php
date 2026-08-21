<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AreaScheduleService
{
    public function upcomingSchedule(int $weeks = 3): Collection
    {
        $start = now()->startOfDay();
        $end = now()->addWeeks($weeks)->endOfDay();

        $customers = Customer::query()
            ->get(['name', 'category', 'region'])
            ->keyBy('name');

        $contracts = Contract::query()
            ->whereNotNull('next_ppm_due')
            ->whereBetween('next_ppm_due', [$start, $end])
            ->whereIn('status', ['upcoming', 'overdue', 'expiring', 'active'])
            ->orderBy('next_ppm_due')
            ->get();

        return $contracts
            ->groupBy(fn (Contract $contract) => $contract->next_ppm_due->isoWeek().'-'.$contract->next_ppm_due->isoWeekYear())
            ->map(function (Collection $weekContracts, string $weekKey) use ($customers) {
                [$week, $year] = array_map('intval', explode('-', $weekKey));
                $weekStart = Carbon::now()->setISODate($year, $week)->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();

                return $weekContracts
                    ->groupBy(fn (Contract $contract) => $contract->area ?: 'Unassigned')
                    ->map(function (Collection $areaContracts, string $area) use ($week, $weekStart, $weekEnd, $customers) {
                        $chainCustomers = [];
                        $newCustomers = [];

                        foreach ($areaContracts->unique('customer_name') as $contract) {
                            $customer = $customers->get($contract->customer_name);
                            $category = $customer?->category ?? 'new';

                            if ($category === 'chain') {
                                $chainCustomers[] = $contract->customer_name;
                            } else {
                                $newCustomers[] = $contract->customer_name;
                            }
                        }

                        return [
                            'week' => $week,
                            'week_label' => 'Week '.$week,
                            'week_range' => $weekStart->format('d M').' – '.$weekEnd->format('d M Y'),
                            'area' => $area,
                            'chain_customers' => array_values(array_unique($chainCustomers)),
                            'new_customers' => array_values(array_unique($newCustomers)),
                            'ppm_count' => $areaContracts->count(),
                            'contracts' => $areaContracts,
                        ];
                    })
                    ->sortBy('area')
                    ->values();
            })
            ->sortKeys()
            ->flatten(1)
            ->values();
    }

    public function areaSummary(int $weeks = 3): Collection
    {
        return $this->upcomingSchedule($weeks)
            ->groupBy('area')
            ->map(fn (Collection $items, string $area) => [
                'area' => $area,
                'ppm_count' => $items->sum('ppm_count'),
                'weeks' => $items->pluck('week')->unique()->sort()->values(),
            ])
            ->sortBy('area')
            ->values();
    }
}
