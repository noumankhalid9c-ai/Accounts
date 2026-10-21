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

    public function getFlightsAttribute(): array
    {
        if (!empty($this->snapshot_data['flights'])) {
            return $this->snapshot_data['flights'];
        }

        if ($this->travelGroup && $this->travelGroup->relationLoaded('flights')) {
            return $this->travelGroup->flights->toArray();
        }

        if ($this->travelGroup && $this->travelGroup->flights()->exists()) {
            return $this->travelGroup->flights()->get()->toArray();
        }

        return [];
    }

    public function getFlightNumbersSummaryAttribute(): string
    {
        $flights = $this->flights;
        if (empty($flights)) {
            return 'N/A';
        }

        $numbers = [];
        foreach ($flights as $f) {
            if (!empty($f['flight_number'])) {
                $numbers[] = strtoupper($f['flight_number']);
            }
        }

        $numbers = array_unique($numbers);
        return !empty($numbers) ? implode(' / ', $numbers) : 'N/A';
    }
}

