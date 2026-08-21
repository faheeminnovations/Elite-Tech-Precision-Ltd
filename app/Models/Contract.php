<?php

namespace App\Models;

use App\Models\Concerns\HasListingFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasListingFilters;

    public const STATUSES = [
        'active' => 'Active',
        'expiring' => 'Expiring',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled',
    ];

    public const LEGACY_STATUSES = [
        'upcoming' => 'Upcoming',
        'overdue' => 'Overdue',
    ];

    public const FREQUENCIES = [
        '3 Monthly' => '3 Monthly',
        '6 Monthly' => '6 Monthly',
        'Annual' => 'Annual',
        'Custom' => 'Custom',
    ];

    public const LEGACY_FREQUENCIES = [
        'Quarterly' => '3 Monthly',
        '6 monthly' => '6 Monthly',
    ];

    protected $fillable = [
        'contract_available',
        'customer_name',
        'job_ref',
        'area',
        'site_name',
        'contract_ref',
        'start_date',
        'expiry_date',
        'frequency',
        'last_ppm_date',
        'next_ppm_due',
        'contract_value',
        'customer_contact',
        'customer_email',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'last_ppm_date' => 'date',
        'next_ppm_due' => 'date',
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $this->applySearch($query, $filters['search'] ?? null, [
            'customer_name',
            'job_ref',
            'site_name',
            'contract_ref',
            'customer_contact',
            'customer_email',
            'notes',
        ]);

        $this->applyExactFilter($query, 'status', $filters['status'] ?? null);
        $this->applyExactFilter($query, 'area', $filters['area'] ?? null);
        $this->applyExactFilter($query, 'frequency', $filters['frequency'] ?? null);
        $this->applyDateRange($query, 'next_ppm_due', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        return $query;
    }
}
