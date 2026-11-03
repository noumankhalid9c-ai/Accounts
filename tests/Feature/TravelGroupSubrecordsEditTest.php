<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TravelGroup;
use App\Models\GroupClient;
use App\Models\GroupFlight;
use App\Models\GroupHotel;
use App\Models\GroupTransport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelGroupSubrecordsEditTest extends TestCase
{
    protected $adminUser;
    protected $group;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->adminUser = User::where('email', 'admin@madinashareef.com')->first();
        if (!$this->adminUser) {
            $this->adminUser = User::first();
        }

        $this->group = TravelGroup::firstOrCreate(
            ['group_id' => 'GRP-TEST-SUBRECORDS'],
            [
                'group_name' => 'Subrecords Test Group',
                'service_type' => 'Umrah',
                'status' => 'Active',
            ]
        );
    }

    public function test_can_update_client_all_fields()
    {
        $client = GroupClient::create([
            'travel_group_id' => $this->group->id,
            'client_name' => 'Original Client Name',
            'pax_type' => 'Adult',
            'booking_status' => 'Pending',
            'payment_status' => 'Pending',
        ]);

        $updateData = [
            'client_name' => 'Updated Client Name',
            'father_name' => 'Updated Father Name',
            'gender' => 'Male',
            'passport_number' => 'AB9876543',
            'passport_expiry' => '2030-05-20',
            'phone' => '+923009998877',
            'package' => '21 Days Deluxe Umrah',
            'pax_type' => 'Adult',
            'room_type' => 'Double',
            'bed' => 'Bed 1',
            'group_number' => 'BATCH-A1',
            'visa_number' => 'VISA-445566',
            'pnr' => 'PNR123',
            'booking_status' => 'Confirmed',
            'payment_status' => 'Paid',
            'notes' => 'Requires wheelchair assistance',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('travel-groups.clients.update', [$this->group->id, $client->id]), $updateData);

        $response->assertSessionHas('success');

        $client->refresh();
        $this->assertEquals('Updated Client Name', $client->client_name);
        $this->assertEquals('Updated Father Name', $client->father_name);
        $this->assertEquals('AB9876543', $client->passport_number);
        $this->assertEquals('2030-05-20', $client->passport_expiry->format('Y-m-d'));
        $this->assertEquals('Confirmed', $client->booking_status);
        $this->assertEquals('Paid', $client->payment_status);
        $this->assertEquals('Requires wheelchair assistance', $client->notes);

        // Clean up
        $client->delete();
    }

    public function test_can_update_flight_all_fields()
    {
        $flight = GroupFlight::create([
            'travel_group_id' => $this->group->id,
            'flight_type' => 'Departure',
            'flight_number' => 'PK-739',
            'sector' => 'ISB - JED',
        ]);

        $updateData = [
            'flight_type' => 'Return',
            'flight_number' => 'SV-738',
            'sector' => 'MED - LHE',
            'departure_date' => '2026-11-20',
            'departure_time' => '16:00',
            'arrival_date' => '2026-11-21',
            'arrival_time' => '01:30',
            'notes' => 'Terminal 1, 40kg baggage',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('travel-groups.flights.update', [$this->group->id, $flight->id]), $updateData);

        $response->assertSessionHas('success');

        $flight->refresh();
        $this->assertEquals('Return', $flight->flight_type);
        $this->assertEquals('SV-738', $flight->flight_number);
        $this->assertEquals('MED - LHE', $flight->sector);
        $this->assertEquals('2026-11-20', $flight->departure_date->format('Y-m-d'));
        $this->assertEquals('16:00', $flight->departure_time);
        $this->assertEquals('Terminal 1, 40kg baggage', $flight->notes);

        // Clean up
        $flight->delete();
    }

    public function test_can_update_hotel_all_fields()
    {
        $hotel = GroupHotel::create([
            'travel_group_id' => $this->group->id,
            'city' => 'Makkah',
            'hotel_name' => 'Old Hotel',
        ]);

        $updateData = [
            'city' => 'Madinah',
            'hotel_name' => 'Dar Al Taqwa Madinah',
            'view' => 'Haram View',
            'meal' => 'Bed & Breakfast',
            'confirmation_number' => 'BRN-998877',
            'room_type' => 'Quad Room',
            'check_in' => '2026-11-25',
            'check_out' => '2026-11-30',
            'nights' => 5,
            'notes' => 'Connected rooms requested',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('travel-groups.hotels.update', [$this->group->id, $hotel->id]), $updateData);

        $response->assertSessionHas('success');

        $hotel->refresh();
        $this->assertEquals('Madinah', $hotel->city);
        $this->assertEquals('Dar Al Taqwa Madinah', $hotel->hotel_name);
        $this->assertEquals('Haram View', $hotel->view);
        $this->assertEquals('BRN-998877', $hotel->confirmation_number);
        $this->assertEquals(5, $hotel->nights);

        // Clean up
        $hotel->delete();
    }

    public function test_can_update_transport_all_fields()
    {
        $transport = GroupTransport::create([
            'travel_group_id' => $this->group->id,
            'transporter' => 'Old Transporter',
            'type' => 'Bus',
        ]);

        $updateData = [
            'travel_date' => '2026-11-25',
            'transporter' => 'Al Safwa VIP Transport',
            'type' => 'GMC Yukon XL',
            'description' => 'VIP Airport to Hotel transfer',
            'pickup_location' => 'Jeddah Airport Hajj Terminal',
            'drop_location' => 'Makkah Clock Tower Hotel',
            'notes' => 'Driver: Brother Tariq (+966500112233)',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('travel-groups.transports.update', [$this->group->id, $transport->id]), $updateData);

        $response->assertSessionHas('success');

        $transport->refresh();
        $this->assertEquals('Al Safwa VIP Transport', $transport->transporter);
        $this->assertEquals('GMC Yukon XL', $transport->type);
        $this->assertEquals('Jeddah Airport Hajj Terminal', $transport->pickup_location);
        $this->assertEquals('Makkah Clock Tower Hotel', $transport->drop_location);

        // Clean up
        $transport->delete();
    }

    public function test_show_page_renders_edit_buttons_and_modals()
    {
        $client = GroupClient::create([
            'travel_group_id' => $this->group->id,
            'client_name' => 'Zayd Test Client',
            'pax_type' => 'Adult',
            'booking_status' => 'Confirmed',
            'payment_status' => 'Paid',
        ]);

        $flight = GroupFlight::create([
            'travel_group_id' => $this->group->id,
            'flight_type' => 'Departure',
            'flight_number' => 'SV-991',
        ]);

        $hotel = GroupHotel::create([
            'travel_group_id' => $this->group->id,
            'city' => 'Makkah',
            'hotel_name' => 'Fairmont Makkah Clock Royal Tower',
        ]);

        $transport = GroupTransport::create([
            'travel_group_id' => $this->group->id,
            'transporter' => 'Rawafed Transport Co',
            'type' => 'Coaster',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('travel-groups.show', $this->group->id));

        $response->assertStatus(200);
        $response->assertSee('Zayd Test Client');
        $response->assertSee('editClientModal_' . $client->id);
        $response->assertSee('editFlightModal_' . $flight->id);
        $response->assertSee('editHotelModal_' . $hotel->id);
        $response->assertSee('editTransportModal_' . $transport->id);

        // Clean up
        $client->delete();
        $flight->delete();
        $hotel->delete();
        $transport->delete();
        $this->group->delete();
    }
}
