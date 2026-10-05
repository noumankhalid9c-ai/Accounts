<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CrmActivity extends Model { use HasFactory; protected $guarded = []; public function customer() { return $this->belongsTo(Customer::class); } public function lead() { return $this->belongsTo(CrmLead::class, 'lead_id'); } public function user() { return $this->belongsTo(User::class, 'user_id'); } }