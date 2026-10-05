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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('name')->nullable()->after('company_name');
            $table->string('mobile')->nullable()->after('phone');
            $table->string('reference_type')->nullable()->default('Direct')->after('country');
            $table->string('vendor_name')->nullable()->after('reference_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['name', 'mobile', 'reference_type', 'vendor_name']);
        });
    }
};
