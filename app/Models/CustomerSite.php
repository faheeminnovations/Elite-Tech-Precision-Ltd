<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerSite extends Model
{
    protected $fillable = [
        'customer_id',
        'region_id',
        'site_name',
        'address',
        'contact_person',
        'contact_number',
        'email',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function doors(): HasMany
    {
        return $this->hasMany(Door::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'site_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'site_id');
    }
}
