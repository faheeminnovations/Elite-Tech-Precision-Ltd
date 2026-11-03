<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;
use App\Models\CustomerSite;
use App\Models\Region;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing comma-separated sites to individual CustomerSite records
        $customers = Customer::all();

        foreach ($customers as $customer) {
            // Skip if customer has no sites
            if (empty($customer->sites)) {
                continue;
            }

            // Get or create region
            $region = null;
            if (!empty($customer->region)) {
                $region = Region::firstOrCreate(
                    ['name' => $customer->region],
                    ['name' => $customer->region]
                );
            }

            // Parse sites - handle both comma-separated strings and JSON arrays
            $sites = [];
            if (is_array($customer->sites)) {
                $sites = $customer->sites;
            } elseif (is_string($customer->sites)) {
                // Split by comma and trim whitespace
                $sites = array_map('trim', explode(',', $customer->sites));
            }

            // Create individual site records
            foreach ($sites as $siteName) {
                if (empty($siteName)) {
                    continue;
                }

                // Check if site already exists for this customer and region
                $existingSite = CustomerSite::where('customer_id', $customer->id)
                    ->where('site_name', $siteName)
                    ->where('region_id', $region ? $region->id : null)
                    ->first();

                if (!$existingSite) {
                    CustomerSite::create([
                        'customer_id' => $customer->id,
                        'region_id' => $region ? $region->id : null,
                        'site_name' => $siteName,
                        'address' => $customer->address, // Use customer address as default
                        'status' => 'active',
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all customer sites created by this migration
        // Note: This is a destructive operation and may not be fully reversible
        CustomerSite::truncate();
    }
};
