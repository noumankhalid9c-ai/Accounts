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
        // Add Travel Agency Specific fields to Clients table
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'cnic')) {
                $table->string('cnic')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('clients', 'city')) {
                $table->string('city')->nullable()->after('cnic');
            }
        });

        // Create B2B Agents table
        Schema::create('b2b_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // Create Bookings table (Client Data Record)
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->unsignedBigInteger('b2b_agent_id')->nullable();
            $table->integer('assigned_user_id')->nullable(); // Sales Executive
            $table->string('service_taken'); // Visa, Ticket, Tour
            $table->text('package_details')->nullable();
            $table->date('booking_date');
            $table->date('travel_date')->nullable();
            
            // Financials
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('advance_payment', 12, 2)->default(0);
            $table->decimal('b2b_commission', 12, 2)->default(0);
            $table->decimal('company_share', 12, 2)->default(0); // auto-calculated
            
            $table->enum('payment_status', ['Pending', 'Partial', 'Paid'])->default('Pending');
            $table->enum('b2b_commission_status', ['Pending', 'Paid'])->default('Pending');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('b2b_agent_id')->references('id')->on('b2b_agents')->onDelete('set null');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Create Employees (Team Salary) table
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->string('contact')->nullable();
            $table->timestamps();
        });

        // Create Salaries table
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('salary_month');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('sales_commission', 10, 2)->default(0);
            $table->decimal('incentive', 10, 2)->default(0);
            $table->decimal('advance_salary', 10, 2)->default(0);
            $table->decimal('deduction', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0); // basic + commission + incentive - advance - deduction
            $table->enum('status', ['Pending', 'Paid'])->default('Pending');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        // Create Daily Cash / Petty Cash Register
        Schema::create('daily_cash_registers', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('opening_cash', 12, 2)->default(0);
            $table->decimal('cash_added', 12, 2)->default(0);
            $table->decimal('total_expenses', 12, 2)->default(0);
            $table->decimal('closing_cash', 12, 2)->default(0);
            $table->timestamps();
        });

        // Modify Expenses to track against Daily Cash
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'daily_cash_register_id')) {
                $table->unsignedBigInteger('daily_cash_register_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('expenses', 'paid_to')) {
                $table->string('paid_to')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('expenses', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paid_to');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('b2b_agents');
        Schema::dropIfExists('daily_cash_registers');
    }
};
