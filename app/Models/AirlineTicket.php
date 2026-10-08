<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlineTicket extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'ticket_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function b2bAgent()
    {
        return $this->belongsTo(B2bAgent::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function segments()
    {
        return $this->hasMany(TicketFlightSegment::class, 'ticket_id')->orderBy('segment_order');
    }

    public function passengers()
    {
        return $this->hasMany(TicketPassenger::class, 'ticket_id');
    }

    public function payments()
    {
        return $this->hasMany(TicketPayment::class, 'ticket_id')->orderBy('payment_date');
    }

    public function invoice()
    {
        return $this->hasOne(TicketInvoice::class, 'ticket_id');
    }
}
