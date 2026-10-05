<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Booking;
use App\Models\B2bAgent;
use App\Models\Salary;
use App\Models\DailyCashRegister;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $thisMonth = Carbon::now()->format('Y-m');

        // Total Client Payments & Pending
        $totalAmount = Booking::sum('total_amount');
        $totalReceived = Booking::sum('advance_payment');
        $totalPending = max(0, $totalAmount - $totalReceived);

        // Today's Collection (assuming we track daily cash added)
        $dailyCash = DailyCashRegister::where('date', $today)->first();
        $todaysCollection = $dailyCash ? $dailyCash->cash_added : 0;
        $todaysExpenses = $dailyCash ? $dailyCash->total_expenses : 0;
        $cashInHand = $dailyCash ? $dailyCash->closing_cash : 0;

        // Payables
        $b2bCommissionPayable = Booking::where('b2b_commission_status', 'Pending')->sum('b2b_commission');
        $teamSalaryPayable = Salary::where('status', 'Pending')->sum('net_salary');

        // Monthly Profit calculation
        // (Total Company Share from Bookings this month) - (Total Expenses this month) - (Salaries paid this month)
        $monthlyCompanyShare = Booking::whereYear('booking_date', Carbon::now()->year)
                                      ->whereMonth('booking_date', Carbon::now()->month)
                                      ->sum('company_share');
                                      
        $monthlyExpenses = DailyCashRegister::whereYear('date', Carbon::now()->year)
                                            ->whereMonth('date', Carbon::now()->month)
                                            ->sum('total_expenses');
                                            
        $monthlySalaries = Salary::where('status', 'Paid')
                                 ->whereYear('salary_month', Carbon::now()->year)
                                 ->whereMonth('salary_month', Carbon::now()->month)
                                 ->sum('net_salary');
                                 
        $monthlyProfit = $monthlyCompanyShare - $monthlyExpenses - $monthlySalaries;

        $stats = [
            'total_received' => $totalReceived,
            'total_pending' => $totalPending,
            'todays_collection' => $todaysCollection,
            'b2b_payable' => $b2bCommissionPayable,
            'salary_payable' => $teamSalaryPayable,
            'todays_expenses' => $todaysExpenses,
            'cash_in_hand' => $cashInHand,
            'monthly_profit' => $monthlyProfit,
        ];

        return view('dashboard', compact('stats'));
    }
}
