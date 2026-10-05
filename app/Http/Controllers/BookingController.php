<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\B2bAgent;
use App\Models\Employee;
use App\Models\BookingPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('client')->withSum('payments', 'amount')->orderBy('id', 'desc')->paginate(15);
        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $clients = Client::all();
        $agents = B2bAgent::all();
        $employees = Employee::all();
        return view('bookings.create', compact('clients', 'agents', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'service_type' => 'required',
            'booking_date' => 'required|date',
            'total_amount' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['payment_status'] = 'Pending';
        
        // Auto calculation for initial advance
        if (!empty($data['advance_payment']) && $data['advance_payment'] > 0) {
            if ($data['advance_payment'] >= $data['total_amount']) {
                $data['payment_status'] = 'Paid';
            } else {
                $data['payment_status'] = 'Partially Paid';
            }
        }

        // Auto calculation for Company Share if B2B agent
        if (!empty($data['b2b_agent_id']) && !empty($data['b2b_commission'])) {
            $data['company_share'] = $data['total_amount'] - $data['b2b_commission'];
            $data['b2b_commission_status'] = 'Pending';
        } else {
            $data['company_share'] = $data['total_amount'];
        }

        $booking = Booking::create($data);

        // Record the advance payment as the first payment in history
        if (!empty($data['advance_payment']) && $data['advance_payment'] > 0) {
            BookingPayment::create([
                'booking_id' => $booking->id,
                'amount' => $data['advance_payment'],
                'payment_date' => $booking->booking_date,
                'payment_method' => 'Cash', // Defaulting to Cash
                'notes' => 'Initial Advance Payment'
            ]);
        }

        return redirect()->route('bookings.show', $booking->id)->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['client', 'agent', 'salesExecutive', 'payments']);
        
        // Auto Calculate Remaining
        $totalReceived = $booking->payments->sum('amount');
        $remaining = max(0, $booking->total_amount - $totalReceived);
        
        return view('bookings.show', compact('booking', 'totalReceived', 'remaining'));
    }

    public function edit(Booking $booking)
    {
        // ...
    }

    public function update(Request $request, Booking $booking)
    {
        // ...
    }

    public function destroy(Booking $booking)
    {
        // ...
    }
    
    public function addPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);
        
        BookingPayment::create([
            'booking_id' => $booking->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method ?? 'Cash',
            'notes' => $request->notes,
        ]);
        
        // Update advance_payment field on Booking to reflect total received
        $totalReceived = $booking->payments()->sum('amount');
        $booking->advance_payment = $totalReceived;
        
        // Auto recalculate payment_status
        if ($totalReceived >= $booking->total_amount) {
            $booking->payment_status = 'Paid';
        } else {
            $booking->payment_status = 'Partially Paid';
        }
        $booking->save();
        
        return back()->with('success', 'Payment added successfully. Remaining balance auto-calculated.');
    }
    
    public function generateInvoice(Booking $booking)
    {
        // A simple view that shows an invoice format
        $booking->load(['client', 'payments']);
        $totalReceived = $booking->payments->sum('amount');
        $remaining = max(0, $booking->total_amount - $totalReceived);
        
        return view('bookings.invoice', compact('booking', 'totalReceived', 'remaining'));
    }
}
