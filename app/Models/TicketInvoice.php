<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketInvoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    public function ticket()
    {
        return $this->belongsTo(AirlineTicket::class, 'ticket_id');
    }
}
