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
        Schema::create('travel_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_id')->unique();
            $table->string('group_name');
            $table->string('service_type');
            $table->string('destination')->nullable();
            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('group_leader')->nullable();
            $table->unsignedBigInteger('assigned_team_member_id')->nullable();
            $table->string('status')->default('Draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_groups');
    }
};
