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
        Schema::create('group_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_group_id')->constrained('travel_groups')->cascadeOnDelete();
            $table->date('travel_date')->nullable();
            $table->string('transporter')->nullable();
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->string('pickup_location')->nullable();
            $table->string('drop_location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_transports');
    }
};
