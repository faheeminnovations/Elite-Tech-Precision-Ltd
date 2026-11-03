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
        Schema::create('doors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_site_id')->constrained()->onDelete('cascade');
            $table->string('door_name');
            $table->text('location_details')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['customer_site_id', 'door_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doors');
    }
};
