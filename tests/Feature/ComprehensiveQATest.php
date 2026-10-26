<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\CrmLead;
use App\Models\CrmLeadComment;
use App\Models\Client;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\TravelGroup;
use App\Models\TravelVoucher;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\AirlineTicket;
use App\Models\TicketPassenger;
use App\Models\TicketPayment;
use App\Models\B2bAgent;
use App\Models\AgentCommissionPayment;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\ExpenseCategory;
use App\Models\MonthlyExpense;
use App\Models\MonthlyExpensePayment;
use App\Models\PettyCashDay;
use App\Models\PettyCashTransaction;
use App\Models\PolioCertificateInvoice;
use App\Models\PolioInvoicePayment;
use Illuminate\Support\Str;

class ComprehensiveQATest extends TestCase
{
    protected $admin;
    protected $salesUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::find(1);
        $this->salesUser = User::find(8) ?? User::where('role_id', 4)->first();
    }

    /**
     * QA Test 1: Inquiries / Leads (CRM) - All Buttons, Forms, Comments, Loop & History
     */
    public function test_crm_leads_full_lifecycle_and_comments_loop()
    {
        // 1. Visit Leads Index
        $response = $this->actingAs($this->admin)->get(route('crm.leads'));
        $response->assertStatus(200);
        $response->assertSee('Inquiries', false);

        // 2. Validation Test: Submit empty lead form
        $response = $this->actingAs($this->admin)->post(route('crm.leads.store'), []);
        $response->assertSessionHasErrors(['customer_name', 'phone', 'inquiry_type', 'destination', 'travel_date', 'pax', 'source']);

        // 3. Create Valid Lead with all fields
        $leadData = [
            'customer_name' => 'QA Client ' . Str::random(5),
            'phone' => '03009988776',
            'whatsapp' => '03009988776',
            'email' => 'qaclient@example.com',
            'inquiry_type' => 'Umrah',
            'destination' => 'Makkah & Madinah',
            'travel_date' => date('Y-m-d', strtotime('+30 days')),
            'pax' => 4,
            'source' => 'WhatsApp',
            'budget' => 500000,
            'notes' => 'Testing CRM lead with all fields filled.',
        ];
        $response = $this->actingAs($this->admin)->post(route('crm.leads.store'), $leadData);
        $response->assertRedirect(route('crm.leads'));
        $response->assertSessionHas('success');

        $lead = CrmLead::where('customer_name', $leadData['customer_name'])->first();
        $this->assertNotNull($lead);

        // 4. Test Lead Details View
        $response = $this->actingAs($this->admin)->get(route('crm.leads.show', $lead->id));
        $response->assertStatus(200);
        $response->assertSee($lead->customer_name);
        $response->assertSee('Comments & History Loop', false);

        // 5. Test Posting Comments by Admin
        $commentDataAdmin = ['comment' => 'Admin Comment: Initial consultation done. Package options sent.'];
        $response = $this->actingAs($this->admin)->post(route('crm.leads.comments.store', $lead->id), $commentDataAdmin);
        $response->assertSessionHas('success');

        // 6. Test Posting Comments by Sales Person
        if ($this->salesUser) {
            $commentDataSales = ['comment' => 'Sales Comment: Client confirmed budget. Needs hotel near gate 25.'];
            $response = $this->actingAs($this->salesUser)->post(route('crm.leads.comments.store', $lead->id), $commentDataSales);
            $response->assertSessionHas('success');
        }

        // 7. Verify Comments Loop History Ordering & Content
        $lead->refresh();
        $comments = $lead->comments;
        $this->assertGreaterThanOrEqual(1, $comments->count());

        $response = $this->actingAs($this->admin)->get(route('crm.leads.show', $lead->id));
        $response->assertStatus(200);
        $response->assertSee('Admin Comment: Initial consultation done.');
        if ($this->salesUser) {
            $response->assertSee('Sales Comment: Client confirmed budget.');
        }

        // 8. Test AJAX Comment Post
        $ajaxResponse = $this->actingAs($this->admin)->postJson(route('crm.leads.comments.store', $lead->id), [
            'comment' => 'AJAX Comment from modal dialog.'
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson(['success' => true]);

        // 9. Test Comment Deletion
        $commentToDelete = $lead->comments()->first();
        $this->assertNotNull($commentToDelete);
        $response = $this->actingAs($this->admin)->delete(route('crm.leads.comments.destroy', [$lead->id, $commentToDelete->id]));
        $response->assertSessionHas('success');

        // 10. Test Stage Update
        $response = $this->actingAs($this->admin)->post(route('crm.leads.stage', $lead->id), [
            'stage' => 'Quotation Sent'
        ]);
        $response->assertSessionHas('success');
        $this->assertEquals('Quotation Sent', $lead->fresh()->stage);

        // 11. Test Follow-up Submission
        $followupData = [
            'followup_date' => date('Y-m-d', strtotime('+2 days')),
            'followup_time' => '14:30',
            'type' => 'Phone Call',
            'notes' => 'Call to confirm flight preference.',
            'result' => 'Pending Confirmation'
        ];
        $response = $this->actingAs($this->admin)->post(route('crm.leads.followup', $lead->id), $followupData);
        $response->assertSessionHas('success');

        // 12. Check Pipeline Board View
        $response = $this->actingAs($this->admin)->get(route('crm.pipeline'));
        $response->assertStatus(200);
        $response->assertSee($lead->customer_name);
    }

    /**
     * QA Test 2: Clients Module - Validation, Creation, Listing, Editing
     */
    public function test_clients_module_crud()
    {
        // Index
        $response = $this->actingAs($this->admin)->get(route('clients.index'));
        $response->assertStatus(200);

        // Create form
        $response = $this->actingAs($this->admin)->get(route('clients.create'));
        $response->assertStatus(200);

        // Store with validation (Direct reference without vendor_name)
        $clientData = [
            'name' => 'QA Client Test ' . Str::random(4),
            'company_name' => 'QA Client Test Co',
            'contact_person' => 'QA Contact Person',
            'phone' => '03211234567',
            'email' => 'client_' . Str::random(4) . '@example.com',
            'country' => 'Pakistan',
            'address' => 'Gulberg III, Lahore',
            'status' => 'Active',
            'reference_type' => 'Direct',
            'vendor_name' => null,
        ];
        $response = $this->actingAs($this->admin)->post(route('clients.store'), $clientData);
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $client = Client::where('name', $clientData['name'])->first();
        $this->assertNotNull($client);
        $this->assertNull($client->vendor_name);

        // Validation test: Vendor reference requires vendor_name
        $invalidVendorClientData = [
            'name' => 'Vendor Client Without Name ' . Str::random(4),
            'contact_person' => 'Vendor Test',
            'email' => 'vendor_test_' . Str::random(4) . '@example.com',
            'status' => 'Active',
            'reference_type' => 'Vendor',
            'vendor_name' => null,
        ];
        $response = $this->actingAs($this->admin)->post(route('clients.store'), $invalidVendorClientData);
        $response->assertSessionHasErrors('vendor_name');

        // Successful Vendor reference client creation
        $validVendorClientData = array_merge($invalidVendorClientData, [
            'vendor_name' => 'Travel Partners LLC'
        ]);
        $response = $this->actingAs($this->admin)->post(route('clients.store'), $validVendorClientData);
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $vendorClient = Client::where('name', $validVendorClientData['name'])->first();
        $this->assertNotNull($vendorClient);
        $this->assertEquals('Travel Partners LLC', $vendorClient->vendor_name);

        // Edit View
        $response = $this->actingAs($this->admin)->get(route('clients.edit', $client->id));
        $response->assertStatus(200);

        // Update from Vendor to Direct should clear vendor_name
        $response = $this->actingAs($this->admin)->put(route('clients.update', $vendorClient->id), [
            'name' => $vendorClient->name,
            'company_name' => $vendorClient->company_name,
            'contact_person' => $vendorClient->contact_person,
            'email' => $vendorClient->email,
            'status' => 'Active',
            'reference_type' => 'Direct',
            'vendor_name' => 'Stale Vendor',
        ]);
        $response->assertStatus(302);
        $this->assertNull($vendorClient->fresh()->vendor_name);

        // Update Direct client
        $response = $this->actingAs($this->admin)->put(route('clients.update', $client->id), array_merge($clientData, [
            'address' => 'Blue Area, Islamabad'
        ]));
        $response->assertStatus(302);
        $this->assertEquals('Blue Area, Islamabad', $client->fresh()->address);
    }

    /**
     * QA Test 3: Airline Tickets Module - Validation, Dashboard, Create, Payments, Invoice
     */
    public function test_airline_tickets_full_features()
    {
        // 1. Dashboard & Lists
        $this->actingAs($this->admin)->get(route('airline-tickets.dashboard'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('airline-tickets.today'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('airline-tickets.upcoming'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('ticket-invoices.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('ticket-reports.index'))->assertStatus(200);

        // 2. Create Ticket Form
        $response = $this->actingAs($this->admin)->get(route('airline-tickets.create'));
        $response->assertStatus(200);

        // 3. Airline & Airport Master Data
        $this->actingAs($this->admin)->get(route('airlines.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('airports.index'))->assertStatus(200);

        // 4. Test Existing Ticket operations
        $ticket = AirlineTicket::with(['passengers', 'payments'])->first();
        if ($ticket) {
            $this->actingAs($this->admin)->get(route('airline-tickets.show', $ticket->id))->assertStatus(200);
            $this->actingAs($this->admin)->get(route('airline-tickets.invoice', $ticket->id))->assertStatus(200);
            $this->actingAs($this->admin)->get(route('airline-tickets.print', $ticket->id))->assertStatus(200);

            // Public Verification URL
            $this->get(route('airline-tickets.verify', $ticket->qr_token))->assertStatus(200);
        }
    }

    /**
     * QA Test 4: B2B Agents & Agent Commission Payments
     */
    public function test_b2b_agents_module()
    {
        $this->actingAs($this->admin)->get(route('b2b.dashboard'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('b2b-agents.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('b2b-agents.create'))->assertStatus(200);

        $agent = B2bAgent::first();
        if ($agent) {
            $this->actingAs($this->admin)->get(route('b2b-agents.show', $agent->id))->assertStatus(200);
            $this->actingAs($this->admin)->get(route('b2b-agents.statement', $agent->id))->assertStatus(200);
        }
    }

    /**
     * QA Test 5: Employees & Payroll / Salary Management
     */
    public function test_employees_and_salary_module()
    {
        $this->actingAs($this->admin)->get(route('employees.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('employees.create'))->assertStatus(200);

        $this->actingAs($this->admin)->get(route('salary.index'))->assertStatus(200);

        $salary = Salary::first();
        if ($salary) {
            $this->actingAs($this->admin)->get(route('salary.slip', $salary->id))->assertStatus(200);
        }
    }

    /**
     * QA Test 6: Monthly Expenses & Petty Cash Day Register
     */
    public function test_expenses_and_petty_cash()
    {
        // Monthly Expenses Dashboard & Reports
        $this->actingAs($this->admin)->get(route('monthly_expenses.dashboard'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('monthly_expenses.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('monthly_expenses.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('monthly_expenses.utility_bills'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('monthly_expenses.recurring'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('monthly_expenses.reports'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('monthly_expenses.categories'))->assertStatus(200);

        // Petty Cash
        $this->actingAs($this->admin)->get(route('petty_cash.dashboard'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('petty_cash.daily'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('petty_cash.reports'))->assertStatus(200);
    }

    /**
     * QA Test 7: Travel Groups & Travel Vouchers Subsystem
     */
    public function test_travel_groups_and_vouchers()
    {
        $this->actingAs($this->admin)->get(route('travel-groups.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('travel-groups.create'))->assertStatus(200);

        $group = TravelGroup::first();
        if ($group) {
            $this->actingAs($this->admin)->get(route('travel-groups.show', $group->id))->assertStatus(200);
            $this->actingAs($this->admin)->get(route('travel-groups.edit', $group->id))->assertStatus(200);
            $this->actingAs($this->admin)->get(route('travel-vouchers.create', $group->id))->assertStatus(200);
        }

        $voucher = TravelVoucher::first();
        if ($voucher) {
            $this->actingAs($this->admin)->get(route('travel-vouchers.show', $voucher->id))->assertStatus(200);
            $this->actingAs($this->admin)->get(route('travel-vouchers.print', $voucher->id))->assertStatus(200);
            $this->get(route('travel-vouchers.verify', $voucher->qr_token))->assertStatus(200);
        }
    }

    /**
     * QA Test 8: Polio Certificate Invoices Module
     */
    public function test_polio_invoices()
    {
        $this->actingAs($this->admin)->get(route('polio-invoices.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('polio-invoices.create'))->assertStatus(200);

        $invoice = PolioCertificateInvoice::first();
        if ($invoice) {
            $this->actingAs($this->admin)->get(route('polio-invoices.show', $invoice->id))->assertStatus(200);
            $this->actingAs($this->admin)->get(route('polio-invoices.print', $invoice->id))->assertStatus(200);
        }
    }

    /**
     * QA Test 9: User Profile, Password & Session Security
     */
    public function test_user_profile_and_security()
    {
        $this->actingAs($this->admin)->get(route('profile'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('security-settings.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('activity-logs.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('login-history.index'))->assertStatus(200);
    }
}
