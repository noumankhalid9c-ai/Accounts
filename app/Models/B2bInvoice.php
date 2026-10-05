<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2bInvoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(B2bAgent::class, 'b2b_agent_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
