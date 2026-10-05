<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $table = 'domains';
    protected $guarded = [];

    const UPDATED_AT = null;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
