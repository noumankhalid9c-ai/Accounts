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
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('campaign_type')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('service')->nullable();
            $table->string('destination')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('Draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique()->nullable();
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('cnic')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('preferred_service')->nullable();
            $table->string('preferred_destination')->nullable();
            $table->string('travel_type')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_code')->unique()->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('inquiry_type')->nullable();
            $table->string('service')->nullable();
            $table->string('destination')->nullable();
            $table->date('travel_date')->nullable();
            $table->integer('pax')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('expected_value', 15, 2)->nullable();
            $table->string('source')->nullable();
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->nullOnDelete();
            $table->string('priority')->default('Medium');
            $table->string('stage')->default('New Inquiry');
            $table->string('status')->default('Active');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('crm_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->cascadeOnDelete();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->date('followup_date');
            $table->time('followup_time')->nullable();
            $table->string('type')->nullable();
            $table->text('notes')->nullable();
            $table->string('result')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('activity_type');
            $table->text('description');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_followups');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('marketing_campaigns');
    }
};
