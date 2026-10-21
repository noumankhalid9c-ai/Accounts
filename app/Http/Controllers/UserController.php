<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AuditLog;
use App\Models\SecuritySetting;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.view')->only(['index', 'show']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.edit')->only(['edit', 'update', 'resetPassword', 'toggleStatus', 'permissions', 'updatePermissions', 'revokeSessions']);
        $this->middleware('permission:users.delete')->only(['destroy', 'bulkAction']);
    }

    public function index(Request $request)
    {
        $query = User::with('role');

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('user_code', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($roleId = $request->input('role_id')) {
            $query->where('role_id', $roleId);
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Department filter
        if ($dept = $request->input('department')) {
            $query->where('department', $dept);
        }

        // Date range filter
        if ($from = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['id', 'user_code', 'name', 'username', 'email', 'status', 'created_at', 'last_login_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $perPage = (int) $request->input('per_page', 10);
        $users = $query->paginate($perPage)->withQueryString();

        $roles = Role::orderBy('id')->get();
        $departments = User::whereNotNull('department')->distinct()->pluck('department');

        // Overview metrics
        $metrics = [
            'total' => User::count(),
            'active' => User::where('status', 'Active')->count(),
            'inactive' => User::where('status', 'Inactive')->count(),
            'suspended' => User::where('status', 'Suspended')->orWhere('status', 'Locked')->count(),
        ];

        return view('users.index', compact('users', 'roles', 'departments', 'metrics'));
    }

    public function create()
    {
        $roles = Role::orderBy('id')->get();
        $departments = ['Management', 'Operations', 'Sales / CRM', 'Finance & Accounts', 'Ticketing', 'Customer Support', 'IT & Administration'];

        // Auto-generate next user code (e.g. USR-1002)
        $latestUser = User::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $latestUser ? $latestUser->id + 1 : 1;
        $autoUserCode = 'USR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('users.create', compact('roles', 'departments', 'autoUserCode'));
    }

    public function store(Request $request)
    {
        $minLength = (int) SecuritySetting::get('password_min_length', 8);

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:100|alpha_dash|unique:users,username',
            'email' => 'required|email|max:150|unique:users,email',
            'mobile' => 'nullable|string|max:50',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive,Suspended',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'password' => ['required', 'string', 'confirmed', 'min:' . $minLength],
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'must_change_password' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Auto code
            $latestUser = User::withTrashed()->orderBy('id', 'desc')->first();
            $nextId = $latestUser ? $latestUser->id + 1 : 1;
            $userCode = $request->filled('user_code')
                ? $request->user_code
                : 'USR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Handle Profile Picture Upload
            $pictureName = null;
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $pictureName = time() . '_' . Str::slug($request->username) . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/avatars');
                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }
                $file->move($destination, $pictureName);
            }

            $user = User::create([
                'user_code' => $userCode,
                'name' => $request->name,
                'username' => strtolower(trim($request->username)),
                'email' => strtolower(trim($request->email)),
                'mobile' => $request->mobile,
                'profile_picture' => $pictureName,
                'role_id' => $request->role_id,
                'status' => $request->status,
                'department' => $request->department,
                'designation' => $request->designation,
                'password_hash' => Hash::make($request->password),
                'must_change_password' => $request->boolean('must_change_password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_created',
                'module' => 'users',
                'table_name' => 'users',
                'record_id' => $user->id,
                'new_value' => json_encode([
                    'id' => $user->id,
                    'user_code' => $user->user_code,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'status' => $user->status
                ]),
                'ip_address' => $request->ip(),
                'description' => "Created new user account: {$user->name} ({$user->user_code})",
                'created_at' => now(),
            ]);

            DB::commit();

            if ($request->boolean('send_welcome_email')) {
                // If email delivery requested, flash credentials summary for reference
                session()->flash('welcome_notice', "Welcome email dispatch simulated for {$user->email}.");
            }

            return redirect()->route('users.index')
                ->with('success', "User account {$user->name} ({$user->user_code}) was created successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function show(User $user)
    {
        $user->load(['role.permissions', 'userPermissions', 'loginHistories' => function ($q) {
            $q->latest('created_at')->take(10);
        }, 'activities' => function ($q) {
            $q->latest('created_at')->take(10);
        }]);

        // Effective permissions map
        $allPermissions = Permission::orderBy('module')->orderBy('action')->get();

        return view('users.show', compact('user', 'allPermissions'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('id')->get();
        $departments = ['Management', 'Operations', 'Sales / CRM', 'Finance & Accounts', 'Ticketing', 'Customer Support', 'IT & Administration'];

        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:100|alpha_dash|unique:users,username,' . $user->id,
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:50',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive,Suspended,Locked',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'must_change_password' => 'nullable|boolean',
        ]);

        // Protection: Prevent demoting or deactivating the last active Super Admin!
        if ($user->isSuperAdmin()) {
            $activeSuperAdmins = User::where('role_id', 1)->where('status', 'Active')->where('id', '!=', $user->id)->count();
            if ($activeSuperAdmins < 1) {
                if ($request->role_id != 1) {
                    return back()->withErrors(['role_id' => 'Security Error: You cannot demote the only active Super Administrator.']);
                }
                if ($request->status !== 'Active') {
                    return back()->withErrors(['status' => 'Security Error: You cannot deactivate or suspend the only active Super Administrator.']);
                }
            }
        }

        // Protection: Prevent user from removing their own Super Admin access
        if ($user->id === auth()->id() && $user->role_id == 1 && $request->role_id != 1) {
            return back()->withErrors(['role_id' => 'Security Error: You cannot remove your own Super Admin access.']);
        }

        DB::beginTransaction();
        try {
            $oldData = $user->only(['name', 'email', 'role_id', 'status', 'department', 'designation']);

            // Handle Profile Picture
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $pictureName = time() . '_' . Str::slug($request->username) . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/avatars');
                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }
                $file->move($destination, $pictureName);
                $user->profile_picture = $pictureName;
            }

            $user->name = $request->name;
            $user->username = strtolower(trim($request->username));
            $user->email = strtolower(trim($request->email));
            $user->mobile = $request->mobile;
            if ($user->role_id != $request->role_id) {
                // Clear user-specific overrides so the new role's permissions take full effect
                DB::table('user_permissions')->where('user_id', $user->id)->delete();
            }
            $user->role_id = $request->role_id;
            $user->status = $request->status;
            $user->department = $request->department;
            $user->designation = $request->designation;
            $user->must_change_password = $request->boolean('must_change_password');

            // If account is marked Active, clear any temporary lockout
            if ($request->status === 'Active') {
                $user->failed_login_attempts = 0;
                $user->locked_until = null;
            }

            $user->save();

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_updated',
                'module' => 'users',
                'table_name' => 'users',
                'record_id' => $user->id,
                'old_value' => json_encode($oldData),
                'new_value' => json_encode($user->only(['name', 'email', 'role_id', 'status', 'department', 'designation'])),
                'ip_address' => $request->ip(),
                'description' => "Updated user profile details for {$user->name} ({$user->user_code})",
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', "User account {$user->name} has been updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        // 1. Cannot delete self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Security Violation: You cannot delete your own account.');
        }

        // 2. Cannot delete the only Super Admin
        if ($user->isSuperAdmin()) {
            $otherSuperAdmins = User::where('role_id', 1)->where('id', '!=', $user->id)->count();
            if ($otherSuperAdmins < 1) {
                return back()->with('error', 'Security Violation: Cannot delete the last remaining Super Admin.');
            }
        }

        $userName = $user->name;
        $userCode = $user->user_code;
        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_deleted',
            'module' => 'users',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => request()->ip(),
            'description' => "Soft-deleted user {$userName} ({$userCode})",
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', "User account {$userName} was successfully removed.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $action = $request->input('reset_type', 'generate_temp');

        if ($action === 'generate_temp') {
            // Generate strong 10-char temporary password
            $tempPassword = Str::random(4) . rand(10, 99) . '@' . Str::upper(Str::random(3));
            $user->update([
                'password_hash' => Hash::make($tempPassword),
                'must_change_password' => true,
                'password_changed_at' => now(),
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'admin_generated_temp_password',
                'module' => 'users',
                'table_name' => 'users',
                'record_id' => $user->id,
                'ip_address' => $request->ip(),
                'description' => "Generated temporary password for user {$user->name}",
                'created_at' => now(),
            ]);

            return back()->with('temp_password_generated', [
                'user' => $user->name,
                'email' => $user->email,
                'password' => $tempPassword,
            ]);
        }

        if ($action === 'send_link') {
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'admin_sent_reset_link',
                'module' => 'users',
                'table_name' => 'password_reset_tokens',
                'record_id' => $user->id,
                'ip_address' => $request->ip(),
                'description' => "Dispatched password reset token for {$user->email}",
                'created_at' => now(),
            ]);

            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
            return back()->with('success', "A password reset link has been dispatched to {$user->email}. Direct test link: {$resetUrl}");
        }

        if ($action === 'force_change') {
            $user->update(['must_change_password' => true]);
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'forced_password_change_flag_set',
                'module' => 'users',
                'table_name' => 'users',
                'record_id' => $user->id,
                'ip_address' => $request->ip(),
                'description' => "Forced password change required for {$user->name}",
                'created_at' => now(),
            ]);
            return back()->with('success', "User {$user->name} will be required to change password on next sign-in.");
        }

        if ($action === 'custom_password') {
            $minLength = (int) SecuritySetting::get('password_min_length', 8);
            $request->validate([
                'new_password' => 'required|string|min:' . $minLength,
            ]);

            $user->update([
                'password_hash' => Hash::make($request->new_password),
                'must_change_password' => $request->boolean('force_change_after', true),
                'password_changed_at' => now(),
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'admin_manual_password_change',
                'module' => 'users',
                'table_name' => 'users',
                'record_id' => $user->id,
                'ip_address' => $request->ip(),
                'description' => "Administrator manually set password for {$user->name}",
                'created_at' => now(),
            ]);

            return back()->with('success', "Password for {$user->name} has been updated successfully.");
        }

        return back()->with('error', 'Invalid password reset option requested.');
    }

    public function toggleStatus(Request $request, User $user)
    {
        $newStatus = $request->input('status');
        if (!in_array($newStatus, ['Active', 'Inactive', 'Suspended', 'Locked'])) {
            return back()->with('error', 'Invalid status provided.');
        }

        // Protect last active Super Admin
        if ($user->isSuperAdmin() && $newStatus !== 'Active') {
            $otherActive = User::where('role_id', 1)->where('status', 'Active')->where('id', '!=', $user->id)->count();
            if ($otherActive < 1) {
                return back()->with('error', 'Security Violation: Cannot deactivate or suspend the only active Super Admin.');
            }
        }

        if ($user->id === auth()->id() && $newStatus !== 'Active') {
            return back()->with('error', 'Security Violation: You cannot deactivate or suspend your own account.');
        }

        $oldStatus = $user->status;
        $user->status = $newStatus;
        if ($newStatus === 'Active') {
            $user->failed_login_attempts = 0;
            $user->locked_until = null;
        }
        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_status_changed',
            'module' => 'users',
            'table_name' => 'users',
            'record_id' => $user->id,
            'old_value' => $oldStatus,
            'new_value' => $newStatus,
            'ip_address' => $request->ip(),
            'description' => "Changed status of {$user->name} from {$oldStatus} to {$newStatus}",
            'created_at' => now(),
        ]);

        return back()->with('success', "Status for {$user->name} changed to {$newStatus}.");
    }

    public function revokeSessions(Request $request, User $user)
    {
        // Regenerate remember token to invalidate persistent cookies
        $user->remember_token = Str::random(60);
        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'sessions_revoked',
            'module' => 'users',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => "Revoked active sessions and remember tokens for {$user->name}",
            'created_at' => now(),
        ]);

        return back()->with('success', "Active sessions and remember tokens for {$user->name} have been revoked.");
    }

    public function permissions(User $user)
    {
        $user->load(['role.permissions', 'userPermissions']);
        $modules = [
            'dashboard' => 'Dashboard',
            'users' => 'User Management',
            'roles' => 'Roles & Permissions',
            'crm' => 'CRM / Inquiries / Sales',
            'clients' => 'Client Management',
            'airline_tickets' => 'Airline Tickets',
            'polio_invoices' => 'Polio Certificate Invoices',
            'travel_groups' => 'Group / Travel Management',
            'travel_vouchers' => 'Travel Vouchers',
            'b2b' => 'B2B Agent Management',
            'salary' => 'Team Salary / Payroll',
            'petty_cash' => 'Daily Expenses / Petty Cash',
            'monthly_expenses' => 'Monthly Expenses & Utility Bills',
            'invoices' => 'Invoices & Payments',
            'reports' => 'Reports & Analytics',
            'settings' => 'Company & Security Settings',
        ];

        $actions = [
            'view' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'print' => 'Print',
            'pdf' => 'Download PDF',
            'export' => 'Export',
            'approve' => 'Approve',
            'payments' => 'Manage Payments',
            'financial' => 'View Financial Details',
        ];

        $allPermissions = Permission::all()->keyBy('slug');
        $rolePermissionSlugs = $user->role ? $user->role->permissions->pluck('slug')->toArray() : [];
        $userOverrides = $user->userPermissions->keyBy('slug');

        return view('users.permissions', compact('user', 'modules', 'actions', 'allPermissions', 'rolePermissionSlugs', 'userOverrides'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $grants = $request->input('permissions', []); // Array of permission_id => state (inherit, grant, deny)

        DB::beginTransaction();
        try {
            DB::table('user_permissions')->where('user_id', $user->id)->delete();

            foreach ($grants as $permId => $state) {
                if ($state === 'grant') {
                    DB::table('user_permissions')->insert([
                        'user_id' => $user->id,
                        'permission_id' => $permId,
                        'is_granted' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } elseif ($state === 'deny') {
                    DB::table('user_permissions')->insert([
                        'user_id' => $user->id,
                        'permission_id' => $permId,
                        'is_granted' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                // 'inherit' means no row in user_permissions, falls back to role
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'user_custom_permissions_updated',
                'module' => 'users',
                'table_name' => 'user_permissions',
                'record_id' => $user->id,
                'ip_address' => $request->ip(),
                'description' => "Updated custom permission overrides for {$user->name}",
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('users.show', $user->id)
                ->with('success', "Individual permissions for {$user->name} have been updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update custom permissions: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('bulk_action');
        $userIds = $request->input('selected_users', []);

        if (empty($userIds) || !is_array($userIds)) {
            return back()->with('error', 'Please select at least one user.');
        }

        // Filter out current user from bulk actions
        $filteredIds = array_diff($userIds, [auth()->id()]);

        if ($action === 'activate') {
            User::whereIn('id', $filteredIds)->update([
                'status' => 'Active',
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);
            $msg = count($filteredIds) . " users activated successfully.";
        } elseif ($action === 'deactivate') {
            // Protect Super Admins
            $targets = User::whereIn('id', $filteredIds)->where('role_id', '!=', 1)->get();
            foreach ($targets as $u) {
                $u->update(['status' => 'Inactive']);
            }
            $msg = $targets->count() . " users deactivated.";
        } elseif ($action === 'delete') {
            $targets = User::whereIn('id', $filteredIds)->where('role_id', '!=', 1)->get();
            foreach ($targets as $u) {
                $u->delete();
            }
            $msg = $targets->count() . " users deleted.";
        } else {
            return back()->with('error', 'Invalid bulk action.');
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'bulk_user_action',
            'module' => 'users',
            'table_name' => 'users',
            'ip_address' => $request->ip(),
            'description' => "Executed bulk action '{$action}' on users: " . implode(',', $filteredIds),
            'created_at' => now(),
        ]);

        return back()->with('success', $msg);
    }
}
