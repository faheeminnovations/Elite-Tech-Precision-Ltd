<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailNotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_type',
        'display_name',
        'description',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Check if a specific email type is enabled
     */
    public static function isEnabled(string $emailType): bool
    {
        $setting = self::where('email_type', $emailType)->first();
        return $setting ? $setting->is_enabled : false;
    }

    /**
     * Enable a specific email type
     */
    public static function enable(string $emailType): void
    {
        self::where('email_type', $emailType)->update(['is_enabled' => true]);
    }

    /**
     * Disable a specific email type
     */
    public static function disable(string $emailType): void
    {
        self::where('email_type', $emailType)->update(['is_enabled' => false]);
    }

    /**
     * Get all enabled email types
     */
    public static function getEnabledTypes(): array
    {
        return self::where('is_enabled', true)->pluck('email_type')->toArray();
    }

    /**
     * Get all email types grouped by category
     */
    public static function getByCategory(): array
    {
        $all = self::all();
        
        return [
            'contract' => $all->filter(fn($item) => str_starts_with($item->email_type, 'contract_')),
            'customer' => $all->filter(fn($item) => str_starts_with($item->email_type, 'customer_')),
            'service' => $all->filter(fn($item) => str_starts_with($item->email_type, 'service_')),
            'response' => $all->filter(fn($item) => str_starts_with($item->email_type, 'response_')),
            'reminder' => $all->filter(fn($item) => str_contains($item->email_type, 'reminder')),
        ];
    }
}
