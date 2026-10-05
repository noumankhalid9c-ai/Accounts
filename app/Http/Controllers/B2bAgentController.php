<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\B2bAgent;
use App\Models\Booking;
use App\Models\AgentCommissionPayment;

class B2bAgentController extends Controller
{
    public function dashboard()
    {
        $totalAgents = B2bAgent::where('status', 'Active')->count();
        $totalReceived = Booking::whereNotNull('b2b_agent_id')->sum('total_amount');
        $totalCommission = Booking::whereNotNull('b2b_agent_id')->sum('b2b_commission');
        $companyAmount = Booking::whereNotNull('b2b_agent_id')->sum('company_share');
        $commissionPaid = AgentCommissionPayment::sum('amount');
        $commissionPending = $totalCommission - $commissionPaid;

        return view('b2b_agents.dashboard', compact(
            'totalAgents',
            'totalReceived',
            'totalCommission',
            'companyAmount',
            'commissionPaid',
            'commissionPending'
        ));
    }

    public function index()
    {
        $agents = B2bAgent::latest()->paginate(15);
        return view('b2b_agents.index', compact('agents'));
    }

    public function statement($id)
    {
        $agent = B2bAgent::with(['bookings', 'commissionPayments'])->findOrFail($id);
        
        $totalReceived = $agent->bookings()->sum('total_amount');
        $totalCommission = $agent->bookings()->sum('b2b_commission');
        $companyAmount = $agent->bookings()->sum('company_share');
        $commissionPaid = $agent->commissionPayments()->sum('amount');
        $commissionPending = $totalCommission - $commissionPaid;
        
        return view('b2b_agents.statement', compact(
            'agent', 
            'totalReceived', 
            'totalCommission', 
            'companyAmount', 
            'commissionPaid', 
            'commissionPending'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('b2b_agents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        // Map to DB columns
        $data = $validated;
        $data['name'] = $validated['agent_name'];
        $data['contact'] = $validated['phone'];
        unset($data['agent_name'], $data['phone']);

        B2bAgent::create($data);

        return redirect()->route('b2b-agents.index')->with('success', 'B2B Agent created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $agent = B2bAgent::findOrFail($id);
        return view('b2b_agents.edit', compact('agent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $agent = B2bAgent::findOrFail($id);
        
        $validated = $request->validate([
            'agent_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $data = $validated;
        $data['name'] = $validated['agent_name'];
        $data['contact'] = $validated['phone'];
        unset($data['agent_name'], $data['phone']);

        $agent->update($data);

        return redirect()->route('b2b-agents.index')->with('success', 'B2B Agent updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $agent = B2bAgent::findOrFail($id);
        $agent->delete();
        return redirect()->route('b2b-agents.index')->with('success', 'B2B Agent deleted successfully.');
    }
}
