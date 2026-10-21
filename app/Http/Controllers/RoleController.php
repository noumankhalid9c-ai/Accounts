<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AuditLog;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:roles.view')->only(['index']);
        $this->middleware('permission:roles.create')->only(['create', 'store']);
        $this->middleware('permission:roles.edit')->only(['edit', 'update', 'permissions', 'updatePermissions']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::withCount('users')->orderBy('id')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->name);
        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_system' => false,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_created',
            'module' => 'roles',
            'table_name' => 'roles',
            'record_id' => $role->id,
            'ip_address' => $request->ip(),
            'description' => "Created new custom role: {$role->name}",
            'created_at' => now(),
        ]);

        return redirect()->route('roles.permissions', $role->id)
            ->with('success', "Role '{$role->name}' created. Please configure its permissions matrix below.");
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
        ]);

        // If system role, keep original slug
        $slug = $role->is_system ? $role->slug : Str::slug($request->name);

        $old = $role->toArray();
        $role->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_updated',
            'module' => 'roles',
            'table_name' => 'roles',
            'record_id' => $role->id,
            'old_value' => json_encode($old),
            'new_value' => json_encode($role->toArray()),
            'ip_address' => $request->ip(),
            'description' => "Updated role info for: {$role->name}",
            'created_at' => now(),
        ]);

        return redirect()->route('roles.index')->with('success', "Role '{$role->name}' updated successfully.");
    }

    public function destroy(Role $role)
    {
        // 1. Cannot delete system role
        if ($role->is_system || $role->id === 1) {
            return back()->with('error', "Security Violation: System role '{$role->name}' cannot be deleted.");
        }

        // 2. Cannot delete role if assigned to users
        if ($role->users()->count() > 0) {
            return back()->with('error', "Cannot delete role '{$role->name}' because it has {$role->users()->count()} assigned user(s). Reassign them first.");
        }

        $roleName = $role->name;
        $role->permissions()->detach();
        $role->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_deleted',
            'module' => 'roles',
            'table_name' => 'roles',
            'record_id' => $role->id,
            'ip_address' => request()->ip(),
            'description' => "Deleted role: {$roleName}",
            'created_at' => now(),
        ]);

        return redirect()->route('roles.index')->with('success', "Role '{$roleName}' deleted successfully.");
    }

    public function permissions(Role $role)
    {
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
        $activePermissionIds = $role->permissions()->pluck('permissions.id')->toArray();

        return view('roles.permissions', compact('role', 'modules', 'actions', 'allPermissions', 'activePermissionIds'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        // If Super Admin, always grant everything
        if ($role->id === 1) {
            $allIds = Permission::pluck('id')->toArray();
            $role->permissions()->sync($allIds);
            return back()->with('info', 'Super Admin maintains full unrestricted access to all permissions.');
        }

        $selectedPermissionIds = $request->input('permissions', []);

        $role->permissions()->sync($selectedPermissionIds);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'role_permissions_updated',
            'module' => 'roles',
            'table_name' => 'role_permissions',
            'record_id' => $role->id,
            'ip_address' => $request->ip(),
            'description' => "Updated permissions matrix for role: {$role->name} (" . count($selectedPermissionIds) . " permissions assigned)",
            'created_at' => now(),
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Permissions matrix for '{$role->name}' has been updated successfully.");
    }
}
