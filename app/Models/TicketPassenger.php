<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPassenger extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry' => 'date',
    ];

    public function ticket()
    {
        return $this->belongsTo(AirlineTicket::class, 'ticket_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
