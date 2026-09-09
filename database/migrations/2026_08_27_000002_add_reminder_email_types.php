<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert reminder email notification types
        DB::table('email_notification_settings')->insertOrIgnore([
            [
                'email_type' => 'customer_ppm_reminder',
                'display_name' => 'Customer PPM Reminder',
                'description' => 'Send reminder emails to customers before their PPM is due',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'internal_ppm_reminder',
                'display_name' => 'Internal PPM Reminder',
                'description' => 'Send internal reminder emails about upcoming PPMs',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'contract_expiry_reminder',
                'display_name' => 'Contract Expiry Reminder',
                'description' => 'Send reminder emails before contracts expire',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'service_due_reminder',
                'display_name' => 'Service Due Reminder',
                'description' => 'Send reminder emails when services are due',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'overdue_contract_reminder',
                'display_name' => 'Overdue Contract Reminder',
                'description' => 'Send reminder emails for overdue contracts',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'response_pending_reminder',
                'display_name' => 'Response Pending Reminder',
                'description' => 'Send reminder emails for pending customer responses',
                'is_enabled' => true,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove reminder email notification types
        DB::table('email_notification_settings')->whereIn('email_type', [
            'customer_ppm_reminder',
            'internal_ppm_reminder',
            'contract_expiry_reminder',
            'service_due_reminder',
            'overdue_contract_reminder',
            'response_pending_reminder',
        ])->delete();
    }
};
