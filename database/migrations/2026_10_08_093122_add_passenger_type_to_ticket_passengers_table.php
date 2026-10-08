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
        Schema::table('ticket_passengers', function (Blueprint $table) {
            $table->string('passenger_type')->nullable()->after('passenger_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_passengers', function (Blueprint $table) {
            $table->dropColumn('passenger_type');
        });
    }
};
