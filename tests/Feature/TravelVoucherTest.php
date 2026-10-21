<?php

namespace Tests\Feature;

use App\Models\GroupFlight;
use App\Models\TravelGroup;
use App\Models\TravelVoucher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelVoucherTest extends TestCase
{
    public function test_can_create_voucher_with_flight_numbers_and_comments(): void
    {
        $superAdmin = User::where('role_id', 1)->first() ?? User::factory()->create(['role_id' => 1]);

        $group = TravelGroup::create([
            'group_id' => 'GRP-TEST-' . rand(1000, 9999),
            'group_name' => 'Umrah Test Group',
            'service_type' => 'Umrah',
            'status' => 'Active',
            'departure_date' => '2026-11-01',
            'return_date' => '2026-11-15',
        ]);

        $voucherData = [
            'voucher_number' => 'VOU-TEST-' . rand(10000, 99999),
            'voucher_type' => 'Umrah',
            'voucher_date' => '2026-10-21',
            'group_head' => 'Muhammad Ali',
            'pax' => 4,
            'whatsapp' => '+923001234567',
            'comments' => 'VIP Airport Meet & Assist requested. Wheelchair required for elderly pax.',
            'special_instructions' => 'Keep passports safe with team lead.',
            
            // Direct flight inputs
            'dep_flight_number' => 'SV-735',
            'dep_sector' => 'LHE - JED',
            'dep_date' => '2026-11-01',
            'dep_time' => '14:30',
            'ret_flight_number' => 'SV-736',
            'ret_sector' => 'JED - LHE',
            'ret_date' => '2026-11-15',
            'ret_time' => '02:15',
        ];

        $response = $this->actingAs($superAdmin)
            ->post(route('travel-vouchers.store', $group->id), $voucherData);

        $response->assertSessionHasNoErrors();

        $voucher = TravelVoucher::where('voucher_number', $voucherData['voucher_number'])->first();
        $this->assertNotNull($voucher);
        $this->assertEquals('VIP Airport Meet & Assist requested. Wheelchair required for elderly pax.', $voucher->comments);

        // Check that flights were created on the group and snapshot
        $this->assertEquals('SV-735 / SV-736', $voucher->flight_numbers_summary);

        // Check show page renders flight numbers and comments
        $showResponse = $this->actingAs($superAdmin)->get(route('travel-vouchers.show', $voucher->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('SV-735');
        $showResponse->assertSee('SV-736');
        $showResponse->assertSee('VIP Airport Meet & Assist requested');

        // Check print page renders flight numbers and comments
        $printResponse = $this->actingAs($superAdmin)->get(route('travel-vouchers.print', $voucher->id));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('SV-735');
        $printResponse->assertSee('SV-736');
        $printResponse->assertSee('Comments / Remarks');
        $printResponse->assertSee('VIP Airport Meet & Assist requested');

        // Check update voucher comments
        $updateResponse = $this->actingAs($superAdmin)->put(route('travel-vouchers.update', $voucher->id), [
            'voucher_type' => 'Umrah',
            'voucher_date' => '2026-10-21',
            'group_head' => 'Muhammad Ali Updated',
            'pax' => 5,
            'whatsapp' => '+923001234567',
            'comments' => 'Updated comments: 5 pax total, transport bus confirmed.',
            'dep_flight_number' => 'SV-737',
            'dep_sector' => 'LHE - JED',
            'dep_date' => '2026-11-01',
            'dep_time' => '15:00',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertEquals('Updated comments: 5 pax total, transport bus confirmed.', $voucher->fresh()->comments);
        $this->assertStringContainsString('SV-737', $voucher->fresh()->flight_numbers_summary);

        // Clean up
        $voucher->forceDelete();
        $group->flights()->forceDelete();
        $group->forceDelete();
    }
}
