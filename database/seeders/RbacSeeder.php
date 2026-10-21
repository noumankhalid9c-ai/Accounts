<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            [
                'id' => 1,
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full access to all system modules, settings, security, and financial operations.',
                'is_system' => true,
            ],
            [
                'id' => 2,
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Manages operational modules, clients, tickets, bookings, and standard users.',
                'is_system' => true,
            ],
            [
                'id' => 3,
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Access assigned operational modules, approvals, view reports and records.',
                'is_system' => true,
            ],
            [
                'id' => 4,
                'name' => 'Sales / CRM User',
                'slug' => 'sales',
                'description' => 'Manage inquiries, clients, leads, bookings, and customer interactions without delete access.',
                'is_system' => true,
            ],
            [
                'id' => 5,
                'name' => 'Accounts / Finance User',
                'slug' => 'accounts',
                'description' => 'Manage payments, expenses, salaries, invoices, and financial reports.',
                'is_system' => true,
            ],
            [
                'id' => 6,
                'name' => 'B2B Agent Manager',
                'slug' => 'b2b-manager',
                'description' => 'Manage B2B agents, commissions, payment records, and related vouchers.',
                'is_system' => true,
            ],
            [
                'id' => 7,
                'name' => 'Staff / Viewer',
                'slug' => 'staff',
                'description' => 'Read-only access to assigned modules. No create, edit, delete, or financial approvals.',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            $existing = DB::table('roles')->where('id', $roleData['id'])->first();
            if ($existing) {
                DB::table('roles')->where('id', $roleData['id'])->update([
                    'name' => $roleData['name'],
                    'slug' => $roleData['slug'],
                    'description' => $roleData['description'],
                    'is_system' => $roleData['is_system'],
                ]);
            } else {
                DB::table('roles')->insert($roleData);
            }
        }

        // 2. Modules & Actions
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

        // Specific relevant actions per module
        $moduleActionMap = [
            'dashboard' => ['view', 'financial', 'export'],
            'users' => ['view', 'create', 'edit', 'delete', 'export'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'crm' => ['view', 'create', 'edit', 'delete', 'export'],
            'clients' => ['view', 'create', 'edit', 'delete', 'print', 'export', 'financial'],
            'airline_tickets' => ['view', 'create', 'edit', 'delete', 'print', 'pdf', 'export', 'approve', 'payments', 'financial'],
            'polio_invoices' => ['view', 'create', 'edit', 'delete', 'print', 'pdf', 'export', 'payments', 'financial'],
            'travel_groups' => ['view', 'create', 'edit', 'delete', 'print', 'pdf', 'export'],
            'travel_vouchers' => ['view', 'create', 'edit', 'delete', 'print', 'pdf'],
            'b2b' => ['view', 'create', 'edit', 'delete', 'print', 'pdf', 'export', 'payments', 'financial'],
            'salary' => ['view', 'create', 'edit', 'delete', 'print', 'pdf', 'export', 'approve', 'payments', 'financial'],
            'petty_cash' => ['view', 'create', 'edit', 'delete', 'print', 'pdf', 'export', 'approve', 'payments', 'financial'],
            'monthly_expenses' => ['view', 'create', 'edit', 'delete', 'print', 'pdf', 'export', 'payments', 'financial'],
            'invoices' => ['view', 'create', 'edit', 'delete', 'print', 'pdf', 'export', 'payments', 'financial'],
            'reports' => ['view', 'print', 'pdf', 'export', 'financial'],
            'settings' => ['view', 'edit'],
        ];

        $permissionIds = [];
        foreach ($moduleActionMap as $mod => $acts) {
            foreach ($acts as $act) {
                $slug = "{$mod}.{$act}";
                $name = "{$actions[$act]} {$modules[$mod]}";
                $desc = "Allows user to {$actions[$act]} records in {$modules[$mod]} module.";

                $perm = DB::table('permissions')->where('slug', $slug)->first();
                if (!$perm) {
                    $id = DB::table('permissions')->insertGetId([
                        'module' => $mod,
                        'action' => $act,
                        'slug' => $slug,
                        'name' => $name,
                        'description' => $desc,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $permissionIds[$slug] = $id;
                } else {
                    $permissionIds[$slug] = $perm->id;
                }
            }
        }

        // 3. Map Default Role Permissions
        // Super Admin gets all permissions
        foreach ($permissionIds as $slug => $pId) {
            DB::table('role_permissions')->updateOrInsert(
                ['role_id' => 1, 'permission_id' => $pId]
            );
        }

        // Admin gets all except deleting super admin / settings security
        foreach ($permissionIds as $slug => $pId) {
            if ($slug !== 'settings.edit') {
                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => 2, 'permission_id' => $pId]
                );
            }
        }

        // Manager role
        $managerPerms = [
            'dashboard.view', 'dashboard.financial',
            'clients.view', 'clients.create', 'clients.edit', 'clients.print', 'clients.export',
            'crm.view', 'crm.create', 'crm.edit', 'crm.export',
            'airline_tickets.view', 'airline_tickets.create', 'airline_tickets.edit', 'airline_tickets.print', 'airline_tickets.pdf', 'airline_tickets.export', 'airline_tickets.approve', 'airline_tickets.payments',
            'polio_invoices.view', 'polio_invoices.create', 'polio_invoices.edit', 'polio_invoices.print', 'polio_invoices.pdf', 'polio_invoices.payments',
            'travel_groups.view', 'travel_groups.create', 'travel_groups.edit', 'travel_groups.print', 'travel_groups.pdf',
            'travel_vouchers.view', 'travel_vouchers.create', 'travel_vouchers.edit', 'travel_vouchers.print', 'travel_vouchers.pdf',
            'b2b.view', 'b2b.create', 'b2b.edit', 'b2b.print', 'b2b.pdf', 'b2b.export', 'b2b.payments',
            'petty_cash.view', 'petty_cash.create', 'petty_cash.edit', 'petty_cash.print', 'petty_cash.pdf', 'petty_cash.export', 'petty_cash.approve', 'petty_cash.payments',
            'monthly_expenses.view', 'monthly_expenses.create', 'monthly_expenses.edit', 'monthly_expenses.print', 'monthly_expenses.pdf', 'monthly_expenses.export',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.print', 'invoices.pdf', 'invoices.export', 'invoices.payments',
            'reports.view', 'reports.print', 'reports.pdf', 'reports.export',
        ];
        foreach ($managerPerms as $slug) {
            if (isset($permissionIds[$slug])) {
                DB::table('role_permissions')->updateOrInsert(['role_id' => 3, 'permission_id' => $permissionIds[$slug]]);
            }
        }

        // Sales / CRM User
        $salesPerms = [
            'dashboard.view',
            'crm.view', 'crm.create', 'crm.edit', 'crm.export',
            'clients.view', 'clients.create', 'clients.edit', 'clients.print',
            'airline_tickets.view', 'airline_tickets.create', 'airline_tickets.edit', 'airline_tickets.print', 'airline_tickets.pdf',
            'travel_groups.view', 'travel_groups.create', 'travel_groups.edit', 'travel_groups.print',
            'travel_vouchers.view', 'travel_vouchers.create', 'travel_vouchers.print',
        ];
        foreach ($salesPerms as $slug) {
            if (isset($permissionIds[$slug])) {
                DB::table('role_permissions')->updateOrInsert(['role_id' => 4, 'permission_id' => $permissionIds[$slug]]);
            }
        }

        // Accounts / Finance User
        $accountsPerms = [
            'dashboard.view', 'dashboard.financial',
            'salary.view', 'salary.create', 'salary.edit', 'salary.print', 'salary.pdf', 'salary.export', 'salary.approve', 'salary.payments', 'salary.financial',
            'petty_cash.view', 'petty_cash.create', 'petty_cash.edit', 'petty_cash.print', 'petty_cash.pdf', 'petty_cash.export', 'petty_cash.payments', 'petty_cash.financial',
            'monthly_expenses.view', 'monthly_expenses.create', 'monthly_expenses.edit', 'monthly_expenses.print', 'monthly_expenses.pdf', 'monthly_expenses.export', 'monthly_expenses.payments', 'monthly_expenses.financial',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.print', 'invoices.pdf', 'invoices.export', 'invoices.payments', 'invoices.financial',
            'airline_tickets.view', 'airline_tickets.payments', 'airline_tickets.financial',
            'polio_invoices.view', 'polio_invoices.payments', 'polio_invoices.financial',
            'b2b.view', 'b2b.payments', 'b2b.financial',
            'reports.view', 'reports.print', 'reports.pdf', 'reports.export', 'reports.financial',
        ];
        foreach ($accountsPerms as $slug) {
            if (isset($permissionIds[$slug])) {
                DB::table('role_permissions')->updateOrInsert(['role_id' => 5, 'permission_id' => $permissionIds[$slug]]);
            }
        }

        // B2B Agent Manager
        $b2bPerms = [
            'dashboard.view',
            'b2b.view', 'b2b.create', 'b2b.edit', 'b2b.print', 'b2b.pdf', 'b2b.export', 'b2b.payments', 'b2b.financial',
            'airline_tickets.view', 'airline_tickets.create', 'airline_tickets.print',
            'travel_groups.view', 'travel_groups.create', 'travel_groups.print',
            'travel_vouchers.view', 'travel_vouchers.create', 'travel_vouchers.print',
        ];
        foreach ($b2bPerms as $slug) {
            if (isset($permissionIds[$slug])) {
                DB::table('role_permissions')->updateOrInsert(['role_id' => 6, 'permission_id' => $permissionIds[$slug]]);
            }
        }

        // Staff / Viewer
        $staffPerms = [
            'dashboard.view',
            'clients.view',
            'crm.view',
            'airline_tickets.view',
            'travel_groups.view',
            'travel_vouchers.view',
        ];
        foreach ($staffPerms as $slug) {
            if (isset($permissionIds[$slug])) {
                DB::table('role_permissions')->updateOrInsert(['role_id' => 7, 'permission_id' => $permissionIds[$slug]]);
            }
        }

        // 4. Default Security Settings
        $securitySettings = [
            ['key' => 'max_login_attempts', 'value' => '5', 'description' => 'Maximum failed login attempts before account is locked'],
            ['key' => 'lockout_minutes', 'value' => '15', 'description' => 'Account lockout duration in minutes'],
            ['key' => 'password_min_length', 'value' => '8', 'description' => 'Minimum password character length'],
            ['key' => 'password_require_uppercase', 'value' => '1', 'description' => 'Require at least one uppercase letter'],
            ['key' => 'password_require_numbers', 'value' => '1', 'description' => 'Require at least one numeric character'],
            ['key' => 'password_require_symbols', 'value' => '0', 'description' => 'Require at least one special character symbol'],
            ['key' => 'session_lifetime_minutes', 'value' => '120', 'description' => 'Session inactivity timeout in minutes'],
            ['key' => 'force_password_change_days', 'value' => '90', 'description' => 'Days before password change recommendation'],
        ];
        foreach ($securitySettings as $s) {
            DB::table('security_settings')->updateOrInsert(
                ['key' => $s['key']],
                ['value' => $s['value'], 'description' => $s['description'], 'updated_at' => now()]
            );
        }

        // 5. Update Existing Admin User
        $admin = DB::table('users')->where('id', 1)->first();
        if ($admin) {
            DB::table('users')->where('id', 1)->update([
                'user_code' => $admin->user_code ?? 'USR-1001',
                'username' => $admin->username ?? 'admin',
                'role_id' => 1,
                'status' => 'Active',
                'department' => $admin->department ?? 'Executive Management',
                'designation' => $admin->designation ?? 'Super Administrator',
                'updated_at' => now(),
            ]);
        }
    }
}
