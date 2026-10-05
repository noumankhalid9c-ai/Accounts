<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PettyCashTransaction extends Model { use HasFactory; protected $guarded = []; public function day() { return $this->belongsTo(PettyCashDay::class, 'petty_cash_day_id'); } public function category() { return $this->belongsTo(ExpenseCategory::class); } public function creator() { return $this->belongsTo(User::class, 'created_by'); } }