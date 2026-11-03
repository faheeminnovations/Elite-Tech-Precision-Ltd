<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Door extends Model
{
    protected $fillable = [
        'customer_site_id',
        'door_name',
        'location_details',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function customerSite(): BelongsTo
    {
        return $this->belongsTo(CustomerSite::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
