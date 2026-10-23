<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentCommissionPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function b2bAgent()
    {
        return $this->belongsTo(B2bAgent::class, 'b2b_agent_id');
    }

    public function agent()
    {
        return $this->b2bAgent();
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

}
