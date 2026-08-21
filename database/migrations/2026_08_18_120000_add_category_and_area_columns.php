<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('category', 20)->default('new')->after('status');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->string('area', 100)->nullable()->after('customer_name');
        });

        // Backfill contract areas from customer regions
        $contracts = DB::table('contracts')->orderBy('id')->get();

        foreach ($contracts as $contract) {
            $area = DB::table('customers')
                ->where('name', $contract->customer_name)
                ->value('region');

            if ($area) {
                DB::table('contracts')->where('id', $contract->id)->update(['area' => $area]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('area');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
