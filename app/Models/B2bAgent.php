<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2bAgent extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'b2b_agent_id');
    }

    public function commissionPayments()
    {
        return $this->hasMany(AgentCommissionPayment::class, 'b2b_agent_id');
    }

    public function invoices()
    {
        return $this->hasMany(B2bInvoice::class, 'b2b_agent_id');
    }
}
