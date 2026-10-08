<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public Voucher Verification
Route::get('/voucher/verify/{token}', [\App\Http\Controllers\TravelVoucherController::class, 'verify'])->name('travel-vouchers.verify');

// Public Ticket Verification
Route::get('/ticket/verify/{token}', [\App\Http\Controllers\AirlineTicketController::class, 'verify'])->name('airline-tickets.verify');


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/captcha/refresh', [AuthController::class, 'generateCaptchaApi'])->name('captcha.refresh');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Placeholder routes for navigation links
    Route::get('/profile', function() { return 'Profile'; })->name('profile');
    // Client Management
    Route::resource('clients', ClientController::class);

    Route::get('/b2b', [\App\Http\Controllers\B2bAgentController::class, 'dashboard'])->name('b2b.dashboard');
    Route::resource('b2b-agents', \App\Http\Controllers\B2bAgentController::class);
    Route::get('/b2b-agents/{b2b_agent}/statement', [\App\Http\Controllers\B2bAgentController::class, 'statement'])->name('b2b-agents.statement');
    
    // Commission Payments
    Route::post('/commission-payments', [\App\Http\Controllers\AgentCommissionPaymentController::class, 'store'])->name('commission-payments.store');
    Route::get('/commission-payments/{payment}/slip', [\App\Http\Controllers\AgentCommissionPaymentController::class, 'slip'])->name('commission-payments.slip');
    
    // B2B Invoices
    Route::post('/b2b-invoices', [\App\Http\Controllers\B2bInvoiceController::class, 'store'])->name('b2b-invoices.store');
    Route::get('/b2b-invoices/{invoice}', [\App\Http\Controllers\B2bInvoiceController::class, 'show'])->name('b2b-invoices.show');

    Route::resource('employees', \App\Http\Controllers\EmployeeController::class);

    Route::get('/salary', [\App\Http\Controllers\SalaryController::class, 'index'])->name('salary.index');
    Route::post('/salary', [\App\Http\Controllers\SalaryController::class, 'store'])->name('salary.store');
    Route::put('/salary/{salary}', [\App\Http\Controllers\SalaryController::class, 'update'])->name('salary.update');
    Route::delete('/salary/{salary}', [\App\Http\Controllers\SalaryController::class, 'destroy'])->name('salary.destroy');
    Route::post('/salary/generate', [\App\Http\Controllers\SalaryController::class, 'generate'])->name('salary.generate');
    Route::post('/salary/{salary}/mark-paid', [\App\Http\Controllers\SalaryController::class, 'markPaid'])->name('salary.markPaid');
    Route::post('/salary/{salary}/mark-pending', [\App\Http\Controllers\SalaryController::class, 'markPending'])->name('salary.markPending');
    Route::get('/salary/{salary}/slip', [\App\Http\Controllers\SalaryController::class, 'slip'])->name('salary.slip');
    Route::get('/expenses', function() { return 'Daily Expense'; })->name('expenses.index');
    Route::resource('bookings', \App\Http\Controllers\BookingController::class);
    Route::post('/bookings/{booking}/payment', [\App\Http\Controllers\BookingController::class, 'addPayment'])->name('bookings.add_payment');
    Route::get('/bookings/{booking}/invoice', [\App\Http\Controllers\BookingController::class, 'generateInvoice'])->name('bookings.invoice');
    
    // Travel Groups & Vouchers
    Route::resource('travel-groups', \App\Http\Controllers\TravelGroupController::class);
    Route::post('/travel-groups/{travel_group}/clients', [\App\Http\Controllers\TravelGroupController::class, 'storeClient'])->name('travel-groups.clients.store');
    Route::post('/travel-groups/{travel_group}/flights', [\App\Http\Controllers\TravelGroupController::class, 'storeFlight'])->name('travel-groups.flights.store');
    Route::post('/travel-groups/{travel_group}/hotels', [\App\Http\Controllers\TravelGroupController::class, 'storeHotel'])->name('travel-groups.hotels.store');
    Route::post('/travel-groups/{travel_group}/transports', [\App\Http\Controllers\TravelGroupController::class, 'storeTransport'])->name('travel-groups.transports.store');
    
    Route::get('/travel-groups/{travel_group}/vouchers/create', [\App\Http\Controllers\TravelVoucherController::class, 'create'])->name('travel-vouchers.create');
    Route::post('/travel-groups/{travel_group}/vouchers', [\App\Http\Controllers\TravelVoucherController::class, 'store'])->name('travel-vouchers.store');
    Route::get('/travel-vouchers/{travel_voucher}', [\App\Http\Controllers\TravelVoucherController::class, 'show'])->name('travel-vouchers.show');
    Route::get('/travel-vouchers/{travel_voucher}/print', [\App\Http\Controllers\TravelVoucherController::class, 'print'])->name('travel-vouchers.print');

    Route::get('/users', function() { return 'Users'; })->name('users.index');
    Route::get('/settings', function() { return 'Settings'; })->name('settings.index');
    
    // Airline Tickets Module
    Route::get('/airline-tickets/dashboard', [\App\Http\Controllers\AirlineTicketController::class, 'dashboard'])->name('airline-tickets.dashboard');
    Route::get('/airline-tickets/today', [\App\Http\Controllers\AirlineTicketController::class, 'todayFlights'])->name('airline-tickets.today');
    Route::get('/airline-tickets/upcoming', [\App\Http\Controllers\AirlineTicketController::class, 'upcomingFlights'])->name('airline-tickets.upcoming');
    Route::resource('airline-tickets', \App\Http\Controllers\AirlineTicketController::class);
    Route::get('/airline-tickets/{ticket}/invoice', [\App\Http\Controllers\AirlineTicketController::class, 'invoice'])->name('airline-tickets.invoice');
    Route::get('/airline-tickets/{ticket}/invoice/pdf', [\App\Http\Controllers\AirlineTicketController::class, 'invoicePdf'])->name('airline-tickets.invoice.pdf');
    Route::get('/airline-tickets/{ticket}/print', [\App\Http\Controllers\AirlineTicketController::class, 'printTicket'])->name('airline-tickets.print');
    Route::post('/airline-tickets/{ticket}/payments', [\App\Http\Controllers\AirlineTicketController::class, 'addPayment'])->name('airline-tickets.payments.store');
    Route::post('/airline-tickets/{ticket}/cancel', [\App\Http\Controllers\AirlineTicketController::class, 'cancel'])->name('airline-tickets.cancel');
    Route::post('/airline-tickets/{ticket}/reissue', [\App\Http\Controllers\AirlineTicketController::class, 'reissue'])->name('airline-tickets.reissue');
    
    // Airline & Airport Master Data
    Route::resource('airlines', \App\Http\Controllers\AirlineController::class);
    Route::resource('airports', \App\Http\Controllers\AirportController::class);
    
    // Ticket Invoices
    Route::get('/ticket-invoices', [\App\Http\Controllers\TicketInvoiceController::class, 'index'])->name('ticket-invoices.index');
    
    // Ticket Reports
    Route::get('/ticket-reports', [\App\Http\Controllers\TicketReportController::class, 'index'])->name('ticket-reports.index');
});

