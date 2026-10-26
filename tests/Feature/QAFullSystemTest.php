<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\TravelGroup;
use App\Models\TravelVoucher;
use App\Models\AirlineTicket;
use App\Models\MonthlyExpense;
use App\Models\PolioCertificateInvoice;
use App\Models\Client;
use App\Models\Customer;

class QAFullSystemTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::find(1);
    }

    public function test_dashboard_accessible_to_super_admin()
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Madina Shareef Travels & Tours');
    }

    public function test_user_management_module_pages()
    {
        // Users Index
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);

        // Create User Form
        $response = $this->actingAs($this->admin)->get('/users/create');
        $response->assertStatus(200);

        // Show User
        $response = $this->actingAs($this->admin)->get('/users/' . $this->admin->id);
        $response->assertStatus(200);

        // Edit User
        $response = $this->actingAs($this->admin)->get('/users/' . $this->admin->id . '/edit');
        $response->assertStatus(200);

        // User Permissions Matrix
        $response = $this->actingAs($this->admin)->get('/users/' . $this->admin->id . '/permissions');
        $response->assertStatus(200);

        // Roles Index
        $response = $this->actingAs($this->admin)->get('/roles');
        $response->assertStatus(200);

        // Activity Logs
        $response = $this->actingAs($this->admin)->get('/activity-logs');
        $response->assertStatus(200);

        // Login History
        $response = $this->actingAs($this->admin)->get('/login-history');
        $response->assertStatus(200);

        // Security Settings
        $response = $this->actingAs($this->admin)->get('/security-settings');
        $response->assertStatus(200);
    }

    public function test_travel_groups_and_vouchers_lifecycle()
    {
        // Index
        $response = $this->actingAs($this->admin)->get('/travel-groups');
        $response->assertStatus(200);

        // Create Group View
        $response = $this->actingAs($this->admin)->get('/travel-groups/create');
        $response->assertStatus(200);

        $group = TravelGroup::first();
        if ($group) {
            // Show Group
            $response = $this->actingAs($this->admin)->get('/travel-groups/' . $group->id);
            $response->assertStatus(200);

            // Edit Group
            $response = $this->actingAs($this->admin)->get('/travel-groups/' . $group->id . '/edit');
            $response->assertStatus(200);

            // Create Voucher Form
            $response = $this->actingAs($this->admin)->get('/travel-groups/' . $group->id . '/vouchers/create');
            $response->assertStatus(200);
            $response->assertSee('Flight Numbers');
            $response->assertSee('Voucher Comments / Remarks');
        }

        $voucher = TravelVoucher::first();
        if ($voucher) {
            // Show Voucher
            $response = $this->actingAs($this->admin)->get('/travel-vouchers/' . $voucher->id);
            $response->assertStatus(200);

            // Edit Voucher
            $response = $this->actingAs($this->admin)->get('/travel-vouchers/' . $voucher->id . '/edit');
            $response->assertStatus(200);

            // Print Voucher
            $response = $this->actingAs($this->admin)->get('/travel-vouchers/' . $voucher->id . '/print');
            $response->assertStatus(200);

            // Verify Public QR
            $response = $this->get('/voucher/verify/' . $voucher->qr_token);
            $response->assertStatus(200);
        }
    }

    public function test_airline_tickets_module()
    {
        // Dashboard
        $response = $this->actingAs($this->admin)->get('/airline-tickets/dashboard');
        $response->assertStatus(200);

        // Index
        $response = $this->actingAs($this->admin)->get('/airline-tickets');
        $response->assertStatus(200);

        // Create Form
        $response = $this->actingAs($this->admin)->get('/airline-tickets/create');
        $response->assertStatus(200);

        // Today & Upcoming Flights
        $response = $this->actingAs($this->admin)->get('/airline-tickets/today');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/airline-tickets/upcoming');
        $response->assertStatus(200);

        $ticket = AirlineTicket::first();
        if ($ticket) {
            // Show Ticket
            $response = $this->actingAs($this->admin)->get('/airline-tickets/' . $ticket->id);
            $response->assertStatus(200);

            // Edit Ticket
            $response = $this->actingAs($this->admin)->get('/airline-tickets/' . $ticket->id . '/edit');
            $response->assertStatus(200);

            // Invoice
            $response = $this->actingAs($this->admin)->get('/airline-tickets/' . $ticket->id . '/invoice');
            $response->assertStatus(200);

            // Print
            $response = $this->actingAs($this->admin)->get('/airline-tickets/' . $ticket->id . '/print');
            $response->assertStatus(200);

            // Public Verify
            $response = $this->get('/ticket/verify/' . $ticket->qr_token);
            $response->assertStatus(200);
        }
    }

    public function test_polio_certificate_invoices_module()
    {
        $response = $this->actingAs($this->admin)->get('/polio-invoices');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/polio-invoices/create');
        $response->assertStatus(200);

        $polio = PolioCertificateInvoice::first();
        if ($polio) {
            $response = $this->actingAs($this->admin)->get('/polio-invoices/' . $polio->id);
            $response->assertStatus(200);

            $response = $this->actingAs($this->admin)->get('/polio-invoices/' . $polio->id . '/edit');
            $response->assertStatus(200);

            $response = $this->actingAs($this->admin)->get('/polio-invoices/' . $polio->id . '/print');
            $response->assertStatus(200);
        }
    }

    public function test_monthly_expenses_and_petty_cash()
    {
        $response = $this->actingAs($this->admin)->get('/monthly-expenses/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/monthly-expenses/all');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/monthly-expenses/utility-bills');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/monthly-expenses/recurring');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/monthly-expenses/reports');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/petty-cash/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/petty-cash/daily');
        $response->assertStatus(200);
    }

    public function test_crm_and_operational_modules()
    {
        $response = $this->actingAs($this->admin)->get('/crm/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/crm/leads');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/crm/pipeline');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/clients');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/b2b');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/employees');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/salary');
        $response->assertStatus(200);
    }
}
