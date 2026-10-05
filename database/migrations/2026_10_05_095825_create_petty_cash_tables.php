<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::create('petty_cash_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('cash_received', 15, 2)->default(0);
            $table->decimal('cash_expenses', 15, 2)->default(0);
            $table->decimal('closing_cash', 15, 2)->default(0);
            $table->decimal('actual_cash', 15, 2)->nullable();
            $table->decimal('cash_difference', 15, 2)->nullable();
            $table->string('status')->default('Open');
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('petty_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petty_cash_day_id')->constrained('petty_cash_days')->cascadeOnDelete();
            $table->string('transaction_type'); // Cash Received, Expense, Adjustment
            $table->date('date');
            $table->integer('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('expense_categories')->nullOnDelete();
            $table->text('description');
            $table->string('paid_to')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('Cash'); // Cash, Bank, Card, Online
            $table->string('reference_number')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
        // Insert default categories
        DB::table('expense_categories')->insert([
            ['name' => 'Office'], ['name' => 'Transport'], ['name' => 'Tea / Refreshment'],
            ['name' => 'Printing'], ['name' => 'Stationery'], ['name' => 'Marketing'],
            ['name' => 'Other']
        ]);
    }
    public function down(): void {
        Schema::dropIfExists('petty_cash_transactions');
        Schema::dropIfExists('petty_cash_days');
        Schema::dropIfExists('expense_categories');
    }
};