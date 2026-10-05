<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    use HasFactory;

    protected $table = 'renewals';
    protected $guarded = [];

    const UPDATED_AT = null;
}
