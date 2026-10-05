<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CrmLead;
use App\Models\CrmFollowup;
use App\Models\CrmActivity;
use App\Models\User;

class CrmController extends Controller
{
    public function dashboard()
    {
        $totalCustomers = Customer::count();
        $newInquiries = CrmLead::where('stage', 'New Inquiry')->count();
        $followupsToday = CrmFollowup::whereDate('followup_date', today())->count();
        $convertedLeads = CrmLead::where('stage', 'Converted')->count();
        $lostLeads = CrmLead::where('stage', 'Lost')->count();
        
        $leadsByStage = CrmLead::selectRaw('stage, count(*) as count')->groupBy('stage')->pluck('count', 'stage');
        $leadsBySource = CrmLead::selectRaw('source, count(*) as count')->groupBy('source')->pluck('count', 'source');
        $leadsByService = CrmLead::selectRaw('inquiry_type, count(*) as count')->groupBy('inquiry_type')->pluck('count', 'inquiry_type');
        
        $recentActivities = CrmActivity::with(['user', 'lead'])->latest()->take(8)->get();
        $upcomingFollowups = CrmFollowup::with(['lead.customer', 'assignedTo'])->where('followup_date', '>=', today())->orderBy('followup_date')->orderBy('followup_time')->take(5)->get();
        
        return view('crm.dashboard', compact(
            'totalCustomers', 'newInquiries', 'followupsToday', 'convertedLeads', 'lostLeads',
            'leadsByStage', 'leadsBySource', 'leadsByService', 'recentActivities', 'upcomingFollowups'
        ));
    }

    public function pipeline()
    {
        $leads = CrmLead::with(['assignedTo'])->get();
        $stages = [
            'New Inquiry', 'Contacted', 'Follow-up', 'Quotation Sent', 
            'Negotiation', 'Confirmed', 'Converted', 'Lost'
        ];
        return view('crm.leads.pipeline', compact('leads', 'stages'));
    }

    public function leads()
    {
        $leads = CrmLead::with(['customer', 'assignedTo'])->latest()->get();
        return view('crm.leads.index', compact('leads'));
    }

    public function showLead(CrmLead $lead)
    {
        $lead->load(['followups.assignedTo', 'activities.user', 'assignedTo', 'customer']);
        $users = User::all(); // for reassignment or followup assignment
        return view('crm.leads.show', compact('lead', 'users'));
    }

    public function storeLead(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required',
            'phone' => 'required',
            'inquiry_type' => 'required',
            'destination' => 'required',
            'travel_date' => 'required|date',
            'pax' => 'required|integer',
            'source' => 'required',
            'notes' => 'nullable',
        ]);
        
        $data['created_by'] = auth()->id();
        $data['assigned_to'] = auth()->id();
        $data['stage'] = 'New Inquiry';

        // Duplicate Check basic logic
        $existingCustomer = Customer::where('phone', $data['phone'])->first();
        if ($existingCustomer) {
            $data['customer_id'] = $existingCustomer->id;
        } else {
            $newCustomer = Customer::create([
                'name' => $data['customer_name'],
                'phone' => $data['phone'],
                'created_by' => auth()->id(),
                'assigned_to' => auth()->id(),
            ]);
            $data['customer_id'] = $newCustomer->id;
        }

        $lead = CrmLead::create($data);
        
        CrmActivity::create([
            'lead_id' => $lead->id,
            'customer_id' => $data['customer_id'],
            'user_id' => auth()->id(),
            'activity_type' => 'Created',
            'description' => 'New inquiry created',
        ]);

        return redirect()->route('crm.leads')->with('success', 'Lead created successfully');
    }
    
    public function updateLeadStage(Request $request, CrmLead $lead)
    {
        $oldStage = $lead->stage;
        $lead->update(['stage' => $request->stage]);

        CrmActivity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'user_id' => auth()->id(),
            'activity_type' => 'Stage Changed',
            'old_value' => $oldStage,
            'new_value' => $lead->stage,
            'description' => 'Stage updated to ' . $lead->stage,
        ]);

        return back()->with('success', 'Stage updated');
    }

    public function storeFollowup(Request $request, CrmLead $lead)
    {
        $data = $request->validate([
            'followup_date' => 'required|date',
            'followup_time' => 'nullable',
            'type' => 'required',
            'notes' => 'nullable',
            'result' => 'nullable',
            'next_followup_date' => 'nullable|date',
        ]);
        
        $data['lead_id'] = $lead->id;
        $data['customer_id'] = $lead->customer_id;
        $data['assigned_to'] = auth()->id();
        $data['created_by'] = auth()->id();

        CrmFollowup::create($data);

        CrmActivity::create([
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'user_id' => auth()->id(),
            'activity_type' => 'Follow-up Added',
            'description' => 'Follow-up scheduled for ' . $data['followup_date'] . ' via ' . $data['type'],
        ]);

        return back()->with('success', 'Follow-up added successfully');
    }

    public function destroyLead(CrmLead $lead)
    {
        $this->authorize('delete', $lead); // Calls policy
        $lead->delete();
        return back()->with('success', 'Lead deleted');
    }
}