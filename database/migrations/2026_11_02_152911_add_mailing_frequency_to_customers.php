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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'mailing_frequency')) {
                $table->string('mailing_frequency')->nullable()->after('category');
            }
            if (!Schema::hasColumn('customers', 'last_email_sent')) {
                $table->date('last_email_sent')->nullable()->after('mailing_frequency');
            }
            if (!Schema::hasColumn('customers', 'next_email_due')) {
                $table->date('next_email_due')->nullable()->after('last_email_sent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['mailing_frequency', 'last_email_sent', 'next_email_due']);
        });
    }
};
