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
        Schema::create('group_flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_group_id')->constrained('travel_groups')->cascadeOnDelete();
            $table->string('flight_type')->default('Departure');
            $table->string('flight_number')->nullable();
            $table->string('sector')->nullable();
            $table->date('departure_date')->nullable();
            $table->string('departure_time')->nullable();
            $table->date('arrival_date')->nullable();
            $table->string('arrival_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_flights');
    }
};
