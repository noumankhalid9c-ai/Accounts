<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function followups()
    {
        return $this->hasMany(CrmFollowup::class, 'lead_id');
    }

    public function activities()
    {
        return $this->hasMany(CrmActivity::class, 'lead_id');
    }

    public function comments()
    {
        return $this->hasMany(CrmLeadComment::class, 'lead_id')->orderBy('created_at', 'desc');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }
}