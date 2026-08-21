<?php

namespace App\Models;

use App\Models\Concerns\HasListingFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasListingFilters;

    public const RESPONSE_TYPES = [
        'accepted' => 'Accepted',
        'declined' => 'Declined',
        'no_response' => 'No Response',
    ];

    public const LEGACY_RESPONSE_TYPES = [
        'awaiting' => 'No Response',
    ];

    protected $fillable = [
        'ppm_reference',
        'contract_ref',
        'job_ref',
        'customer_name',
        'site_name',
        'ppm_due',
        'reminder_sent',
        'response',
        'responded_on',
        'response_time',
        'notes',
        'internal_notification_sent',
    ];

    protected $casts = [
        'ppm_due' => 'date',
        'reminder_sent' => 'date',
        'responded_on' => 'date',
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $this->applySearch($query, $filters['search'] ?? null, [
            'ppm_reference',
            'contract_ref',
            'job_ref',
            'customer_name',
            'site_name',
            'notes',
        ]);

        $this->applyExactFilter($query, 'response', $filters['response'] ?? null);
        $this->applyDateRange($query, 'ppm_due', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        return $query;
    }
}
