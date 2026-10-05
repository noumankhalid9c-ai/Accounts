<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelVoucher extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'voucher_date' => 'date',
        'snapshot_data' => 'array',
    ];

    public function travelGroup()
    {
        return $this->belongsTo(TravelGroup::class);
    }
}
