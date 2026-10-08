<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AirlineTicket;
use App\Models\TicketFlightSegment;
use Illuminate\Support\Facades\DB;

class TicketReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Overview Stats
        $totalTickets = AirlineTicket::whereBetween('ticket_date', [$startDate, $endDate])->count();
        $totalSales = AirlineTicket::whereBetween('ticket_date', [$startDate, $endDate])->sum('total_fare');
        $totalProfit = AirlineTicket::whereBetween('ticket_date', [$startDate, $endDate])->sum(DB::raw('total_fare - (base_fare + taxes)'));
        
        // Top Airlines
        $topAirlines = DB::table('ticket_flight_segments')
            ->join('airlines', 'ticket_flight_segments.airline_id', '=', 'airlines.id')
            ->join('airline_tickets', 'ticket_flight_segments.ticket_id', '=', 'airline_tickets.id')
            ->whereBetween('airline_tickets.ticket_date', [$startDate, $endDate])
            ->select('airlines.name', DB::raw('COUNT(ticket_flight_segments.id) as segment_count'))
            ->groupBy('airlines.name')
            ->orderByDesc('segment_count')
            ->limit(5)
            ->get();
            
        // Daily Sales
        $dailySales = AirlineTicket::whereBetween('ticket_date', [$startDate, $endDate])
            ->select('ticket_date as issue_date', DB::raw('SUM(total_fare) as daily_total'), DB::raw('COUNT(*) as ticket_count'))
            ->groupBy('ticket_date')
            ->orderBy('ticket_date')
            ->get();

        return view('airline_tickets.reports.index', compact('startDate', 'endDate', 'totalTickets', 'totalSales', 'totalProfit', 'topAirlines', 'dailySales'));
    }
}
