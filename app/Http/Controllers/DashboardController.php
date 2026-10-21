<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Booking;
use App\Models\B2bAgent;
use App\Models\Salary;
use App\Models\DailyCashRegister;
use App\Models\TicketFlightSegment;
use Carbon\Carbon;

use App\Models\MonthlyExpense;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->isSuperAdmin() && !$user->canDo('dashboard.view')) {
            if ($request->expectsJson()) {
                abort(403, 'Access Denied. You do not have permission to access the main Dashboard.');
            }

            return redirect()->to($user->getLandingUrl())
                ->with('warning', 'Notice: Access to the main Dashboard is restricted for your account. You have been redirected to your operational workspace.');
        }

        $today = Carbon::today()->toDateString();

        $thisMonth = Carbon::now()->format('Y-m');

        // Comprehensive Financial Calculations
        $ticketFare = (float) \App\Models\AirlineTicket::sum('total_fare');
        $ticketPaid = (float) \App\Models\AirlineTicket::sum('amount_paid');
        $ticketPending = (float) \App\Models\AirlineTicket::sum('amount_pending');

        $polioTotal = (float) \App\Models\PolioCertificateInvoice::sum('total_amount');
        $polioPaid = (float) \App\Models\PolioCertificateInvoice::sum('amount_paid');
        $polioPending = (float) \App\Models\PolioCertificateInvoice::sum('balance_due');

        $bookingTotal = (float) Booking::sum('total_amount');
        $bookingReceived = (float) Booking::sum('advance_payment');
        $bookingPending = max(0, $bookingTotal - $bookingReceived);

        // Overall Totals
        $totalRevenue = $ticketFare + $polioTotal + $bookingTotal;
        $totalCollected = $ticketPaid + $polioPaid + $bookingReceived;
        $totalOutstanding = $ticketPending + $polioPending + $bookingPending;

        // Total Expenses
        $monthlyExpTotal = (float) MonthlyExpense::activeNonCancelled()->sum('amount');
        $monthlyExpPaid = (float) MonthlyExpense::activeNonCancelled()->sum('paid_amount');
        
        $salariesPaid = (float) Salary::where('status', 'Paid')->sum('net_salary');
        $pettyCashExp = (float) DailyCashRegister::sum('total_expenses');
        $totalExpenses = $monthlyExpTotal + $salariesPaid + $pettyCashExp;

        // Gross Profit (Total Collected - Settled Expenses)
        $grossProfit = $totalCollected - ($monthlyExpPaid + $salariesPaid + $pettyCashExp);
        $profitMargin = $totalCollected > 0 ? round(($grossProfit / $totalCollected) * 100, 1) : 0;

        // Daily Cash
        $dailyCash = DailyCashRegister::where('date', $today)->first();
        $todaysCollection = $dailyCash ? $dailyCash->cash_added : 0;
        $todaysExpenses = $dailyCash ? $dailyCash->total_expenses : 0;
        $cashInHand = $dailyCash ? $dailyCash->closing_cash : 0;

        // Operational Counts
        $totalTickets = \App\Models\AirlineTicket::count();
        $totalGroups = \App\Models\TravelGroup::count();
        $totalVouchers = \App\Models\TravelVoucher::count();
        $totalClients = \App\Models\Client::count() + \App\Models\Customer::count();

        // Recent Invoices / Transactions (Latest Tickets & Polio Invoices)
        $recentTickets = \App\Models\AirlineTicket::with('customer')->latest()->take(4)->get();
        $recentPolios = \App\Models\PolioCertificateInvoice::latest()->take(2)->get();

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_collected' => $totalCollected,
            'total_expenses' => $totalExpenses,
            'gross_profit' => $grossProfit,
            'profit_margin' => $profitMargin,
            'total_outstanding' => $totalOutstanding,
            'todays_collection' => $todaysCollection,
            'todays_expenses' => $todaysExpenses,
            'cash_in_hand' => $cashInHand,
            'total_tickets' => $totalTickets,
            'total_groups' => $totalGroups,
            'total_vouchers' => $totalVouchers,
            'total_clients' => $totalClients,
        ];

        // Monthly Expenses Module Integration
        $now = Carbon::now();
        $currentMonthExpenses = MonthlyExpense::where('expense_month', $now->month)
            ->where('expense_year', $now->year)
            ->activeNonCancelled()
            ->sum('amount');

        $currentMonthPaid = MonthlyExpense::where('expense_month', $now->month)
            ->where('expense_year', $now->year)
            ->activeNonCancelled()
            ->sum('paid_amount');

        $currentMonthPending = max(0, $currentMonthExpenses - $currentMonthPaid);

        $overdueBillsCount = MonthlyExpense::activeNonCancelled()
            ->where('due_date', '<', Carbon::today())
            ->whereIn('payment_status', ['Pending', 'Partially Paid', 'Overdue'])
            ->count();

        $overdueBillsAmount = MonthlyExpense::activeNonCancelled()
            ->where('due_date', '<', Carbon::today())
            ->whereIn('payment_status', ['Pending', 'Partially Paid', 'Overdue'])
            ->sum(DB::raw('amount - paid_amount'));

        $upcomingBills = MonthlyExpense::with('category')
            ->activeNonCancelled()
            ->where('due_date', '>=', Carbon::today())
            ->whereIn('payment_status', ['Pending', 'Partially Paid', 'Overdue'])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $monthlyExpenseStats = [
            'expenses' => $currentMonthExpenses,
            'paid' => $currentMonthPaid,
            'pending' => $currentMonthPending,
            'overdue_count' => $overdueBillsCount,
            'overdue_amount' => $overdueBillsAmount,
            'upcoming' => $upcomingBills,
        ];

        // Airline Ticket Module: Today's and Upcoming Flights
        $todayFlights = TicketFlightSegment::where('departure_date', $today)
            ->with(['ticket.passengers', 'airline', 'departureAirport', 'arrivalAirport'])
            ->orderBy('departure_time')
            ->get();
            
        $upcomingFlights = TicketFlightSegment::where('departure_date', '>', $today)
            ->where('departure_date', '<=', Carbon::now()->addDays(7)->toDateString())
            ->with(['ticket.passengers', 'airline', 'departureAirport', 'arrivalAirport'])
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        // User & Security Overview Widget (Authorized Administrators Only)
        $userSecurityStats = null;
        if (auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->canDo('users.view'))) {
            $userSecurityStats = [
                'total_users' => \App\Models\User::count(),
                'active_users' => \App\Models\User::where('status', 'Active')->count(),
                'inactive_users' => \App\Models\User::where('status', 'Inactive')->count(),
                'suspended_users' => \App\Models\User::where(function($q) {
                    $q->where('status', 'Suspended')->orWhere('status', 'Locked');
                })->count(),
                'recent_logins' => \App\Models\LoginHistory::with('user')->where('status', 'Success')->latest('created_at')->take(5)->get(),
                'recent_failed' => \App\Models\LoginHistory::where('status', '!=', 'Success')->latest('created_at')->take(5)->get(),
                'recent_users' => \App\Models\User::with('role')->latest('created_at')->take(5)->get(),
            ];
        }

        return view('dashboard', compact('stats', 'todayFlights', 'upcomingFlights', 'monthlyExpenseStats', 'userSecurityStats', 'recentTickets', 'recentPolios'));
    }
}
