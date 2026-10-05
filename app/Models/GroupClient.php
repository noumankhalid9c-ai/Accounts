<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupClient extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected $casts = [
        'passport_expiry' => 'date',
    ];

    public function travelGroup()
    {
        return $this->belongsTo(TravelGroup::class);
    }
}
