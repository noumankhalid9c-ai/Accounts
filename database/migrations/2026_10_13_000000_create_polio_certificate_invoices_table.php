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
        Schema::create('polio_certificate_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->string('client_name');
            $table->string('phone');
            $table->string('passport_number')->nullable();
            $table->unsignedInteger('persons')->default(1);
            $table->decimal('cost_per_person', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->decimal('amount_paid', 15, 2)->default(0.00);
            $table->decimal('balance_due', 15, 2)->default(0.00);
            $table->string('payment_status', 30)->default('Pending'); // Pending, Partially Paid, Paid
            $table->string('payment_method', 50)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['invoice_date', 'payment_status']);
            $table->index('client_name');
            $table->index('phone');
            $table->index('passport_number');
        });

        Schema::create('polio_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polio_invoice_id')->constrained('polio_certificate_invoices')->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polio_invoice_payments');
        Schema::dropIfExists('polio_certificate_invoices');
    }
};
