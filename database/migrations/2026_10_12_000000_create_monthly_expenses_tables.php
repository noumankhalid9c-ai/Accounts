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
        // 1. Ensure expense_categories has group column if table exists
        if (Schema::hasTable('expense_categories')) {
            Schema::table('expense_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('expense_categories', 'group')) {
                    $table->string('group', 100)->nullable()->default('General')->after('name');
                }
            });
        } else {
            Schema::create('expense_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('group', 100)->nullable()->default('General');
                $table->text('description')->nullable();
                $table->string('status', 50)->default('Active');
                $table->timestamps();
            });
        }

        // 2. Create recurring_expenses table
        if (!Schema::hasTable('recurring_expenses')) {
            Schema::create('recurring_expenses', function (Blueprint $table) {
                $table->id();
                $table->string('expense_name', 255);
                $table->unsignedBigInteger('category_id');
                $table->decimal('default_amount', 15, 2)->default(0.00);
                $table->string('provider_name', 255)->nullable();
                $table->string('frequency', 20)->default('Monthly'); // Monthly, Quarterly, Yearly
                $table->unsignedTinyInteger('start_month');
                $table->unsignedSmallInteger('start_year');
                $table->unsignedTinyInteger('end_month')->nullable();
                $table->unsignedSmallInteger('end_year')->nullable();
                $table->unsignedTinyInteger('due_day')->nullable()->default(10);
                $table->string('payment_method', 50)->nullable();
                $table->boolean('is_utility_bill')->default(false);
                $table->string('utility_type', 50)->nullable();
                $table->string('consumer_number', 100)->nullable();
                $table->string('status', 20)->default('Active'); // Active, Inactive
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('category_id');
                $table->index('status');
            });
        }

        // 3. Create monthly_expenses table
        if (!Schema::hasTable('monthly_expenses')) {
            Schema::create('monthly_expenses', function (Blueprint $table) {
                $table->id();
                $table->string('expense_code', 50)->unique();
                $table->string('expense_title', 255);
                $table->unsignedBigInteger('category_id');
                $table->unsignedTinyInteger('expense_month');
                $table->unsignedSmallInteger('expense_year');
                $table->date('expense_date');
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->decimal('paid_amount', 15, 2)->default(0.00);
                $table->string('provider_name', 255)->nullable();
                $table->string('bill_reference', 100)->nullable();
                $table->date('due_date')->nullable();
                $table->string('payment_status', 50)->default('Pending'); // Pending, Partially Paid, Paid, Overdue, Cancelled
                $table->string('payment_method', 50)->nullable();
                $table->date('payment_date')->nullable();
                $table->string('attachment_path', 255)->nullable();
                $table->string('attachment_name', 255)->nullable();
                $table->unsignedBigInteger('recurring_expense_id')->nullable();
                $table->boolean('is_utility_bill')->default(false);
                $table->string('utility_type', 50)->nullable(); // Electricity, Gas, Telephone, Mobile, Internet, Water, Other
                $table->string('consumer_number', 100)->nullable();
                $table->date('bill_issue_date')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->index(['expense_year', 'expense_month']);
                $table->index('category_id');
                $table->index('payment_status');
                $table->index('is_utility_bill');
                $table->index('recurring_expense_id');
            });
        }

        // 4. Create monthly_expense_payments table
        if (!Schema::hasTable('monthly_expense_payments')) {
            Schema::create('monthly_expense_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('monthly_expense_id');
                $table->date('payment_date');
                $table->decimal('amount', 15, 2)->default(0.00);
                $table->string('payment_method', 50)->default('Bank Transfer');
                $table->string('transaction_reference', 100)->nullable();
                $table->string('paid_by', 255)->nullable();
                $table->text('notes')->nullable();
                $table->string('attachment_path', 255)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('monthly_expense_id')
                      ->references('id')
                      ->on('monthly_expenses')
                      ->onDelete('cascade');
                $table->index('payment_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_expense_payments');
        Schema::dropIfExists('monthly_expenses');
        Schema::dropIfExists('recurring_expenses');
        if (Schema::hasTable('expense_categories') && Schema::hasColumn('expense_categories', 'group')) {
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->dropColumn('group');
            });
        }
    }
};
