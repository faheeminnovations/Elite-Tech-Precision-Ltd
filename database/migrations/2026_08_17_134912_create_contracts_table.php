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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('site_name')->nullable();
            $table->string('contract_ref')->unique();
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('frequency')->default('6 monthly');
            $table->date('last_ppm_date')->nullable();
            $table->date('next_ppm_due')->nullable();
            $table->decimal('contract_value', 10, 2)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('status')->default('upcoming');
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
