<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class MonthlyExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'monthly_expenses';
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'expense_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'bill_issue_date' => 'date',
        'is_utility_bill' => 'boolean',
        'expense_month' => 'integer',
        'expense_year' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->expense_code)) {
                $monthStr = str_pad($model->expense_month ?? Carbon::now()->month, 2, '0', STR_PAD_LEFT);
                $yearStr = $model->expense_year ?? Carbon::now()->year;
                $prefix = $model->is_utility_bill ? 'UTL' : 'EXP';
                
                // Find next sequence for this prefix and year-month
                $latest = static::withTrashed()
                    ->where('expense_code', 'LIKE', "{$prefix}-{$yearStr}{$monthStr}-%")
                    ->latest('id')
                    ->first();

                $seq = 1;
                if ($latest && preg_match('/-(\d+)$/', $latest->expense_code, $matches)) {
                    $seq = intval($matches[1]) + 1;
                }
                
                $model->expense_code = sprintf('%s-%s%s-%04d', $prefix, $yearStr, $monthStr, $seq);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function payments()
    {
        return $this->hasMany(MonthlyExpensePayment::class, 'monthly_expense_id')->orderBy('payment_date', 'asc');
    }

    public function recurringExpense()
    {
        return $this->belongsTo(RecurringExpense::class, 'recurring_expense_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, round((float)$this->amount - (float)$this->paid_amount, 2));
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->payment_status === 'Paid' || $this->payment_status === 'Cancelled') {
            return false;
        }
        if (!$this->due_date) {
            return false;
        }
        return Carbon::parse($this->due_date)->endOfDay()->isPast() && $this->remaining_amount > 0;
    }

    /**
     * Recalculate paid_amount and payment_status based on payments
     */
    public function recalculateStatus(): void
    {
        $totalPaid = round((float)$this->payments()->sum('amount'), 2);
        $this->paid_amount = $totalPaid;

        if ($this->payment_status === 'Cancelled') {
            // Keep cancelled unless paid
            if ($totalPaid >= $this->amount && $this->amount > 0) {
                $this->payment_status = 'Paid';
            }
        } else {
            if ($this->amount > 0 && $totalPaid >= $this->amount) {
                $this->payment_status = 'Paid';
            } elseif ($totalPaid > 0) {
                $this->payment_status = 'Partially Paid';
            } elseif ($this->due_date && Carbon::parse($this->due_date)->endOfDay()->isPast()) {
                $this->payment_status = 'Overdue';
            } else {
                $this->payment_status = 'Pending';
            }
        }

        $this->save();
    }

    public function scopeForPeriod($query, $month, $year)
    {
        return $query->where('expense_month', $month)->where('expense_year', $year);
    }

    public function scopeActiveNonCancelled($query)
    {
        return $query->where('payment_status', '!=', 'Cancelled');
    }

    public function scopeUtilityBills($query)
    {
        return $query->where('is_utility_bill', true);
    }
}
