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
        // 1. Update b2b_agents table
        Schema::table('b2b_agents', function (Blueprint $table) {
            // we already have name, contact, email
            $table->string('company_name')->nullable()->after('name');
            $table->string('contact_person')->nullable()->after('company_name');
            $table->string('whatsapp')->nullable()->after('contact');
            $table->text('address')->nullable()->after('email');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->text('notes')->nullable()->after('country');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('notes');
        });

        // 2. Update bookings table to support advanced commission types
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('commission_type', ['Fixed', 'Percentage'])->nullable()->after('advance_payment');
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('commission_type');
            // b2b_commission and company_share already exist
            // b2b_commission_status already exists as Pending, Paid. Let's add Partially Paid?
            // Actually enum might fail to alter easily. We will leave it as is, or change it if needed.
        });

        // 3. Create agent_commission_payments table
        Schema::create('agent_commission_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('b2b_agent_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->string('payment_slip_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method');
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('b2b_agent_id')->references('id')->on('b2b_agents')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });

        // 4. Create b2b_invoices table
        Schema::create('b2b_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('b2b_agent_id');
            $table->unsignedBigInteger('booking_id');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->decimal('total_payment_received', 12, 2)->default(0);
            $table->decimal('agent_commission', 12, 2)->default(0);
            $table->decimal('company_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['Pending', 'Partially Paid', 'Paid', 'Cancelled'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('b2b_agent_id')->references('id')->on('b2b_agents')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2b_invoices');
        Schema::dropIfExists('agent_commission_payments');
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_percentage']);
        });

        Schema::table('b2b_agents', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'contact_person',
                'whatsapp',
                'address',
                'city',
                'country',
                'notes',
                'status'
            ]);
        });
    }
};
