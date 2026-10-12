<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringExpense extends Model
{
    use HasFactory;

    protected $table = 'recurring_expenses';
    protected $guarded = [];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'is_utility_bill' => 'boolean',
        'start_month' => 'integer',
        'start_year' => 'integer',
        'end_month' => 'integer',
        'end_year' => 'integer',
        'due_day' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function monthlyExpenses()
    {
        return $this->hasMany(MonthlyExpense::class, 'recurring_expense_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Determine if this recurring template applies to the specified month & year
     */
    public function isApplicableFor(int $month, int $year): bool
    {
        if ($this->status !== 'Active') {
            return false;
        }

        $startDateInt = ($this->start_year * 12) + $this->start_month;
        $targetDateInt = ($year * 12) + $month;

        if ($targetDateInt < $startDateInt) {
            return false;
        }

        if ($this->end_year && $this->end_month) {
            $endDateInt = ($this->end_year * 12) + $this->end_month;
            if ($targetDateInt > $endDateInt) {
                return false;
            }
        }

        if ($this->frequency === 'Monthly') {
            return true;
        } elseif ($this->frequency === 'Quarterly') {
            $diff = $targetDateInt - $startDateInt;
            return ($diff % 3) === 0;
        } elseif ($this->frequency === 'Yearly') {
            return $this->start_month === $month;
        }

        return true;
    }
}
