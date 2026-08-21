<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('site_name')->nullable();
            $table->date('ppm_due')->nullable();
            $table->date('reminder_sent')->nullable();
            $table->string('response')->default('awaiting'); // accepted, declined, awaiting
            $table->date('responded_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
