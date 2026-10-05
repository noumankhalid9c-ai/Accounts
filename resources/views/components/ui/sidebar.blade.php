<div id="sidebar">
    <div class="sidebar-brand-box" style="padding: 1.25rem 1.15rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
        <a href="{{ route('dashboard') }}" class="sidebar-brand-link" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
            <div class="sidebar-brand-icon-box" style="background: #ffffff; border-radius: 10px; padding: 4px 8px; height: 40px; min-width: 52px; max-width: 80px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25); flex-shrink: 0; overflow: hidden;">
                <img src="{{ asset('MADINA-LOGO-3.png') }}" alt="{{ config('app.name') }}" style="height: 26px; max-height: 26px; width: auto; max-width: 70px; object-fit: contain; display: block;" width="70" height="26">
            </div>
            <div class="sidebar-brand-info" style="min-width: 0;">
                <div class="sidebar-brand-title" style="color: #ffffff !important; font-size: 1rem; font-weight: 800; letter-spacing: -0.01em; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ config('app.name') }}</div>
                <div class="sidebar-brand-subtitle" style="margin-top: 3px;">
                    <span class="sidebar-suite-badge" style="display: inline-block; color: #cbd5e1; background: rgba(99, 102, 241, 0.25); border: 1px solid rgba(99, 102, 241, 0.35); border-radius: 9999px; padding: 1px 8px; font-size: 0.68rem; font-weight: 600; letter-spacing: 0.03em;">Management Suite</span>
                </div>
            </div>
        </a>
    </div>

    <div class="d-flex flex-column py-3 overflow-auto" style="flex: 1;">
        <div class="sidebar-section-heading" style="color: #818cf8 !important; font-size: 0.68rem !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.09em !important; padding: 0.85rem 1.25rem 0.35rem 1.25rem !important;">Management</div>
        <div class="nav flex-column mb-3">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Client Management</span>
            </a>
            <a href="{{ route('travel-groups.index') }}" class="nav-link {{ request()->routeIs('travel-groups.*') || request()->routeIs('travel-vouchers.*') ? 'active' : '' }}">
                <i class="bi bi-airplane-engines-fill"></i>
                <span>Groups / Travel</span>
            </a>
            <a href="{{ route('b2b.dashboard') }}" class="nav-link {{ request()->routeIs('b2b.*') || request()->routeIs('b2b-agents.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase-fill"></i>
                <span>B2B Agent Management</span>
            </a>
            <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i>
                <span>Team / Employees</span>
            </a>
            <a href="{{ route('salary.index') }}" class="nav-link {{ request()->routeIs('salary.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Team Salary</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Old Expenses</span>
            </a>
            <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                <i class="bi bi-database-fill"></i>
                <span>Client Data Record</span>
            </a>
        </div>
        
        <!-- Petty Cash Menu -->
        <div class="sidebar-section-heading" style="color: #818cf8 !important; font-size: 0.68rem !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.09em !important; padding: 0.85rem 1.25rem 0.35rem 1.25rem !important;">Daily Expenses / Petty Cash</div>
        <div class="nav flex-column mb-3">
            <a href="{{ route('petty_cash.dashboard') }}" class="nav-link {{ request()->routeIs('petty_cash.dashboard') ? 'active' : '' }}">
                <i class="bi bi-pie-chart-fill"></i>
                <span>Petty Cash Dashboard</span>
            </a>
            <a href="{{ route('petty_cash.daily') }}" class="nav-link {{ request()->routeIs('petty_cash.daily') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i>
                <span>Daily Cash</span>
            </a>
            <a href="{{ route('petty_cash.reports') }}" class="nav-link {{ request()->routeIs('petty_cash.reports') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                <span>Reports</span>
            </a>
        </div>
        
        <!-- CRM Menu -->
        <div class="sidebar-section-heading" style="color: #818cf8 !important; font-size: 0.68rem !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.09em !important; padding: 0.85rem 1.25rem 0.35rem 1.25rem !important;">CRM / Sales</div>
        <div class="nav flex-column mb-3">
            <a href="{{ route('crm.dashboard') }}" class="nav-link {{ request()->routeIs('crm.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>CRM Dashboard</span>
            </a>
            <a href="{{ route('crm.leads') }}" class="nav-link {{ request()->routeIs('crm.leads*') && !request()->routeIs('crm.pipeline') ? 'active' : '' }}">
                <i class="bi bi-funnel-fill"></i>
                <span>Inquiries / Leads</span>
            </a>
            <a href="{{ route('crm.pipeline') }}" class="nav-link {{ request()->routeIs('crm.pipeline') ? 'active' : '' }}">
                <i class="bi bi-kanban"></i>
                <span>Sales Pipeline</span>
            </a>
        </div>

        <!-- System Menu -->
        @if(Auth::user() && Auth::user()->role_id == 1)
        <div class="sidebar-section-heading" style="color: #818cf8 !important; font-size: 0.68rem !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.09em !important; padding: 0.85rem 1.25rem 0.35rem 1.25rem !important;">Administration</div>
        <div class="nav flex-column mb-3">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i>
                <span>Users</span>
            </a>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-wide-connected"></i>
                <span>Settings</span>
            </a>
        </div>
        @endif
    </div>
</div>
