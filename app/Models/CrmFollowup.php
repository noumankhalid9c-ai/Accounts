<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CrmFollowup extends Model { use HasFactory, SoftDeletes; protected $guarded = []; public function lead() { return $this->belongsTo(CrmLead::class, 'lead_id'); } public function customer() { return $this->belongsTo(Customer::class, 'customer_id'); } public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); } }