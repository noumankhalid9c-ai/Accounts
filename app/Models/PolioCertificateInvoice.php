<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolioCertificateInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'polio_certificate_invoices';
    protected $guarded = [];

    protected $casts = [
        'invoice_date' => 'date',
        'cost_per_person' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'persons' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->invoice_number)) {
                $model->invoice_number = static::generateNextInvoiceNumber();
            }
        });
    }

    /**
     * Generate sequential invoice number (e.g. POLIO-INV-00001)
     */
    public static function generateNextInvoiceNumber(): string
    {
        $last = static::withTrashed()->orderBy('id', 'desc')->first();
        $nextNum = 1;
        if ($last && preg_match('/POLIO-INV-(\d+)/', $last->invoice_number, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        } else {
            $maxId = static::withTrashed()->max('id') ?? 0;
            $nextNum = $maxId + 1;
        }
        return sprintf('POLIO-INV-%05d', $nextNum);
    }

    /**
     * Recalculate payments and status
     */
    public function recalculatePayments(): void
    {
        $totalPaid = round((float)$this->payments()->sum('amount'), 2);
        $this->amount_paid = $totalPaid;
        $this->balance_due = max(0, round((float)$this->total_amount - $totalPaid, 2));

        if ($this->total_amount > 0 && $totalPaid >= $this->total_amount) {
            $this->payment_status = 'Paid';
        } elseif ($totalPaid > 0) {
            $this->payment_status = 'Partially Paid';
        } else {
            $this->payment_status = 'Pending';
        }

        $this->save();
    }

    public function payments()
    {
        return $this->hasMany(PolioInvoicePayment::class, 'polio_invoice_id')->orderBy('payment_date', 'asc');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
