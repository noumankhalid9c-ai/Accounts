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

    public function test_voucher_create_and_edit_pages_have_final_review_and_edit_options(): void
    {
        $superAdmin = User::where('role_id', 1)->first() ?? User::factory()->create(['role_id' => 1]);

        $group = TravelGroup::create([
            'group_id' => 'GRP-REV-' . rand(1000, 9999),
            'group_name' => 'Review Test Group',
            'service_type' => 'Umrah',
            'status' => 'Active',
            'departure_date' => '2026-11-01',
            'return_date' => '2026-11-15',
        ]);

        // 1. Create page renders Final Review button & Review Modal with Edit option
        $createResponse = $this->actingAs($superAdmin)->get(route('travel-vouchers.create', $group->id));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Final Review Before Issuing');
        $createResponse->assertSee('Edit Details');
        $createResponse->assertSee('Confirm & Generate Final Voucher', false);

        $voucher = TravelVoucher::create([
            'travel_group_id' => $group->id,
            'voucher_number' => 'VOU-REV-' . rand(10000, 99999),
            'voucher_type' => 'Umrah',
            'voucher_date' => '2026-11-03',
            'pax' => 3,
            'qr_token' => \Illuminate\Support\Str::random(32),
            'created_by' => $superAdmin->id,
        ]);

        // 2. Edit page renders Review Changes button & modal with Back to Editing option
        $editResponse = $this->actingAs($superAdmin)->get(route('travel-vouchers.edit', $voucher->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Review Changes');
        $editResponse->assertSee('Back to Editing');
        $editResponse->assertSee('Confirm & Save Changes', false);

        // 3. Group show page has distinct Edit button
        $groupShowResponse = $this->actingAs($superAdmin)->get(route('travel-groups.show', $group->id));
        $groupShowResponse->assertStatus(200);
        $groupShowResponse->assertSee(route('travel-vouchers.edit', $voucher->id));

        $voucher->forceDelete();
        $group->forceDelete();
    }

    public function test_admin_can_delete_travel_voucher(): void
    {
        $superAdmin = User::where('role_id', 1)->first() ?? User::factory()->create(['role_id' => 1]);

        $group = TravelGroup::create([
            'group_id' => 'GRP-DEL-' . rand(1000, 9999),
            'group_name' => 'Delete Test Group',
            'service_type' => 'Umrah',
            'status' => 'Active',
            'departure_date' => '2026-11-01',
            'return_date' => '2026-11-15',
        ]);

        $voucher = TravelVoucher::create([
            'travel_group_id' => $group->id,
            'voucher_number' => 'VOU-DEL-' . rand(10000, 99999),
            'voucher_type' => 'Umrah',
            'voucher_date' => '2026-11-03',
            'pax' => 2,
            'qr_token' => \Illuminate\Support\Str::random(32),
            'created_by' => $superAdmin->id,
        ]);

        // Verify admin sees delete option on group show and voucher show
        $groupShowResponse = $this->actingAs($superAdmin)->get(route('travel-groups.show', $group->id));
        $groupShowResponse->assertStatus(200);
        $groupShowResponse->assertSee('title="Delete Voucher"', false);

        $voucherShowResponse = $this->actingAs($superAdmin)->get(route('travel-vouchers.show', $voucher->id));
        $voucherShowResponse->assertStatus(200);
        $voucherShowResponse->assertSee('Delete Voucher');

        // Perform delete as admin
        $deleteResponse = $this->actingAs($superAdmin)
            ->delete(route('travel-vouchers.destroy', $voucher->id));

        $deleteResponse->assertRedirect(route('travel-groups.show', $group->id));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('travel_vouchers', ['id' => $voucher->id]);

        $group->forceDelete();
    }

    public function test_non_admin_cannot_delete_travel_voucher(): void
    {
        $superAdmin = User::where('role_id', 1)->first() ?? User::factory()->create(['role_id' => 1]);
        $salesUser = User::where('role_id', 4)->first() ?? User::factory()->create(['role_id' => 4]);

        $group = TravelGroup::create([
            'group_id' => 'GRP-DEL2-' . rand(1000, 9999),
            'group_name' => 'Delete Restrict Test Group',
            'service_type' => 'Umrah',
            'status' => 'Active',
            'departure_date' => '2026-11-01',
            'return_date' => '2026-11-15',
        ]);

        $voucher = TravelVoucher::create([
            'travel_group_id' => $group->id,
            'voucher_number' => 'VOU-RESTRICT-' . rand(10000, 99999),
            'voucher_type' => 'Umrah',
            'voucher_date' => '2026-11-03',
            'pax' => 2,
            'qr_token' => \Illuminate\Support\Str::random(32),
            'created_by' => $superAdmin->id,
        ]);

        // Verify non-admin DOES NOT see delete button on group show and voucher show
        $groupShowResponse = $this->actingAs($salesUser)->get(route('travel-groups.show', $group->id));
        $groupShowResponse->assertStatus(200);
        $groupShowResponse->assertDontSee('title="Delete Voucher"', false);

        $voucherShowResponse = $this->actingAs($salesUser)->get(route('travel-vouchers.show', $voucher->id));
        $voucherShowResponse->assertStatus(200);
        $voucherShowResponse->assertDontSee('Delete Voucher');

        // Attempt delete as non-admin -> must be rejected (403 Forbidden)
        $deleteResponse = $this->actingAs($salesUser)
            ->delete(route('travel-vouchers.destroy', $voucher->id));

        $deleteResponse->assertStatus(403);

        // Voucher must still exist
        $this->assertDatabaseHas('travel_vouchers', ['id' => $voucher->id]);

        // Clean up
        $voucher->forceDelete();
        $group->forceDelete();
    }
}
