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
        Schema::create('travel_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_group_id')->constrained('travel_groups')->cascadeOnDelete();
            $table->string('voucher_number')->unique();
            $table->string('voucher_type')->default('Complete Travel Voucher');
            $table->date('voucher_date')->nullable();
            $table->string('status')->default('Draft');
            $table->string('group_head')->nullable();
            $table->string('package_number')->nullable();
            $table->integer('pax')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('special_instructions')->nullable();
            $table->string('qr_token')->unique()->nullable();
            $table->json('snapshot_data')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_vouchers');
    }
};
