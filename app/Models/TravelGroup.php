<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelGroup extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
    ];

    public function clients()
    {
        return $this->hasMany(GroupClient::class);
    }

    public function flights()
    {
        return $this->hasMany(GroupFlight::class);
    }

    public function hotels()
    {
        return $this->hasMany(GroupHotel::class);
    }

    public function transports()
    {
        return $this->hasMany(GroupTransport::class);
    }

    public function vouchers()
    {
        return $this->hasMany(TravelVoucher::class);
    }
}
