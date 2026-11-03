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
        Schema::table('customer_sites', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_sites', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customer_sites', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('contact_person');
            }
            if (!Schema::hasColumn('customer_sites', 'email')) {
                $table->string('email')->nullable()->after('contact_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_sites', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'contact_number', 'email']);
        });
    }
};
