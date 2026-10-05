<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';
    protected $guarded = [];

    const UPDATED_AT = null;

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function hosting_accounts()
    {
        return $this->hasMany(Hosting::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
