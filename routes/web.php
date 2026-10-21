<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->getLandingUrl());
    }
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

    // Forgot Password & Reset Flow
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Force Password Change Route
    Route::get('/force-change-password', [AuthController::class, 'showForceChangePassword'])->name('password.force_change');
    Route::post('/force-change-password', [AuthController::class, 'forceChangePassword'])->name('password.force_change.post');

    // User Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [\App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/revoke-sessions', [\App\Http\Controllers\ProfileController::class, 'revokeOtherSessions'])->name('profile.revoke_sessions');

    // ==========================================
    // Advanced User Management & RBAC Subsystem
    // ==========================================
    Route::post('/users/bulk-action', [\App\Http\Controllers\UserController::class, 'bulkAction'])->name('users.bulk_action');
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::post('/users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset_password');
    Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle_status');
    Route::post('/users/{user}/revoke-sessions', [\App\Http\Controllers\UserController::class, 'revokeSessions'])->name('users.revoke_sessions');
    Route::get('/users/{user}/permissions', [\App\Http\Controllers\UserController::class, 'permissions'])->name('users.permissions');
    Route::post('/users/{user}/permissions', [\App\Http\Controllers\UserController::class, 'updatePermissions'])->name('users.permissions.update');

    // Roles & Permissions
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::get('/roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'permissions'])->name('roles.permissions');
    Route::post('/roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // User Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Login History
    Route::get('/login-history', [\App\Http\Controllers\LoginHistoryController::class, 'index'])->name('login-history.index');

    // Security Settings
    Route::get('/security-settings', [\App\Http\Controllers\SecuritySettingController::class, 'index'])->name('security-settings.index');
    Route::post('/security-settings', [\App\Http\Controllers\SecuritySettingController::class, 'update'])->name('security-settings.update');
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
    Route::redirect('/expenses', '/petty-cash/daily')->name('expenses.index');
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
    Route::get('/travel-vouchers/{travel_voucher}/edit', [\App\Http\Controllers\TravelVoucherController::class, 'edit'])->name('travel-vouchers.edit');
    Route::put('/travel-vouchers/{travel_voucher}', [\App\Http\Controllers\TravelVoucherController::class, 'update'])->name('travel-vouchers.update');
    Route::get('/travel-vouchers/{travel_voucher}/print', [\App\Http\Controllers\TravelVoucherController::class, 'print'])->name('travel-vouchers.print');

    Route::get('/settings', [\App\Http\Controllers\SecuritySettingController::class, 'index'])->name('settings.index');
    
    // Airline Tickets Module
    Route::get('/airline-tickets/dashboard', [\App\Http\Controllers\AirlineTicketController::class, 'dashboard'])->name('airline-tickets.dashboard');
    Route::get('/airline-tickets/today', [\App\Http\Controllers\AirlineTicketController::class, 'todayFlights'])->name('airline-tickets.today');
    Route::get('/airline-tickets/upcoming', [\App\Http\Controllers\AirlineTicketController::class, 'upcomingFlights'])->name('airline-tickets.upcoming');
    Route::resource('airline-tickets', \App\Http\Controllers\AirlineTicketController::class)->parameters(['airline-tickets' => 'ticket']);
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

    // Polio Certificate Invoice Module
    Route::prefix('polio-invoices')->name('polio-invoices.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'store'])->name('store');
        Route::get('/{polioInvoice}', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'show'])->name('show');
        Route::get('/{polioInvoice}/edit', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'edit'])->name('edit');
        Route::put('/{polioInvoice}', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'update'])->name('update');
        Route::delete('/{polioInvoice}', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'destroy'])->name('destroy');
        Route::post('/{polioInvoice}/payments', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'addPayment'])->name('payments.store');
        Route::delete('/payments/{payment}', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'deletePayment'])->name('payments.destroy');
        Route::get('/{polioInvoice}/print', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'printInvoice'])->name('print');
        Route::get('/{polioInvoice}/pdf', [\App\Http\Controllers\PolioCertificateInvoiceController::class, 'downloadPdf'])->name('pdf');
    });
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

// Monthly Expenses & Utility Bills Module
Route::middleware(['auth'])->prefix('monthly-expenses')->name('monthly_expenses.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\MonthlyExpenseController::class, 'dashboard'])->name('dashboard');
    Route::get('/create', [\App\Http\Controllers\MonthlyExpenseController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\MonthlyExpenseController::class, 'store'])->name('store');
    Route::get('/all', [\App\Http\Controllers\MonthlyExpenseController::class, 'index'])->name('index');
    Route::get('/utility-bills', [\App\Http\Controllers\MonthlyExpenseController::class, 'utilityBills'])->name('utility_bills');
    Route::get('/recurring', [\App\Http\Controllers\MonthlyExpenseController::class, 'recurring'])->name('recurring');
    Route::post('/recurring', [\App\Http\Controllers\MonthlyExpenseController::class, 'storeRecurring'])->name('recurring.store');
    Route::put('/recurring/{recurring}', [\App\Http\Controllers\MonthlyExpenseController::class, 'updateRecurring'])->name('recurring.update');
    Route::delete('/recurring/{recurring}', [\App\Http\Controllers\MonthlyExpenseController::class, 'destroyRecurring'])->name('recurring.destroy');
    Route::post('/recurring/generate', [\App\Http\Controllers\MonthlyExpenseController::class, 'generateRecurring'])->name('recurring.generate');
    Route::get('/reports', [\App\Http\Controllers\MonthlyExpenseController::class, 'reports'])->name('reports');
    Route::get('/reports/comparison', [\App\Http\Controllers\MonthlyExpenseController::class, 'comparisonReport'])->name('reports.comparison');
    Route::get('/reports/yearly', [\App\Http\Controllers\MonthlyExpenseController::class, 'yearlyReport'])->name('reports.yearly');
    Route::get('/comparison', [\App\Http\Controllers\MonthlyExpenseController::class, 'comparisonReport'])->name('comparison');
    Route::get('/yearly', [\App\Http\Controllers\MonthlyExpenseController::class, 'yearlyReport'])->name('yearly');
    Route::get('/reports/export/{format?}', [\App\Http\Controllers\MonthlyExpenseController::class, 'export'])->name('export');
    Route::get('/reports/print', [\App\Http\Controllers\MonthlyExpenseController::class, 'printReport'])->name('print');
    Route::get('/categories', [\App\Http\Controllers\MonthlyExpenseController::class, 'categories'])->name('categories');
    Route::post('/categories', [\App\Http\Controllers\MonthlyExpenseController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [\App\Http\Controllers\MonthlyExpenseController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\MonthlyExpenseController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/{expense}', [\App\Http\Controllers\MonthlyExpenseController::class, 'show'])->name('show');
    Route::get('/{expense}/edit', [\App\Http\Controllers\MonthlyExpenseController::class, 'edit'])->name('edit');
    Route::put('/{expense}', [\App\Http\Controllers\MonthlyExpenseController::class, 'update'])->name('update');
    Route::delete('/{expense}', [\App\Http\Controllers\MonthlyExpenseController::class, 'destroy'])->name('destroy');
    Route::post('/{expense}/payments', [\App\Http\Controllers\MonthlyExpenseController::class, 'addPayment'])->name('payments.store');
    Route::delete('/payments/{payment}', [\App\Http\Controllers\MonthlyExpenseController::class, 'deletePayment'])->name('payments.destroy');
    Route::get('/{expense}/attachment', [\App\Http\Controllers\MonthlyExpenseController::class, 'viewAttachment'])->name('attachment');
});