<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(B2bAgent::class, 'b2b_agent_id');
    }

    public function salesExecutive()
    {
        return $this->belongsTo(Employee::class, 'assigned_sales_executive_id');
    }

    public function payments()
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function commissionPayments()
    {
        return $this->hasMany(AgentCommissionPayment::class, 'booking_id');
    }

    public function invoices()
    {
        return $this->hasMany(B2bInvoice::class, 'booking_id');
    }

    protected static function booted()
    {
        static::saving(function ($booking) {
            if ($booking->commission_type == 'Percentage' && $booking->commission_percentage) {
                $booking->b2b_commission = ($booking->total_amount * $booking->commission_percentage) / 100;
            }
            $booking->company_share = $booking->total_amount - $booking->b2b_commission;
        });
    }
}
