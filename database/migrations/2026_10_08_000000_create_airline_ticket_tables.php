<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('logo')->nullable();
            $table->string('contact')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iata_code')->unique();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('airline_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('b2b_agent_id')->nullable()->constrained('b2b_agents')->onDelete('set null');
            $table->string('pnr')->nullable();
            $table->string('booking_reference')->nullable();
            $table->date('ticket_date');
            $table->string('ticket_type')->nullable(); // One Way, Round Trip, Multi City
            $table->string('route_type')->nullable(); // Direct, Connecting, Multi-City
            
            $table->decimal('base_fare', 15, 2)->default(0);
            $table->decimal('taxes', 15, 2)->default(0);
            $table->decimal('airline_charges', 15, 2)->default(0);
            $table->decimal('service_charges', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('other_charges', 15, 2)->default(0);
            $table->decimal('total_fare', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('amount_pending', 15, 2)->default(0);
            
            $table->string('payment_status')->default('Pending'); // Pending, Partial, Paid
            $table->string('ticket_status')->default('Reserved'); // Reserved, Confirmed, Issued, Cancelled, Refunded, Reissued
            
            $table->text('notes')->nullable();
            $table->string('qr_token')->nullable()->unique();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_flight_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('airline_tickets')->onDelete('cascade');
            $table->foreignId('airline_id')->constrained('airlines');
            $table->string('flight_number');
            $table->foreignId('departure_airport_id')->constrained('airports');
            $table->foreignId('arrival_airport_id')->constrained('airports');
            $table->date('departure_date');
            $table->time('departure_time');
            $table->date('arrival_date');
            $table->time('arrival_time');
            $table->string('terminal')->nullable();
            $table->string('cabin_class')->nullable();
            $table->string('baggage_allowance')->nullable();
            $table->string('meal')->nullable();
            $table->string('seat')->nullable();
            $table->string('flight_status')->nullable();
            $table->integer('segment_order')->default(0);
            $table->timestamps();
        });

        Schema::create('ticket_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('airline_tickets')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('passenger_name');
            $table->string('title')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('nationality')->nullable();
            $table->string('ticket_number')->nullable();
            $table->string('frequent_flyer_number')->nullable();
            $table->string('seat')->nullable();
            $table->string('baggage')->nullable();
            $table->string('meal')->nullable();
            $table->string('special_request')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('airline_tickets')->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('airline_tickets')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_invoices');
        Schema::dropIfExists('ticket_payments');
        Schema::dropIfExists('ticket_passengers');
        Schema::dropIfExists('ticket_flight_segments');
        Schema::dropIfExists('airline_tickets');
        Schema::dropIfExists('airports');
        Schema::dropIfExists('airlines');
    }
};
