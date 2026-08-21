<?php

namespace App\Models;

use App\Models\Concerns\HasListingFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasListingFilters;

    public const STATUSES = [
        'scheduled' => 'Scheduled',
        'in-progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const SERVICE_TYPES = [
        'PPM' => 'PPM',
        'Repair' => 'Repair',
        'Installation' => 'Installation',
        'Service Call' => 'Service Call',
        'Inspection' => 'Inspection',
    ];

    protected $fillable = [
        'job_ref',
        'customer_name',
        'area',
        'service_type',
        'engineer_name',
        'engineer_id',
        'visit_date',
        'status',
        'site_installation',
        'address',
        'job_details',
        'work_completed',
        'job_notes',
        'recommendations',
        'remedial_required',
        'remedial_details',
        'next_ppm_due',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'next_ppm_due' => 'date',
    ];

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $this->applySearch($query, $filters['search'] ?? null, [
            'job_ref',
            'customer_name',
            'site_installation',
            'address',
            'engineer_name',
            'job_details',
            'job_notes',
        ]);

        $this->applyExactFilter($query, 'status', $filters['status'] ?? null);
        $this->applyExactFilter($query, 'service_type', $filters['service_type'] ?? null);
        $this->applyExactFilter($query, 'area', $filters['area'] ?? null);
        $this->applyDateRange($query, 'visit_date', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        if (! empty($filters['engineer_id'])) {
            $query->where('engineer_id', $filters['engineer_id']);
        } elseif (! empty($filters['engineer'])) {
            $query->where('engineer_name', $filters['engineer']);
        }

        return $query;
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }
}
