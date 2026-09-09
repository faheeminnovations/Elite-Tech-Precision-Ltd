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
        Schema::create('email_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('email_type')->unique();
            $table->string('display_name');
            $table->string('description');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        // Insert default email notification types
        DB::table('email_notification_settings')->insert([
            [
                'email_type' => 'contract_created',
                'display_name' => 'Contract Created',
                'description' => 'Send email when a new contract is created',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'contract_updated',
                'display_name' => 'Contract Updated',
                'description' => 'Send email when a contract is updated',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'contract_deleted',
                'display_name' => 'Contract Deleted',
                'description' => 'Send email when a contract is deleted',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'contract_status_changed',
                'display_name' => 'Contract Status Changed',
                'description' => 'Send email when contract status changes',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'customer_created',
                'display_name' => 'Customer Created',
                'description' => 'Send email when a new customer is created',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'customer_updated',
                'display_name' => 'Customer Updated',
                'description' => 'Send email when a customer is updated',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'customer_deleted',
                'display_name' => 'Customer Deleted',
                'description' => 'Send email when a customer is deleted',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'customer_status_changed',
                'display_name' => 'Customer Status Changed',
                'description' => 'Send email when customer status changes',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'service_created',
                'display_name' => 'Service Created',
                'description' => 'Send email when a new service is created',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'service_updated',
                'display_name' => 'Service Updated',
                'description' => 'Send email when a service is updated',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'service_deleted',
                'display_name' => 'Service Deleted',
                'description' => 'Send email when a service is deleted',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'response_created',
                'display_name' => 'Response Created',
                'description' => 'Send email when a new response is created',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'response_updated',
                'display_name' => 'Response Updated',
                'description' => 'Send email when a response is updated',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'response_deleted',
                'display_name' => 'Response Deleted',
                'description' => 'Send email when a response is deleted',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'response_status_changed',
                'display_name' => 'Response Status Changed',
                'description' => 'Send email when response status changes',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'customer_ppm_reminder',
                'display_name' => 'Customer PPM Reminder',
                'description' => 'Send PPM reminder emails to customers',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'internal_ppm_reminder',
                'display_name' => 'Internal PPM Reminder',
                'description' => 'Send PPM reminder emails to internal team',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'contract_expiry_reminder',
                'display_name' => 'Contract Expiry Reminder',
                'description' => 'Send contract expiry reminder emails',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'service_due_reminder',
                'display_name' => 'Service Due Reminder',
                'description' => 'Send service due reminder emails',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'overdue_contract_reminder',
                'display_name' => 'Overdue Contract Reminder',
                'description' => 'Send overdue contract reminder emails',
                'is_enabled' => true,
            ],
            [
                'email_type' => 'response_pending_reminder',
                'display_name' => 'Response Pending Reminder',
                'description' => 'Send response pending reminder emails',
                'is_enabled' => true,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notification_settings');
    }
};
