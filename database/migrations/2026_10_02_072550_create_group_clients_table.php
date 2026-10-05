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
        Schema::create('group_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_group_id')->constrained('travel_groups')->cascadeOnDelete();
            $table->string('client_name');
            $table->string('father_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('phone')->nullable();
            $table->string('package')->nullable();
            $table->string('pax_type')->default('Adult');
            $table->string('room_type')->nullable();
            $table->string('bed')->nullable();
            $table->string('group_number')->nullable();
            $table->string('visa_number')->nullable();
            $table->string('pnr')->nullable();
            $table->string('payment_status')->default('Pending');
            $table->string('booking_status')->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_clients');
    }
};
