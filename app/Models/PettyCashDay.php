<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PettyCashDay extends Model { use HasFactory; protected $guarded = []; public function transactions() { return $this->hasMany(PettyCashTransaction::class, 'petty_cash_day_id'); } }