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
            $table->foreignId('region_id')->nullable()->constrained()->onDelete('set null')->after('customer_name');
            $table->foreignId('site_id')->nullable()->constrained('customer_sites')->onDelete('set null')->after('region_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['site_id']);
            $table->dropColumn(['region_id', 'site_id']);
        });
    }
};
