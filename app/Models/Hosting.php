<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hosting extends Model
{
    use HasFactory;

    protected $table = 'hosting_accounts';
    protected $guarded = [];

    const UPDATED_AT = null;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
