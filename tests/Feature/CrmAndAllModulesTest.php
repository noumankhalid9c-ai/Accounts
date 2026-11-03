<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\CrmLead;
use App\Models\CrmLeadComment;

class CrmAndAllModulesTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::find(1);
    }

    public function test_crm_dashboard_loads_without_errors(): void
    {
        $response = $this->actingAs($this->admin)->get(route('crm.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('CRM', false);
    }

    public function test_crm_leads_page_loads_without_errors(): void
    {
        $response = $this->actingAs($this->admin)->get(route('crm.leads'));
        $response->assertStatus(200);
        $response->assertSee('Inquiries', false);
    }

    public function test_crm_pipeline_page_loads_without_errors(): void
    {
        $response = $this->actingAs($this->admin)->get(route('crm.pipeline'));
        $response->assertStatus(200);
        $response->assertSee('Pipeline', false);
    }

    public function test_crm_lead_details_page_loads_without_errors(): void
    {
        $lead = CrmLead::first();
        $this->assertNotNull($lead, 'Lead must exist for details view');
        $response = $this->actingAs($this->admin)->get(route('crm.leads.show', $lead->id));
        $response->assertStatus(200);
        $response->assertSee($lead->customer_name);
    }

    public function test_crm_all_buttons_and_form_submissions(): void
    {
        // 1. Test creating new inquiry / lead button & form
        $newLeadData = [
            'customer_name' => 'Button Test Client',
            'phone' => '03451122334',
            'inquiry_type' => 'Umrah',
            'destination' => 'Makkah',
            'travel_date' => date('Y-m-d', strtotime('+45 days')),
            'pax' => 2,
            'source' => 'Direct',
            'notes' => 'Testing form submission',
        ];

        $createResponse = $this->actingAs($this->admin)->post(route('crm.leads.store'), $newLeadData);
        $createResponse->assertRedirect(route('crm.leads'));
        $createResponse->assertSessionHas('success');

        $createdLead = CrmLead::where('phone', '03451122334')->first();
        $this->assertNotNull($createdLead);

        // 2. Test updating stage button (Pipeline stage drag/change)
        $stageResponse = $this->actingAs($this->admin)->post(route('crm.leads.stage', $createdLead->id), [
            'stage' => 'Quotation Sent'
        ]);
        $stageResponse->assertSessionHas('success');
        $this->assertEquals('Quotation Sent', $createdLead->fresh()->stage);

        // 3. Test scheduling follow-up button & form
        $followupResponse = $this->actingAs($this->admin)->post(route('crm.leads.followup', $createdLead->id), [
            'followup_date' => date('Y-m-d', strtotime('+2 days')),
            'type' => 'Phone Call',
            'notes' => 'Call to discuss itinerary options',
        ]);
        $followupResponse->assertSessionHas('success');

        // 4. Test posting comment button
        $commentResponse = $this->actingAs($this->admin)->post(route('crm.leads.comments.store', $createdLead->id), [
            'comment' => 'Automated test comment on lead.'
        ]);
        $commentResponse->assertSessionHas('success');

        $comment = CrmLeadComment::where('lead_id', $createdLead->id)->latest()->first();
        $this->assertNotNull($comment);

        // 5. Test deleting comment
        $deleteCommentResponse = $this->actingAs($this->admin)->delete(route('crm.leads.comments.destroy', [$createdLead->id, $comment->id]));
        $deleteCommentResponse->assertSessionHas('success');

        // Clean up test lead
        $createdLead->forceDelete();
    }

    public function test_all_sidebar_navigation_pages_load_cleanly(): void
    {
        $sidebarRoutes = [
            'Main Dashboard' => route('dashboard'),
            'Clients Index' => route('clients.index'),
            'Travel Groups' => route('travel-groups.index'),
            'B2B Dashboard' => route('b2b.dashboard'),
            'Airline Tickets Dashboard' => route('airline-tickets.dashboard'),
            'Airline Tickets Index' => route('airline-tickets.index'),
            'Airline Tickets Create' => route('airline-tickets.create'),
            'Airline Tickets Today' => route('airline-tickets.today'),
            'Airline Tickets Upcoming' => route('airline-tickets.upcoming'),
            'Airlines' => route('airlines.index'),
            'Airports' => route('airports.index'),
            'Ticket Invoices' => route('ticket-invoices.index'),
            'Polio Invoices' => route('polio-invoices.index'),
            'Ticket Reports' => route('ticket-reports.index'),
            'Employees' => route('employees.index'),
            'Salaries' => route('salary.index'),
            'Monthly Expenses Dashboard' => route('monthly_expenses.dashboard'),
            'Monthly Expenses Create' => route('monthly_expenses.create'),
            'Monthly Expenses Index' => route('monthly_expenses.index'),
            'Monthly Expenses Utility Bills' => route('monthly_expenses.utility_bills'),
            'Monthly Expenses Recurring' => route('monthly_expenses.recurring'),
            'Monthly Expenses Reports' => route('monthly_expenses.reports'),
            'Monthly Expenses Categories' => route('monthly_expenses.categories'),
            'Bookings Index' => route('bookings.index'),
            'Petty Cash Dashboard' => route('petty_cash.dashboard'),
            'Petty Cash Daily' => route('petty_cash.daily'),
            'Petty Cash Reports' => route('petty_cash.reports'),
            'CRM Dashboard' => route('crm.dashboard'),
            'CRM Inquiries / Leads' => route('crm.leads'),
            'CRM Sales Pipeline' => route('crm.pipeline'),
            'Users Index' => route('users.index'),
            'Users Create' => route('users.create'),
            'Roles Index' => route('roles.index'),
            'Activity Logs' => route('activity-logs.index'),
            'Login History' => route('login-history.index'),
            'Security Settings' => route('security-settings.index'),
            'User Profile' => route('profile'),
        ];

        foreach ($sidebarRoutes as $label => $url) {
            $response = $this->actingAs($this->admin)->get($url);
            $this->assertEquals(200, $response->getStatusCode(), "Failed loading page: {$label} at {$url}");
        }
    }
}
