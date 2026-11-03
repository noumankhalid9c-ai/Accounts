<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Client;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\TravelGroup;
use App\Models\TravelVoucher;
use App\Models\B2bAgent;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\AirlineTicket;
use App\Models\TicketPassenger;
use App\Models\TicketInvoice;
use App\Models\PolioCertificateInvoice;
use App\Models\PolioInvoicePayment;
use App\Models\SecuritySetting;
use Illuminate\Support\Str;

class ClientDeliveryVerificationTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::find(1);
    }

    /**
     * 1. User Management & User Roles Verification
     */
    public function test_user_management_and_roles_full_workflow(): void
    {
        // A. View Users List
        $res = $this->actingAs($this->admin)->get(route('users.index'));
        $res->assertStatus(200);

        // B. View Create User Page
        $res = $this->actingAs($this->admin)->get(route('users.create'));
        $res->assertStatus(200);

        // C. Create a New User
        $userData = [
            'name' => 'Delivery Staff ' . Str::random(4),
            'username' => 'staff_' . Str::lower(Str::random(6)),
            'email' => 'staff_' . Str::random(6) . '@example.com',
            'role_id' => 4, // Sales / CRM
            'designation' => 'Sales Officer',
            'department' => 'Ticketing & Sales',
            'mobile' => '03001239876',
            'password' => 'Pass123456!',
            'password_confirmation' => 'Pass123456!',
            'must_change_password' => 0,
            'status' => 'Active',
        ];

        $res = $this->actingAs($this->admin)->post(route('users.store'), $userData);
        $res->assertRedirect(route('users.index'));
        $res->assertSessionHas('success');

        $user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($user);

        // D. View User Profile / Details
        $res = $this->actingAs($this->admin)->get(route('users.show', $user->id));
        $res->assertStatus(200);
        $res->assertSee($user->name);

        // E. View Edit User Page
        $res = $this->actingAs($this->admin)->get(route('users.edit', $user->id));
        $res->assertStatus(200);

        // F. Update User (redirects to users.index with success)
        $res = $this->actingAs($this->admin)->put(route('users.update', $user->id), [
            'name' => $user->name . ' Updated',
            'username' => $user->username,
            'email' => $user->email,
            'role_id' => 4,
            'designation' => 'Senior Sales Officer',
            'status' => 'Active',
        ]);
        $res->assertRedirect(route('users.index'));
        $res->assertSessionHas('success');

        // G. Test Generate Temporary Password Button
        $res = $this->actingAs($this->admin)->post(route('users.reset_password', $user->id));
        $res->assertRedirect();
        $res->assertSessionHas('temp_password_generated');

        // H. View Roles & Permissions
        $res = $this->actingAs($this->admin)->get(route('roles.index'));
        $res->assertStatus(200);

        // I. View Specific Role Edit
        $res = $this->actingAs($this->admin)->get(route('roles.edit', 4));
        $res->assertStatus(200);

        // J. View Activity Logs & Login History
        $res = $this->actingAs($this->admin)->get(route('activity-logs.index'));
        $res->assertStatus(200);
        $res = $this->actingAs($this->admin)->get(route('login-history.index'));
        $res->assertStatus(200);

        // K. View Security Settings
        $res = $this->actingAs($this->admin)->get(route('security-settings.index'));
        $res->assertStatus(200);

        // Clean up test user
        $user->forceDelete();
    }

    /**
     * 2. Clients Module Full Workflow
     */
    public function test_clients_module_full_workflow(): void
    {
        // A. View Clients List
        $res = $this->actingAs($this->admin)->get(route('clients.index'));
        $res->assertStatus(200);

        // B. View Create Client Form
        $res = $this->actingAs($this->admin)->get(route('clients.create'));
        $res->assertStatus(200);

        // C. Create Client
        $clientData = [
            'company_name' => 'Al-Madina Group ' . Str::random(3),
            'contact_person' => 'Chaudhry Akram',
            'email' => 'client_' . Str::random(5) . '@example.com',
            'phone' => '0300' . rand(1000000, 9999999),
            'reference_type' => 'Direct',
            'status' => 'Active',
            'address' => 'Mall Road, Lahore',
            'country' => 'Pakistan',
        ];

        $res = $this->actingAs($this->admin)->post(route('clients.store'), $clientData);
        $res->assertRedirect(route('clients.index'));
        $res->assertSessionHas('success');

        $client = Client::where('email', $clientData['email'])->first();
        $this->assertNotNull($client);

        // D. View Client Details & Ledger
        $res = $this->actingAs($this->admin)->get(route('clients.show', $client->id));
        $res->assertStatus(200);
        $res->assertSee($client->contact_person);

        // E. View Edit Client Form
        $res = $this->actingAs($this->admin)->get(route('clients.edit', $client->id));
        $res->assertStatus(200);

        // Clean up
        $client->forceDelete();
    }

    /**
     * 3. B2B Agent Management Workflow
     */
    public function test_b2b_agents_module_full_workflow(): void
    {
        // A. B2B Dashboard
        $res = $this->actingAs($this->admin)->get(route('b2b.dashboard'));
        $res->assertStatus(200);

        // B. B2B Agents List
        $res = $this->actingAs($this->admin)->get(route('b2b-agents.index'));
        $res->assertStatus(200);

        // C. Create B2B Agent
        $agentData = [
            'agent_name' => 'Shaban Ali',
            'company_name' => 'Al-Haram Travels ' . Str::random(3),
            'contact_person' => 'Shaban Ali',
            'phone' => '0321' . rand(1000000, 9999999),
            'email' => 'agent_' . Str::random(5) . '@alharam.com',
            'status' => 'Active',
            'address' => 'G.T. Road, Gujranwala',
            'city' => 'Gujranwala',
            'country' => 'Pakistan',
        ];

        $res = $this->actingAs($this->admin)->post(route('b2b-agents.store'), $agentData);
        $res->assertRedirect(route('b2b-agents.index'));
        $res->assertSessionHas('success');

        $agent = B2bAgent::where('email', $agentData['email'])->first();
        $this->assertNotNull($agent);

        // D. View Agent Details Profile
        $res = $this->actingAs($this->admin)->get(route('b2b-agents.show', $agent->id));
        $res->assertStatus(200);

        // Clean up
        $agent->forceDelete();
    }

    /**
     * 4. Travel Groups and Travel Vouchers Workflow
     */
    public function test_travel_groups_and_vouchers_full_workflow(): void
    {
        // A. Travel Groups List
        $res = $this->actingAs($this->admin)->get(route('travel-groups.index'));
        $res->assertStatus(200);

        // B. Create Travel Group Form
        $res = $this->actingAs($this->admin)->get(route('travel-groups.create'));
        $res->assertStatus(200);

        // C. Create Travel Group
        $groupData = [
            'group_id' => 'GRP-' . rand(1000, 9999),
            'group_name' => '15 Days Ramadan Group ' . Str::random(3),
            'service_type' => 'Umrah',
            'destination' => 'Makkah & Madinah',
            'departure_date' => date('Y-m-d', strtotime('+20 days')),
            'return_date' => date('Y-m-d', strtotime('+35 days')),
            'status' => 'Planning',
        ];

        $res = $this->actingAs($this->admin)->post(route('travel-groups.store'), $groupData);
        $res->assertRedirect(route('travel-groups.index'));
        $res->assertSessionHas('success');

        $group = TravelGroup::where('group_id', $groupData['group_id'])->first();
        $this->assertNotNull($group);

        // D. Group Details View (showing allocations, vouchers, flights, hotels, transport)
        $res = $this->actingAs($this->admin)->get(route('travel-groups.show', $group->id));
        $res->assertStatus(200);

        // E. Create Voucher Form for the Group
        $res = $this->actingAs($this->admin)->get(route('travel-vouchers.create', $group->id));
        $res->assertStatus(200);

        // F. Create Voucher for the Group
        $voucherData = [
            'voucher_number' => 'VCH-' . rand(10000, 99999),
            'voucher_type' => 'Full Service Umrah',
            'voucher_date' => date('Y-m-d'),
            'group_head' => 'Pilgrim ' . Str::random(4),
            'pax' => 2,
            'whatsapp' => '0300' . rand(1000000, 9999999),
            'dep_flight_number' => 'SV-738',
            'dep_sector' => 'LHE - JED',
            'comments' => 'VIP Quad Sharing with Full Board',
        ];

        $res = $this->actingAs($this->admin)->post(route('travel-vouchers.store', $group->id), $voucherData);
        $res->assertSessionHas('success');

        $voucher = TravelVoucher::where('travel_group_id', $group->id)->latest()->first();
        $this->assertNotNull($voucher);
        $res->assertRedirect(route('travel-vouchers.show', $voucher->id));

        // G. View Voucher Details
        $res = $this->actingAs($this->admin)->get(route('travel-vouchers.show', $voucher->id));
        $res->assertStatus(200);

        // H. View Voucher Print View
        $res = $this->actingAs($this->admin)->get(route('travel-vouchers.print', $voucher->id));
        $res->assertStatus(200);

        // Clean up
        $voucher->forceDelete();
        $group->forceDelete();
    }

    /**
     * 5. Invoices (Airline Ticket Invoices & Polio Certificate Invoices)
     */
    public function test_invoices_modules_full_workflow(): void
    {
        // A. Airline Ticket Invoices List
        $res = $this->actingAs($this->admin)->get(route('ticket-invoices.index'));
        $res->assertStatus(200);

        // B. Polio Certificate Invoices List
        $res = $this->actingAs($this->admin)->get(route('polio-invoices.index'));
        $res->assertStatus(200);

        // C. Create Polio Certificate Invoice Form
        $res = $this->actingAs($this->admin)->get(route('polio-invoices.create'));
        $res->assertStatus(200);

        // D. Create Polio Certificate Invoice
        $polioData = [
            'invoice_date' => date('Y-m-d'),
            'client_name' => 'Muhammad Ali ' . Str::random(3),
            'phone' => '03009988112',
            'passport_number' => 'AB' . rand(1000000, 9999999),
            'persons' => 2,
            'cost_per_person' => 2500.00,
            'amount_paid' => 5000.00,
            'payment_method' => 'Cash',
            'notes' => 'Vaccination completed.',
        ];

        $res = $this->actingAs($this->admin)->post(route('polio-invoices.store'), $polioData);
        
        $polio = PolioCertificateInvoice::where('passport_number', $polioData['passport_number'])->first();
        $this->assertNotNull($polio);

        $res->assertRedirect(route('polio-invoices.show', $polio->id));
        $res->assertSessionHas('success');

        // E. View Polio Invoice Details
        $res = $this->actingAs($this->admin)->get(route('polio-invoices.show', $polio->id));
        $res->assertStatus(200);

        // F. View Polio Invoice Print View
        $res = $this->actingAs($this->admin)->get(route('polio-invoices.print', $polio->id));
        $res->assertStatus(200);

        // G. View Polio Invoice PDF Generation
        $res = $this->actingAs($this->admin)->get(route('polio-invoices.pdf', $polio->id));
        $res->assertStatus(200);
        $res->assertHeader('content-type', 'application/pdf');

        // Clean up
        $polio->forceDelete();
    }
}
