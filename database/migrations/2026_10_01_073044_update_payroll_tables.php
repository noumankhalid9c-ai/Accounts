<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->after('id');
            $table->string('designation')->nullable()->after('name');
            $table->date('joining_date')->nullable()->after('designation');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('contact');
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn('salary_month');
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->integer('salary_month')->after('employee_id');
            $table->integer('salary_year')->after('salary_month');
            $table->date('payment_date')->nullable()->after('status');
            $table->text('notes')->nullable()->after('payment_date');
            
            // employee_id + salary_month + salary_year unique constraint
            $table->unique(['employee_id', 'salary_month', 'salary_year']);
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropUnique(['employee_id', 'salary_month', 'salary_year']);
            $table->dropColumn(['salary_month', 'salary_year', 'payment_date', 'notes']);
        });

        Schema::table('salaries', function (Blueprint $table) {
            $table->date('salary_month')->nullable()->after('employee_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'designation', 'joining_date', 'status']);
        });
    }
};
