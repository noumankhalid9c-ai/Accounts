<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $table = 'expense_categories';
    protected $guarded = [];

    public function monthlyExpenses()
    {
        return $this->hasMany(MonthlyExpense::class, 'category_id');
    }

    public function recurringExpenses()
    {
        return $this->hasMany(RecurringExpense::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}