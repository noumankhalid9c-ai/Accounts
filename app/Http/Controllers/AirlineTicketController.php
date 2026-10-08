<?php

namespace App\Http\Controllers;

use App\Models\AirlineTicket;
use App\Models\TicketFlightSegment;
use App\Models\TicketPassenger;
use App\Models\TicketPayment;
use App\Models\TicketInvoice;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Customer;
use App\Models\B2bAgent;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class AirlineTicketController extends Controller
{
    public function dashboard()
    {
        $today = now()->format('Y-m-d');
        
        $todayFlights = TicketFlightSegment::where('departure_date', $today)
            ->with(['ticket.passengers', 'airline', 'departureAirport', 'arrivalAirport'])
            ->get();
            
        $todayTickets = AirlineTicket::whereDate('created_at', $today)->count();
        $todaySales = AirlineTicket::whereDate('created_at', $today)->sum('amount_paid');
        $todayPending = AirlineTicket::whereDate('created_at', $today)->sum('amount_pending');
        
        $monthTickets = AirlineTicket::whereMonth('created_at', now()->month)->count();
        $monthRevenue = AirlineTicket::whereMonth('created_at', now()->month)->sum('total_fare');
        
        return view('airline_tickets.dashboard', compact('todayFlights', 'todayTickets', 'todaySales', 'todayPending', 'monthTickets', 'monthRevenue'));
    }

    public function todayFlights()
    {
        $today = now()->format('Y-m-d');
        $flights = TicketFlightSegment::where('departure_date', $today)
            ->with(['ticket.passengers', 'airline', 'departureAirport', 'arrivalAirport'])
            ->orderBy('departure_time')
            ->get();
            
        return view('airline_tickets.today_flights', compact('flights'));
    }

    public function upcomingFlights(Request $request)
    {
        $startDate = $request->input('start_date', now()->addDay()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->addDays(7)->format('Y-m-d'));
        
        $flights = TicketFlightSegment::whereBetween('departure_date', [$startDate, $endDate])
            ->with(['ticket.passengers', 'airline', 'departureAirport', 'arrivalAirport'])
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();
            
        return view('airline_tickets.upcoming_flights', compact('flights', 'startDate', 'endDate'));
    }

    public function index(Request $request)
    {
        $query = AirlineTicket::with(['customer', 'segments.airline', 'passengers']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('pnr', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
        }
        
        $tickets = $query->latest()->paginate(15);
        return view('airline_tickets.index', compact('tickets'));
    }

    public function create()
    {
        $customers = Customer::all();
        $b2bAgents = B2bAgent::all();
        $airlines = Airline::where('status', true)->get();
        $airports = Airport::where('status', true)->get();
        
        return view('airline_tickets.create', compact('customers', 'b2bAgents', 'airlines', 'airports'));
    }

    public function store(Request $request)
    {
        // Add ticket
        $ticket = AirlineTicket::create([
            'ticket_number' => $request->ticket_number ?? 'TK-' . strtoupper(Str::random(6)),
            'customer_id' => $request->customer_id,
            'b2b_agent_id' => $request->b2b_agent_id,
            'pnr' => $request->pnr,
            'booking_reference' => $request->booking_reference,
            'ticket_date' => $request->ticket_date,
            'ticket_type' => $request->ticket_type,
            'route_type' => $request->route_type,
            'base_fare' => $request->base_fare ?? 0,
            'taxes' => $request->taxes ?? 0,
            'airline_charges' => $request->airline_charges ?? 0,
            'service_charges' => $request->service_charges ?? 0,
            'discount' => $request->discount ?? 0,
            'other_charges' => $request->other_charges ?? 0,
            'total_fare' => 0, // Calculated below
            'amount_paid' => $request->amount_paid ?? 0,
            'amount_pending' => 0,
            'payment_status' => 'Pending',
            'ticket_status' => $request->ticket_status ?? 'Reserved',
            'notes' => $request->notes,
            'qr_token' => Str::random(40),
            'created_by' => auth()->id(),
        ]);

        $this->updateFinancials($ticket);

        // Add segments
        if ($request->has('segments')) {
            foreach ($request->segments as $index => $segment) {
                TicketFlightSegment::create(array_merge($segment, [
                    'ticket_id' => $ticket->id,
                    'segment_order' => $index,
                ]));
            }
        }

        // Add passengers
        if ($request->has('passengers')) {
            foreach ($request->passengers as $passenger) {
                TicketPassenger::create(array_merge($passenger, [
                    'ticket_id' => $ticket->id,
                ]));
            }
        }

        // Initial Payment
        if ($request->filled('amount_paid') && $request->amount_paid > 0) {
            TicketPayment::create([
                'ticket_id' => $ticket->id,
                'payment_date' => now()->format('Y-m-d'),
                'amount' => $request->amount_paid,
                'payment_method' => $request->payment_method ?? 'Cash',
                'received_by' => auth()->user()->name ?? 'Admin',
            ]);
        }

        // Generate Invoice
        TicketInvoice::create([
            'ticket_id' => $ticket->id,
            'invoice_number' => 'TKT-INV-' . str_pad($ticket->id, 6, '0', STR_PAD_LEFT),
            'invoice_date' => now()->format('Y-m-d'),
        ]);

        return redirect()->route('airline-tickets.show', $ticket->id)->with('success', 'Ticket created successfully.');
    }

    public function show(AirlineTicket $airlineTicket)
    {
        $airlineTicket->load(['segments.airline', 'segments.departureAirport', 'segments.arrivalAirport', 'passengers', 'payments', 'invoice', 'customer']);
        $ticket = $airlineTicket;
        return view('airline_tickets.show', compact('ticket'));
    }
    
    public function invoice(AirlineTicket $ticket)
    {
        $ticket->load(['segments.airline', 'segments.departureAirport', 'segments.arrivalAirport', 'passengers', 'payments', 'invoice', 'customer']);
        return view('airline_tickets.invoice', compact('ticket'));
    }

    public function invoicePdf(AirlineTicket $ticket)
    {
        $ticket->load(['segments.airline', 'segments.departureAirport', 'segments.arrivalAirport', 'passengers', 'payments', 'invoice', 'customer']);
        $pdf = Pdf::loadView('airline_tickets.invoice_pdf', compact('ticket'));
        return $pdf->download('Invoice_' . $ticket->ticket_number . '.pdf');
    }

    public function printTicket(AirlineTicket $ticket)
    {
        $ticket->load(['segments.airline', 'segments.departureAirport', 'segments.arrivalAirport', 'passengers']);
        return view('airline_tickets.print', compact('ticket'));
    }

    public function addPayment(Request $request, AirlineTicket $ticket)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        TicketPayment::create([
            'ticket_id' => $ticket->id,
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'received_by' => $request->received_by ?? auth()->user()->name,
            'notes' => $request->notes,
        ]);

        $this->updateFinancials($ticket);

        return back()->with('success', 'Payment added successfully.');
    }
    
    public function cancel(AirlineTicket $ticket)
    {
        $ticket->update(['ticket_status' => 'Cancelled']);
        return back()->with('success', 'Ticket cancelled successfully.');
    }
    
    public function reissue(AirlineTicket $ticket)
    {
        $ticket->update(['ticket_status' => 'Reissued']);
        return back()->with('success', 'Ticket marked as reissued.');
    }
    
    public function verify($token)
    {
        $ticket = AirlineTicket::where('qr_token', $token)
            ->with(['segments.airline', 'segments.departureAirport', 'segments.arrivalAirport', 'passengers'])
            ->firstOrFail();
            
        return view('airline_tickets.verify', compact('ticket'));
    }

    private function updateFinancials(AirlineTicket $ticket)
    {
        $totalFare = $ticket->base_fare + $ticket->taxes + $ticket->airline_charges + $ticket->service_charges + $ticket->other_charges - $ticket->discount;
        $totalPaid = $ticket->payments()->sum('amount');
        $amountPending = $totalFare - $totalPaid;
        
        $status = 'Pending';
        if ($totalPaid > 0 && $totalPaid < $totalFare) {
            $status = 'Partial';
        } elseif ($totalPaid >= $totalFare && $totalFare > 0) {
            $status = 'Paid';
        }

        $ticket->update([
            'total_fare' => $totalFare,
            'amount_paid' => $totalPaid,
            'amount_pending' => $amountPending,
            'payment_status' => $status,
        ]);
    }
}
