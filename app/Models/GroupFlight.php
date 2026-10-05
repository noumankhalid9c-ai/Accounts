<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupFlight extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'departure_date' => 'date',
        'arrival_date' => 'date',
    ];

    public function travelGroup()
    {
        return $this->belongsTo(TravelGroup::class);
    }
}