// CRM Routes
Route::middleware(['auth'])->prefix('crm')->name('crm.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\CrmController::class, 'dashboard'])->name('dashboard');
    Route::get('/pipeline', [\App\Http\Controllers\CrmController::class, 'pipeline'])->name('pipeline');
    Route::get('/leads', [\App\Http\Controllers\CrmController::class, 'leads'])->name('leads');
    Route::post('/leads', [\App\Http\Controllers\CrmController::class, 'storeLead'])->name('leads.store');
    Route::get('/leads/{lead}', [\App\Http\Controllers\CrmController::class, 'showLead'])->name('leads.show');
    Route::post('/leads/{lead}/stage', [\App\Http\Controllers\CrmController::class, 'updateLeadStage'])->name('leads.stage');
    Route::post('/leads/{lead}/followup', [\App\Http\Controllers\CrmController::class, 'storeFollowup'])->name('leads.followup');
    Route::delete('/leads/{lead}', [\App\Http\Controllers\CrmController::class, 'destroyLead'])->name('leads.destroy');
});
// Petty Cash Routes
Route::middleware(['auth'])->prefix('petty-cash')->name('petty_cash.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\PettyCashController::class, 'dashboard'])->name('dashboard');
    Route::get('/daily', [\App\Http\Controllers\PettyCashController::class, 'dailyCash'])->name('daily');
    Route::post('/days/{day}/transaction', [\App\Http\Controllers\PettyCashController::class, 'storeTransaction'])->name('transactions.store');
    Route::post('/days/{day}/close', [\App\Http\Controllers\PettyCashController::class, 'closeDay'])->name('days.close');
    Route::get('/transactions/{transaction}/print', [\App\Http\Controllers\PettyCashController::class, 'printTransaction'])->name('transactions.print');
    Route::get('/reports', [\App\Http\Controllers\PettyCashController::class, 'reports'])->name('reports');
    Route::get('/export', [\App\Http\Controllers\PettyCashController::class, 'export'])->name('export');
});