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
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('door_id')->nullable()->constrained()->onDelete('set null')->after('site_id');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('door_id')->nullable()->constrained()->onDelete('set null')->after('site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['door_id']);
            $table->dropColumn('door_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['door_id']);
            $table->dropColumn('door_id');
        });
    }
};
