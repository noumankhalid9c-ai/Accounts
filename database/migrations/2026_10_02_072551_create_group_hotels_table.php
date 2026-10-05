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
        Schema::create('group_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_group_id')->constrained('travel_groups')->cascadeOnDelete();
            $table->string('city')->nullable();
            $table->string('hotel_name')->nullable();
            $table->string('view')->nullable();
            $table->string('meal')->nullable();
            $table->string('confirmation_number')->nullable();
            $table->string('room_type')->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->integer('nights')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_hotels');
    }
};
