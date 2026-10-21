<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AuditLog;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserManagementAndRbacTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_super_admin_can_access_user_management_index(): void
    {
        $admin = User::find(1);
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('User Management');
        $response->assertSee('Add New User');
    }

    public function test_unauthorized_user_is_forbidden_from_user_management(): void
    {
        // Staff role has role_id 7 without users.view permission
        $staffUser = User::create([
            'user_code' => 'TEST-STAFF-' . rand(1000, 9999),
            'name' => 'Test Staff',
            'username' => 'teststaff_' . rand(100, 999),
            'email' => 'staff' . rand(1000, 9999) . '@example.com',
            'role_id' => 7,
            'status' => 'Active',
            'password_hash' => Hash::make('Secret123!'),
        ]);

        $response = $this->actingAs($staffUser)->get(route('users.index'));
        $response->assertStatus(403);

        $staffUser->forceDelete();
    }

    public function test_super_admin_can_create_user_and_audit_log_is_written(): void
    {
        $admin = User::find(1);

        $username = 'agent_' . rand(1000, 9999);
        $email = $username . '@madina.com';

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Agent Farooq',
            'username' => $username,
            'email' => $email,
            'mobile' => '+923001234567',
            'role_id' => 4, // Sales
            'status' => 'Active',
            'department' => 'Sales',
            'designation' => 'Sales Officer',
            'password' => 'Pass123456!',
            'password_confirmation' => 'Pass123456!',
            'must_change_password' => 1,
        ]);

        $response->assertRedirect(route('users.index'));

        $created = User::where('email', $email)->first();
        $this->assertNotNull($created);
        $this->assertEquals('Agent Farooq', $created->name);
        $this->assertTrue($created->must_change_password);

        // Verify audit log
        $log = AuditLog::where('action', 'user_created')->where('record_id', $created->id)->first();
        $this->assertNotNull($log);

        $created->forceDelete();
    }

    public function test_super_admin_cannot_delete_self(): void
    {
        $admin = User::find(1);
        $response = $this->actingAs($admin)->delete(route('users.destroy', $admin->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => 1, 'deleted_at' => null]);
    }

    public function test_last_active_super_admin_cannot_be_deactivated(): void
    {
        $admin = User::find(1);
        // Ensure no other active super admin
        User::where('role_id', 1)->where('id', '!=', 1)->delete();

        $response = $this->actingAs($admin)->post(route('users.toggle_status', 1), [
            'status' => 'Inactive',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('Active', $admin->fresh()->status);
    }

    public function test_must_change_password_redirects_user_to_force_change_screen(): void
    {
        $testUser = User::create([
            'user_code' => 'TEST-TEMP-' . rand(1000, 9999),
            'name' => 'Temp Password User',
            'username' => 'tempuser_' . rand(100, 999),
            'email' => 'temp' . rand(1000, 9999) . '@example.com',
            'role_id' => 4,
            'status' => 'Active',
            'must_change_password' => true,
            'password_hash' => Hash::make('TempPass123!'),
        ]);

        $response = $this->actingAs($testUser)->get(route('dashboard'));
        $response->assertRedirect(route('password.force_change'));

        // Perform password change
        $changeResponse = $this->actingAs($testUser)->post(route('password.force_change.post'), [
            'current_password' => 'TempPass123!',
            'password' => 'PermanentNewPass123!',
            'password_confirmation' => 'PermanentNewPass123!',
        ]);

        $changeResponse->assertRedirect(route('dashboard'));
        $this->assertFalse($testUser->fresh()->must_change_password);
        $this->assertTrue(Hash::check('PermanentNewPass123!', $testUser->fresh()->password_hash));

        $testUser->forceDelete();
    }

    public function test_inactive_and_suspended_accounts_are_blocked_from_login(): void
    {
        $suspendedUser = User::create([
            'user_code' => 'TEST-SUSP-' . rand(1000, 9999),
            'name' => 'Suspended User',
            'username' => 'susp_' . rand(100, 999),
            'email' => 'susp' . rand(1000, 9999) . '@example.com',
            'role_id' => 4,
            'status' => 'Suspended',
            'password_hash' => Hash::make('Secret123!'),
        ]);

        // Set session captcha
        session(['captcha_code' => 10]);

        $response = $this->post(route('login.post'), [
            'login' => $suspendedUser->email,
            'password' => 'Secret123!',
            'captcha' => 10,
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();

        $suspendedUser->forceDelete();
    }

    public function test_admin_can_generate_temporary_password_for_user(): void
    {
        $admin = User::find(1);

        $targetUser = User::create([
            'user_code' => 'TEST-RESET-' . rand(1000, 9999),
            'name' => 'Reset Target',
            'username' => 'resettarget_' . rand(100, 999),
            'email' => 'reset' . rand(1000, 9999) . '@example.com',
            'role_id' => 4,
            'status' => 'Active',
            'password_hash' => Hash::make('OldPassword123!'),
        ]);

        $response = $this->actingAs($admin)->post(route('users.reset_password', $targetUser->id), [
            'reset_type' => 'generate_temp',
        ]);

        $response->assertSessionHas('temp_password_generated');
        $generatedData = session('temp_password_generated');
        $this->assertNotEmpty($generatedData['password']);

        $fresh = $targetUser->fresh();
        $this->assertTrue($fresh->must_change_password);
        $this->assertTrue(Hash::check($generatedData['password'], $fresh->password_hash));

        $targetUser->forceDelete();
    }

    public function test_password_reset_flow_with_token(): void
    {
        $resetUser = User::create([
            'user_code' => 'TEST-FLOW-' . rand(1000, 9999),
            'name' => 'Flow User',
            'username' => 'flowuser_' . rand(100, 999),
            'email' => 'flowuser' . rand(1000, 9999) . '@example.com',
            'role_id' => 4,
            'status' => 'Active',
            'password_hash' => Hash::make('OldPassword123!'),
        ]);

        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $resetUser->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $resetUser->email,
            'password' => 'BrandNewPassword123!',
            'password_confirmation' => 'BrandNewPassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('BrandNewPassword123!', $resetUser->fresh()->password_hash));

        $resetUser->forceDelete();
    }

    public function test_user_without_dashboard_permission_is_redirected_to_their_operational_landing_page(): void
    {
        $user = User::create([
            'user_code' => 'TEST-NODASH-' . rand(1000, 9999),
            'name' => 'No Dashboard User',
            'username' => 'nodash_' . rand(100, 999),
            'email' => 'nodash' . rand(1000, 9999) . '@example.com',
            'role_id' => 4, // Sales / CRM User
            'status' => 'Active',
            'password_hash' => Hash::make('Secret123!'),
        ]);

        // Deny dashboard.view explicitly for this user
        $dashPerm = \App\Models\Permission::where('slug', 'dashboard.view')->first();
        if ($dashPerm) {
            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'permission_id' => $dashPerm->id,
                'is_granted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->assertFalse($user->fresh()->canDo('dashboard.view'));

        // Attempting to visit /dashboard should redirect to landing page (CRM dashboard or clients)
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect($user->getLandingUrl());
        $response->assertSessionHas('warning');

        // Test login redirect
        $loginResponse = $this->post(route('login.post'), [
            'login' => $user->email,
            'password' => 'Secret123!',
        ]);
        $loginResponse->assertRedirect($user->getLandingUrl());

        $user->forceDelete();
    }
}

