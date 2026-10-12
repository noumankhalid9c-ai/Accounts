<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyExpensePayment extends Model
{
    use HasFactory;

    protected $table = 'monthly_expense_payments';
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected static function booted()
    {
        static::saved(function ($payment) {
            $payment->monthlyExpense?->recalculateStatus();
        });

        static::deleted(function ($payment) {
            $payment->monthlyExpense?->recalculateStatus();
        });
    }

    public function monthlyExpense()
    {
        return $this->belongsTo(MonthlyExpense::class, 'monthly_expense_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
