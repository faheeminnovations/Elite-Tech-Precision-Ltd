<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'job_ref')) {
                $table->string('job_ref')->nullable()->after('name');
            }
            if (!Schema::hasColumn('customers', 'job_details')) {
                $table->text('job_details')->nullable()->after('job_ref');
            }
            if (!Schema::hasColumn('customers', 'completion_date')) {
                $table->date('completion_date')->nullable()->after('job_details');
            }
            if (!Schema::hasColumn('customers', 'address')) {
                $table->text('address')->nullable()->after('completion_date');
            }
            if (!Schema::hasColumn('customers', 'phone')) {
                $table->string('phone')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'email')) {
                $table->string('email')->nullable()->unique()->after('phone');
            }
            if (!Schema::hasColumn('customers', 'job_notes')) {
                $table->text('job_notes')->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'status')) {
                $table->string('status')->default('new')->after('job_notes');
            }
            if (!Schema::hasColumn('customers', 'region')) {
                $table->string('region')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['job_ref', 'job_details', 'completion_date', 'address', 'phone', 'email', 'job_notes', 'status', 'region']);
        });
    }
};
