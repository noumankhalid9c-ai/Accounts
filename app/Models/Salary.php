<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($salary) {
            $salary->net_salary = ($salary->basic_salary ?? 0) 
                + ($salary->sales_commission ?? 0) 
                + ($salary->incentive ?? 0) 
                - ($salary->advance_salary ?? 0) 
                - ($salary->deduction ?? 0);
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
