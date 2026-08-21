<?php

namespace App\Models;

use App\Models\Concerns\HasListingFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, HasListingFilters;

    public const CATEGORIES = [
        'chain' => 'Chain Customer',
        'new' => 'New Customer',
    ];

    public const AREAS = [
        'Dublin',
        'Cork',
        'Galway',
        'Limerick',
        'Waterford',
        'Kilkenny',
        'Wexford',
        'Carlow',
        'Kildare',
        'Meath',
        'Wicklow',
        'Laois',
        'Offaly',
        'Westmeath',
        'Longford',
        'Louth',
        'Cavan',
        'Monaghan',
        'Donegal',
        'Sligo',
        'Leitrim',
        'Roscommon',
        'Mayo',
        'Clare',
        'Tipperary',
        'Kerry',
    ];

    public const STATUSES = [
        'active' => 'Active',
        'nocontract' => 'No Contract',
        'upcoming' => 'Upcoming',
        'overdue' => 'Overdue',
        'expiring' => 'Expiring',
        'new' => 'New',
        'previous' => 'Previous',
        'updated' => 'Updated',
    ];

    protected $fillable = [
        'name',
        'job_ref',
        'job_details',
        'completion_date',
        'address',
        'phone',
        'email',
        'job_notes',
        'status',
        'category',
        'region',
    ];

    protected $casts = [
        'completion_date' => 'date',
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $this->applySearch($query, $filters['search'] ?? null, [
            'name',
            'job_ref',
            'email',
            'phone',
            'address',
            'job_details',
            'job_notes',
        ]);

        $this->applyExactFilter($query, 'status', $filters['status'] ?? null);
        $this->applyExactFilter($query, 'category', $filters['category'] ?? null);
        $this->applyExactFilter($query, 'region', $filters['region'] ?? null);

        return $query;
    }
}
