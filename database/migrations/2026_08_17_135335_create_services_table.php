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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('job_ref')->unique();
            $table->string('customer_name');
            $table->string('service_type')->default('PPM'); // PPM, Repair, Installation, Service Call, Inspection
            $table->string('engineer_name')->nullable();
            $table->date('visit_date')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, in-progress, completed, cancelled
            $table->string('site_installation')->nullable();
            $table->text('address')->nullable();
            $table->longText('job_details')->nullable();
            $table->longText('work_completed')->nullable();
            $table->text('job_notes')->nullable();
            $table->longText('recommendations')->nullable();
            $table->string('remedial_required')->default('no'); // yes, no
            $table->longText('remedial_details')->nullable();
            $table->date('next_ppm_due')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
