<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Customer extends Model { use HasFactory, SoftDeletes; protected $guarded = []; public function leads() { return $this->hasMany(CrmLead::class); } public function followups() { return $this->hasMany(CrmFollowup::class); } public function activities() { return $this->hasMany(CrmActivity::class); } public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); } public function createdBy() { return $this->belongsTo(User::class, 'created_by'); } }