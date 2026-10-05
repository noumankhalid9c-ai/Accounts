<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class MarketingCampaign extends Model { use HasFactory; protected $guarded = []; public function leads() { return $this->hasMany(CrmLead::class, 'campaign_id'); } }