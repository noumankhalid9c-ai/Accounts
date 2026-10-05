<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupHotel extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    public function travelGroup()
    {
        return $this->belongsTo(TravelGroup::class);
    }
}
