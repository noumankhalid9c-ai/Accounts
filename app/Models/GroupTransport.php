<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupTransport extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'travel_date' => 'date',
    ];

    public function travelGroup()
    {
        return $this->belongsTo(TravelGroup::class);
    }
}
