<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolioInvoicePayment extends Model
{
    use HasFactory;

    protected $table = 'polio_invoice_payments';
    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saved(function ($payment) {
            $payment->invoice?->recalculatePayments();
        });

        static::deleted(function ($payment) {
            $payment->invoice?->recalculatePayments();
        });
    }

    public function invoice()
    {
        return $this->belongsTo(PolioCertificateInvoice::class, 'polio_invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
