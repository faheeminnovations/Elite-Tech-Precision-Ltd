<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('mailing_frequency')->nullable()->after('notes')->comment('Email notification frequency: daily, weekly, monthly, quarterly, yearly');
            $table->date('last_email_sent')->nullable()->after('mailing_frequency')->comment('Last date email was sent');
            $table->date('next_email_due')->nullable()->after('last_email_sent')->comment('Next scheduled email date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['mailing_frequency', 'last_email_sent', 'next_email_due']);
        });
    }
};
