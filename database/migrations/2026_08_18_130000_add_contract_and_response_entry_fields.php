<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('contract_available')->default('yes')->after('id');
            $table->string('job_ref')->nullable()->after('customer_name');
            $table->string('customer_contact')->nullable()->after('customer_email');
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->string('ppm_reference')->nullable()->after('id');
            $table->string('contract_ref')->nullable()->after('ppm_reference');
            $table->string('job_ref')->nullable()->after('contract_ref');
            $table->time('response_time')->nullable()->after('responded_on');
            $table->string('internal_notification_sent')->default('no')->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['contract_available', 'job_ref', 'customer_contact']);
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->dropColumn(['ppm_reference', 'contract_ref', 'job_ref', 'response_time', 'internal_notification_sent']);
        });
    }
};
