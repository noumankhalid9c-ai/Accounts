<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AgentCommissionPayment;
use App\Models\B2bAgent;
use Illuminate\Support\Str;

class AgentCommissionPaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'b2b_agent_id' => 'required|exists:b2b_agents,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'transaction_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Generate unique slip number B2B-PAY-XXXXX
        $lastPayment = AgentCommissionPayment::latest('id')->first();
        $nextId = $lastPayment ? $lastPayment->id + 1 : 1;
        $validated['payment_slip_number'] = 'B2B-PAY-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        AgentCommissionPayment::create($validated);

        return back()->with('success', 'Commission Payment of PKR ' . number_format($validated['amount'], 2) . ' recorded successfully!');
    }

    public function slip($id)
    {
        $payment = AgentCommissionPayment::with('b2bAgent')->findOrFail($id);
        return view('b2b_agents.slip', compact('payment'));
    }
}
