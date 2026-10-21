<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

$logoPath = public_path('MADINA-LOGO-3.png');
$logoHtml = '';
if (file_exists($logoPath)) {
    $logoHtml = '<img src="' . $logoPath . '" style="height: 50px; width: auto; margin-bottom: 15px;">';
}

$date = date('F Y');

$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Madina Shareef Travels - Complete System Manual & Feature Guide</title>
<style>
    @page {
        margin: 50px 35px 45px 35px;
    }
    body {
        font-family: Helvetica, Arial, sans-serif;
        color: #1e293b;
        font-size: 9pt;
        line-height: 1.45;
        margin: 0;
        padding: 0;
    }
    
    .page-break {
        page-break-after: always;
    }
    
    /* Cover Page */
    .cover-box {
        text-align: center;
        padding: 40px 20px 20px 20px;
    }
    .cover-tag {
        display: inline-block;
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        border-radius: 15px;
        padding: 4px 14px;
        font-size: 8pt;
        font-weight: bold;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        margin-bottom: 15px;
    }
    .cover-h1 {
        font-size: 22pt;
        font-weight: bold;
        color: #0f172a;
        margin: 10px 0 8px 0;
        line-height: 1.2;
    }
    .cover-sub {
        font-size: 11pt;
        color: #475569;
        margin-bottom: 20px;
    }
    .cover-line {
        width: 60px;
        height: 3px;
        background-color: #2563eb;
        margin: 0 auto 25px auto;
    }
    
    /* Tables */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
    }
    
    .meta-table {
        width: 85%;
        margin: 0 auto;
        border: 1px solid #cbd5e1;
        font-size: 8.5pt;
    }
    .meta-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #e2e8f0;
    }
    .meta-lbl {
        width: 35%;
        font-weight: bold;
        color: #475569;
        background-color: #f8fafc;
    }
    
    .header-bar {
        border-bottom: 2px solid #2563eb;
        padding-bottom: 4px;
        margin-bottom: 15px;
    }
    .header-bar td {
        padding: 0;
    }
    .header-title {
        font-size: 7.5pt;
        font-weight: bold;
        color: #1e3a8a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .header-sub {
        font-size: 7.5pt;
        color: #64748b;
        text-align: right;
    }
    
    .footer-bar {
        border-top: 1px solid #cbd5e1;
        padding-top: 4px;
        margin-top: 15px;
        font-size: 7.5pt;
        color: #64748b;
    }

    /* Section Headings */
    h1 {
        font-size: 15pt;
        font-weight: bold;
        color: #0f172a;
        border-bottom: 1.5px solid #e2e8f0;
        padding-bottom: 4px;
        margin: 0 0 10px 0;
    }
    h2 {
        font-size: 11pt;
        font-weight: bold;
        color: #1e3a8a;
        border-left: 3px solid #2563eb;
        padding-left: 6px;
        margin: 14px 0 6px 0;
    }
    h3 {
        font-size: 9.5pt;
        font-weight: bold;
        color: #334155;
        margin: 10px 0 4px 0;
    }
    p {
        margin: 0 0 8px 0;
        text-align: justify;
    }

    /* Content Data Tables */
    .data-table {
        border: 1px solid #cbd5e1;
        font-size: 8pt;
    }
    .data-table th {
        background-color: #f1f5f9;
        color: #1e293b;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 7.5pt;
        padding: 5px 8px;
        border: 1px solid #cbd5e1;
        text-align: left;
    }
    .data-table td {
        padding: 5px 8px;
        border: 1px solid #e2e8f0;
        vertical-align: top;
    }
    .data-table tr:nth-child(even) td {
        background-color: #f8fafc;
    }

    /* Callout Boxes */
    .box {
        padding: 8px 12px;
        border-radius: 4px;
        margin: 10px 0;
        font-size: 8.5pt;
    }
    .box-info {
        background-color: #eff6ff;
        border-left: 3px solid #2563eb;
        color: #1e40af;
    }
    .box-success {
        background-color: #f0fdf4;
        border-left: 3px solid #16a34a;
        color: #166534;
    }
    .box-warning {
        background-color: #fffbeb;
        border-left: 3px solid #d97706;
        color: #92400e;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 8px;
        font-size: 7pt;
        font-weight: bold;
    }
    .b-green { background-color: #dcfce7; color: #15803d; }
    .b-blue { background-color: #e0f2fe; color: #0369a1; }
    .b-amber { background-color: #fef3c7; color: #b45309; }
    .b-red { background-color: #fee2e2; color: #b91c1c; }

    /* Workflow Number List */
    .step-tbl td {
        padding: 4px 6px;
        vertical-align: top;
    }
    .step-num {
        width: 22px;
        height: 22px;
        background-color: #2563eb;
        color: #ffffff;
        font-weight: bold;
        text-align: center;
        border-radius: 11px;
        font-size: 8pt;
        line-height: 22px;
    }
    .step-hdr {
        font-weight: bold;
        color: #0f172a;
        font-size: 8.5pt;
    }
    .step-txt {
        font-size: 8pt;
        color: #475569;
    }
    
    .toc-item {
        padding: 4px 0;
        border-bottom: 1px dotted #cbd5e1;
    }
</style>
</head>
<body>

    <!-- ==================== PAGE 1: COVER ==================== -->
    <div class="cover-box">
        {$logoHtml}
        <br>
        <div class="cover-tag">Operations & Financial Management Suite</div>
        <div class="cover-h1">Madina Shareef Travels & Tours</div>
        <div class="cover-sub">Comprehensive System User Manual & Complete Feature Reference Guide</div>
        <div class="cover-line"></div>

        <table class="meta-table">
            <tr>
                <td class="meta-lbl">Software Version</td>
                <td>Version 2.0 (Production Release)</td>
            </tr>
            <tr>
                <td class="meta-lbl">Architecture</td>
                <td>Laravel 10 Enterprise Framework / MySQL / Blade Components</td>
            </tr>
            <tr>
                <td class="meta-lbl">Target Audience</td>
                <td>Super Admins, Tour Operators, Ticketing Specialists, Accountants, CRM Staff</td>
            </tr>
            <tr>
                <td class="meta-lbl">Scope of Features</td>
                <td>Groups & Vouchers, Airline Ticketing, User RBAC, CRM, Petty Cash, Expenses</td>
            </tr>
            <tr>
                <td class="meta-lbl">Publication Date</td>
                <td>{$date}</td>
            </tr>
            <tr>
                <td class="meta-lbl">Security Classification</td>
                <td>Confidential &bull; Proprietary Operational Manual</td>
            </tr>
        </table>

        <div style="margin-top: 40px; font-size: 8pt; color: #64748b;">
            &copy; {$date} Madina Shareef Travels (Pvt) Ltd. All Rights Reserved.
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ==================== PAGE 2: TABLE OF CONTENTS ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Table of Contents</td>
        </tr>
    </table>

    <h1>Table of Contents</h1>
    <p>This manual provides an authoritative walkthrough of all application features, user roles, operational workflows, and data security controls.</p>

    <table style="margin-top: 15px;">
        <tr class="toc-item">
            <td style="width: 35px; font-weight: bold; color: #2563eb;">01</td>
            <td style="font-weight: bold;">System Overview & Operational Architecture</td>
            <td style="text-align: right; color: #64748b;">Page 3</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">02</td>
            <td style="font-weight: bold;">Executive Dashboard & Real-Time Financial Intelligence</td>
            <td style="text-align: right; color: #64748b;">Page 4</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">03</td>
            <td style="font-weight: bold;">User Management, Roles & Role-Based Access Control (RBAC)</td>
            <td style="text-align: right; color: #64748b;">Page 5</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">04</td>
            <td style="font-weight: bold;">Travel Groups & Umrah Operations (Hotels, Flights, Mutamers)</td>
            <td style="text-align: right; color: #64748b;">Page 6</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">05</td>
            <td style="font-weight: bold;">Travel Vouchers (Flight Numbers, Remarks Box, QR Verification)</td>
            <td style="text-align: right; color: #64748b;">Page 7</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">06</td>
            <td style="font-weight: bold;">Airline Ticketing Engine & Flight Segment Management</td>
            <td style="text-align: right; color: #64748b;">Page 8</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">07</td>
            <td style="font-weight: bold;">Polio Invoices, Health Compliance & Medical Billing</td>
            <td style="text-align: right; color: #64748b;">Page 9</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">08</td>
            <td style="font-weight: bold;">Client Records & B2B Sub-Agent Commission Portal</td>
            <td style="text-align: right; color: #64748b;">Page 10</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">09</td>
            <td style="font-weight: bold;">CRM Pipeline, Inquiry Tracking & Follow-Up Automation</td>
            <td style="text-align: right; color: #64748b;">Page 11</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">10</td>
            <td style="font-weight: bold;">Petty Cash Daily Register & Day Balancing Workflow</td>
            <td style="text-align: right; color: #64748b;">Page 12</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">11</td>
            <td style="font-weight: bold;">Monthly Overhead Expenses, Utility Bills & Recurring Liabilities</td>
            <td style="text-align: right; color: #64748b;">Page 13</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">12</td>
            <td style="font-weight: bold;">Human Resources, Employee Payroll & Printable Salary Slips</td>
            <td style="text-align: right; color: #64748b;">Page 14</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">13</td>
            <td style="font-weight: bold;">Security Hardening, Password Policies & Audit Logs</td>
            <td style="text-align: right; color: #64748b;">Page 15</td>
        </tr>
        <tr class="toc-item">
            <td style="font-weight: bold; color: #2563eb;">14</td>
            <td style="font-weight: bold;">Master Permissions Matrix (110 Keys Reference)</td>
            <td style="text-align: right; color: #64748b;">Page 16</td>
        </tr>
    </table>

    <div class="box box-info" style="margin-top: 25px;">
        <strong>Quick Guidance:</strong> Each chapter includes practical user steps, form field specifications, action button definitions, and operational best practices.
    </div>

    <div class="page-break"></div>

    <!-- ==================== PAGE 3: SYSTEM ARCHITECTURE ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 01 &bull; System Architecture</td>
        </tr>
    </table>

    <h1>01. System Overview & Architecture</h1>
    
    <h2>1.1 Core Mission</h2>
    <p>
        The <strong>Madina Shareef Travels Management Suite</strong> is a modern ERP application built to orchestrate all facets of travel management—specializing in Umrah and Hajj packages, international airline ticketing, client accounts, petty cash reconciliation, and staff payroll.
    </p>

    <h2>1.2 Role-Based Access Control (RBAC) Structure</h2>
    <p>
        The application strictly segregates duties according to organizational roles:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">System Role</th>
                <th style="width: 30%;">User Profiles</th>
                <th>Permissions & Key Responsibilities</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Super Admin</strong></td>
                <td>Directors & Owners</td>
                <td>Full system sovereignty. Unlimited visibility into revenue, company profits, staff accounts, system settings, and audit trails.</td>
            </tr>
            <tr>
                <td><strong>Branch Manager</strong></td>
                <td>Operations Heads</td>
                <td>Complete operational control across Travel Groups, Airline Tickets, Daily Registers, CRM Pipeline, and Salaries.</td>
            </tr>
            <tr>
                <td><strong>Tour Operator</strong></td>
                <td>Umrah / Group Coordinators</td>
                <td>Manages pilgrim manifests, flight allocations, Makkah/Madinah hotels, transport contractors, and travel voucher printing.</td>
            </tr>
            <tr>
                <td><strong>Ticketing Agent</strong></td>
                <td>IATA Ticketing Specialists</td>
                <td>Direct ticket issuance, PNR record lookup, flight segments, fare calculations, and commercial passenger invoice generation.</td>
            </tr>
            <tr>
                <td><strong>Accountant</strong></td>
                <td>Finance & Cashiers</td>
                <td>Daily cash register entries, day closing balancing, monthly utility bills, recurring overhead expenses, and salary disbursements.</td>
            </tr>
            <tr>
                <td><strong>Sales / CRM Officer</strong></td>
                <td>Client Relationship Execs</td>
                <td>Lead intake, client database updates, quotation delivery, and negotiation tracking through the 5-stage sales pipeline.</td>
            </tr>
        </tbody>
    </table>

    <h2>1.3 Intelligent Operational Landing Redirection</h2>
    <p>
        To prevent security leaks, users without executive financial privileges (e.g. Sales or Ticketing agents lacking <code>dashboard.view</code>) are automatically redirected upon login directly to their designated operational desks (e.g. <code>/crm/pipeline</code> or <code>/airline-tickets</code>) with a friendly notification.
    </p>

    <div class="page-break"></div>

    <!-- ==================== PAGE 4: DASHBOARD ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 02 &bull; Executive Dashboard</td>
        </tr>
    </table>

    <h1>02. Executive Dashboard & Financial Intelligence</h1>

    <h2>2.1 Real-Time Financial Indicators</h2>
    <p>
        The Executive Dashboard calculates key financial metrics dynamically across all revenue and expenditure ledgers in real time:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Financial Metric</th>
                <th style="width: 40%;">Underlying Formula & Data Sources</th>
                <th>Strategic Business Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Total Revenue</strong></td>
                <td>Airline Ticket Total Fares + Polio Invoices + Group Bookings</td>
                <td>Displays gross value invoiced to date across all operational business lines.</td>
            </tr>
            <tr>
                <td><strong>Total Expenses</strong></td>
                <td>Monthly Overhead Expenses + Disbursed Salaries + Petty Cash</td>
                <td>Tracks the company's total operating expenditure and branch office commitments.</td>
            </tr>
            <tr>
                <td><strong>Net Cash Flow</strong></td>
                <td>Total Cash Collected minus Total Settled Expenses</td>
                <td>Reflects retained operational cash liquidity currently on hand in registers and banks.</td>
            </tr>
            <tr>
                <td><strong>Outstanding Balance</strong></td>
                <td>Ticket Pending Balances + Polio Unpaid Amounts + Client Balances</td>
                <td>Alerts accounting to active collection receivables waiting in the recovery pipeline.</td>
            </tr>
        </tbody>
    </table>

    <h2>2.2 Operations Portfolio Cards</h2>
    <p>
        Four interactive stat cards provide immediate operational visibility and one-click navigation:
    </p>
    <ul>
        <li><strong>Airline Tickets:</strong> Total tickets issued in the system. Links directly to <code>/airline-tickets</code>.</li>
        <li><strong>Travel Groups:</strong> Total tour and Umrah delegations registered. Links to <code>/travel-groups</code>.</li>
        <li><strong>Issued Vouchers:</strong> Total official travel vouchers generated. Links to the vouchers section.</li>
        <li><strong>Clients & Mutamers:</strong> Total customer profiles stored in the CRM. Links to <code>/clients</code>.</li>
    </ul>

    <h2>2.3 Flight Schedule Surveillance & Quick Action Center</h2>
    <p>
        The dashboard includes real-time operational widgets to ensure seamless passenger departures:
    </p>
    <ul>
        <li><strong>Departing Today:</strong> Live counter indicating flight segments taking off within the current calendar day.</li>
        <li><strong>Departing Next 7 Days:</strong> Rolling forward schedule for early transport and hotel coordination.</li>
        <li><strong>Quick Actions Toolbar:</strong> One-click rounded buttons to <em>Issue Ticket</em>, <em>Create Travel Group</em>, <em>New Polio Invoice</em>, or <em>Add Monthly Expense</em>.</li>
    </ul>

    <div class="page-break"></div>

    <!-- ==================== PAGE 5: USER MANAGEMENT & RBAC ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 03 &bull; User Management & RBAC</td>
        </tr>
    </table>

    <h1>03. User Management, Roles & RBAC</h1>

    <h2>3.1 User Directory & Lifecycle (`/users`)</h2>
    <p>
        The user management system enables Super Admins to provision staff accounts, manage credentials, and control access permissions.
    </p>

    <table class="step-tbl" style="margin: 10px 0;">
        <tr>
            <td class="step-num">1</td>
            <td>
                <div class="step-hdr">Provision New User Account</div>
                <div class="step-txt">Navigate to <strong>User Management &gt; Add New User</strong>. Input Full Name, Corporate Email Address, Mobile Phone, Department, and assign an appropriate System Role.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">2</td>
            <td>
                <div class="step-hdr">Temporary Credential & Forced Password Reset</div>
                <div class="step-txt">Enter a temporary password. When <em>Force Password Change</em> is checked, the staff member is automatically redirected to a private reset screen on their first login.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">3</td>
            <td>
                <div class="step-hdr">Status & Session Control</div>
                <div class="step-txt">Admins can toggle account status between <strong>Active</strong> and <strong>Inactive</strong>, trigger instant temporary password generation, or execute <strong>Revoke All Sessions</strong>.</div>
            </td>
        </tr>
    </table>

    <h2>3.2 Roles & Granular Permissions Matrix (`/roles`)</h2>
    <p>
        The suite distinguishes between roles (job titles) and permissions (granular capability keys):
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Module Scope</th>
                <th style="width: 35%;">Sample Permission Keys</th>
                <th>Operational Impact</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Dashboard Control</strong></td>
                <td><code>dashboard.view</code><br><code>dashboard.financial</code></td>
                <td>Regulates access to the dashboard interface and protects sensitive revenue/profit totals.</td>
            </tr>
            <tr>
                <td><strong>Groups & Vouchers</strong></td>
                <td><code>travel_groups.create</code><br><code>travel_vouchers.print</code></td>
                <td>Governs who can create tour packages, adjust hotel contracts, or generate printed vouchers.</td>
            </tr>
            <tr>
                <td><strong>Airline Ticketing</strong></td>
                <td><code>airline_tickets.create</code><br><code>airline_tickets.payments</code></td>
                <td>Enables ticket issuance and customer payment recording while restricting unauthorized cancellations.</td>
            </tr>
            <tr>
                <td><strong>Administration</strong></td>
                <td><code>users.create</code>, <code>roles.edit</code></td>
                <td>Restricted to Super Admins to protect core authorization tables.</td>
            </tr>
        </tbody>
    </table>

    <div class="box box-warning">
        <strong>Self-Deletion Safeguard:</strong> The application forbids administrators from deleting their own user account or deactivating the last remaining active Super Admin.
    </div>

    <div class="page-break"></div>

    <!-- ==================== PAGE 6: TRAVEL GROUPS ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 04 &bull; Travel Groups & Umrah</td>
        </tr>
    </table>

    <h1>04. Travel Groups & Umrah Operations</h1>

    <h2>4.1 Overview of Travel Group Management (`/travel-groups`)</h2>
    <p>
        The <strong>Groups / Travel</strong> module serves as the primary operational container for organized tour delegations and Umrah pilgrims. Every group is tracked through an integrated manifest:
    </p>
    <ul>
        <li><strong>Group Header:</strong> Unique Group Code (e.g. <code>UMR-001</code>), Title, Service Type (Umrah, Hajj, Tour, Ziyarat), Departure & Return Dates, and Status.</li>
        <li><strong>Pilgrim Manifest:</strong> Complete list of mutamers, passport numbers, visa details, package rooming types, bed numbers, and payment status.</li>
        <li><strong>Flight Schedules:</strong> Outbound and Return flight legs, airline flight numbers, departure/arrival airports, dates, and times.</li>
        <li><strong>Hotel Bookings:</strong> Accommodations in Makkah and Madinah, check-in/out dates, meal plans, and confirmation numbers.</li>
        <li><strong>Ground Transportation:</strong> Bus, Coaster, or private car assignments, transporter contacts, pickup points, and drop-off locations.</li>
    </ul>

    <h2>4.2 Step-by-Step Group Lifecycle Guide</h2>

    <table class="step-tbl">
        <tr>
            <td class="step-num">1</td>
            <td>
                <div class="step-hdr">Create the Travel Group Header</div>
                <div class="step-txt">Click <strong>+ Add Travel Group</strong>. Provide an identification code (e.g. <code>UMR-2026-OCT</code>), select the Service Type (e.g. Umrah), set departure/return dates, and assign a Group Leader.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">2</td>
            <td>
                <div class="step-hdr">Attach Outbound & Inbound Flights</div>
                <div class="step-txt">On the group page, under <em>Flights</em>, click <strong>+ Add Flight</strong>. Specify Flight Type (Departure or Return), Flight Number (e.g. <code>SV-735</code>), Sector (e.g. <code>LHE - JED</code>), and Date/Time.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">3</td>
            <td>
                <div class="step-hdr">Record Makkah & Madinah Hotels</div>
                <div class="step-txt">Under <em>Hotels</em>, click <strong>+ Add Hotel</strong>. Select City (Makkah or Madinah), Hotel Name, Room Type (Sharing, Quad, Triple, Double), Check-In Date, and Check-Out Date.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">4</td>
            <td>
                <div class="step-hdr">Enroll Mutamers / Clients</div>
                <div class="step-txt">Under <em>Clients</em>, click <strong>+ Add Client</strong>. Enter Passenger Name, Passport Number, Contact Phone, Room Type, Bed Number, and Payment Status (Paid, Partial, Pending).</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Group Status</th>
                <th style="width: 35%;">Operational Meaning</th>
                <th>System Access & Restrictions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge b-amber">Draft</span></td>
                <td>Group is being prepared</td>
                <td>Flights and hotels can be edited freely before generating official vouchers.</td>
            </tr>
            <tr>
                <td><span class="badge b-blue">Confirmed</span></td>
                <td>Visas & tickets confirmed</td>
                <td>Enables official travel voucher generation and pilgrim manifests.</td>
            </tr>
            <tr>
                <td><span class="badge b-green">Travel Completed</span></td>
                <td>Delegation has returned</td>
                <td>Group is archived for historical reporting and accounting audit.</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ==================== PAGE 7: TRAVEL VOUCHERS ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 05 &bull; Travel Vouchers</td>
        </tr>
    </table>

    <h1>05. Travel Vouchers System</h1>

    <h2>5.1 Importance of the Travel Voucher</h2>
    <p>
        The <strong>Travel Voucher</strong> is the official legal document required by civil aviation authorities, the Ministry of Hajj & Umrah, and border control agents. It serves as tangible verification of round-trip flights, hotel bookings, ground transportation, and licensed sponsorship.
    </p>

    <h2>5.2 Flight Numbers & Comments Box Features</h2>
    <p>
        In Version 2.0, two critical features have been integrated into the voucher engine:
    </p>
    <ul>
        <li><strong>Explicit Flight Numbers Engine:</strong> Inbound and Outbound flight numbers (e.g. <code>SV-735</code> and <code>SV-481</code>) are systematically extracted from the group's flight schedule, pre-filled into editable fields during voucher creation, and rendered clearly on the printed document.</li>
        <li><strong>Dedicated Voucher Comments / Remarks Box:</strong> A designated text area allows operators to enter special pilgrim handling instructions (e.g., <em>"Wheelchair assistance required at Jeddah Airport"</em>, <em>"VIP Family - Deluxe Room near Haram"</em>, or transporter emergency contact numbers). This comment box is stamped directly onto the printed voucher.</li>
    </ul>

    <h2>5.3 Step-by-Step Voucher Generation & Verification Flow</h2>

    <table class="step-tbl">
        <tr>
            <td class="step-num">1</td>
            <td>
                <div class="step-hdr">Initiate Voucher Creation</div>
                <div class="step-txt">From the Travel Group detail page (`/travel-groups/{id}`), scroll to <strong>Travel Vouchers</strong> and click <strong>+ Add Voucher</strong>.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">2</td>
            <td>
                <div class="step-hdr">Review Pre-Populated Flights & Hotels</div>
                <div class="step-txt">The voucher engine automatically aggregates all mutamers, hotels, and flight legs from the group. Confirm or adjust Departure/Return Flight Numbers, Sectors, and Timings.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">3</td>
            <td>
                <div class="step-hdr">Input Operational Remarks</div>
                <div class="step-txt">Enter group instructions into the <strong>Voucher Comments / Remarks</strong> box. Click <strong>Generate & Save Voucher</strong>.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">4</td>
            <td>
                <div class="step-hdr">Print & Public QR Verification</div>
                <div class="step-txt">Click <strong>Print Voucher</strong> for a clean letterhead format. Each printed voucher includes an encrypted QR code. Anyone scanning the QR code is redirected to <code>/voucher/verify/{token}</code> for instant verification.</div>
            </td>
        </tr>
    </table>

    <div class="box box-success">
        <strong>Voucher Revision:</strong> If airline schedules change or hotels are upgraded, operators can click <strong>Edit Voucher</strong> on any existing voucher to update flight numbers, dates, or comments without deleting the document.
    </div>

    <div class="page-break"></div>

    <!-- ==================== PAGE 8: AIRLINE TICKETING ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 06 &bull; Airline Ticketing</td>
        </tr>
    </table>

    <h1>06. Airline Ticketing & Flight Segments</h1>

    <h2>6.1 Ticketing Dashboard & Operations Surveillance</h2>
    <p>
        The Airline Ticketing Module (`/airline-tickets`) manages individual seat bookings, corporate tickets, and group tickets across major carriers (PIA, Saudia, Emirates, Fly Jinnah, Airblue, Qatar Airways).
    </p>

    <h2>6.2 Comprehensive Ticket Creation Flow</h2>

    <table class="step-tbl">
        <tr>
            <td class="step-num">1</td>
            <td>
                <div class="step-hdr">Passenger & PNR Information</div>
                <div class="step-txt">Select the Direct Customer or B2B Sub-Agent. Input the global <strong>PNR (Passenger Name Record)</strong>, GDS Booking Reference, Ticket Date, and Ticket Type (One-Way, Round-Trip, or Multi-City).</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">2</td>
            <td>
                <div class="step-hdr">Multi-Segment Flight Builder</div>
                <div class="step-txt">Add flight segments. For each leg, select the Airline, Flight Number, Departure Airport, Arrival Airport, Departure Date & Time, and Arrival Date & Time.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">3</td>
            <td>
                <div class="step-hdr">Passenger Manifest</div>
                <div class="step-txt">Input passenger names, passport numbers, and passenger categories (Adult, Child, Infant). Multiple travelers sharing the same PNR are linked automatically.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">4</td>
            <td>
                <div class="step-hdr">Financial Calculation & Payments</div>
                <div class="step-txt">Input Basic Fare, Taxes, Airline Charges, and Service Charges. The system calculates Net Supplier Cost and Total Selling Price. Record initial cash or bank payments to establish the account balance.</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Action Button</th>
                <th style="width: 30%;">Route / Handler</th>
                <th>Outcome & Deliverable</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>View Details</strong></td>
                <td><code>/airline-tickets/{id}</code></td>
                <td>Displays passenger roster, itinerary route map, payment ledger, and invoice balances.</td>
            </tr>
            <tr>
                <td><strong>Edit Ticket</strong></td>
                <td><code>/airline-tickets/{id}/edit</code></td>
                <td>Allows adjusting status (Confirmed, Reserved, On Hold, Void), PNR locator, and fare totals.</td>
            </tr>
            <tr>
                <td><strong>Commercial Invoice</strong></td>
                <td><code>/airline-tickets/{id}/invoice</code></td>
                <td>Generates an official client invoice with itemized tax breakdown, payment record, and balance due.</td>
            </tr>
            <tr>
                <td><strong>Download PDF</strong></td>
                <td><code>/airline-tickets/{id}/invoice/pdf</code></td>
                <td>Compiles an immediate PDF invoice download for emailing or accounting filing.</td>
            </tr>
            <tr>
                <td><strong>Print Ticket</strong></td>
                <td><code>/airline-tickets/{id}/print</code></td>
                <td>Clean passenger e-ticket layout formatted for paper or PDF ticket handover.</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ==================== PAGE 9: POLIO INVOICES ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 07 &bull; Polio Certificate Invoices</td>
        </tr>
    </table>

    <h1>07. Polio Certificate Invoices & Health Compliance</h1>

    <h2>7.1 Purpose & Regulatory Requirement</h2>
    <p>
        International pilgrimage regulations mandate valid Polio / Meningitis vaccination certificates for travelers departing Pakistan for Saudi Arabia. The Polio Certificate Invoice module (`/polio-invoices`) streamlines certificate billing:
    </p>
    <ul>
        <li><strong>Automated Sequential Numbering:</strong> Generates formatted invoice identifiers (e.g. <code>POLIO-INV-00001</code>).</li>
        <li><strong>Cost & Person Calculations:</strong> Operators input the Number of Persons and Rate per Person; the total amount is auto-calculated.</li>
        <li><strong>Payment Tracking:</strong> Supports partial and split payments, automatically updating payment status between <em>Pending</em>, <em>Partially Paid</em>, and <em>Paid</em>.</li>
        <li><strong>PDF & Print Formats:</strong> Immediate access to printable client invoices with official company verification stamps.</li>
    </ul>

    <h2>7.2 Step-by-Step Polio Invoice Workflow</h2>

    <table class="step-tbl">
        <tr>
            <td class="step-num">1</td>
            <td>
                <div class="step-hdr">Create New Invoice</div>
                <div class="step-txt">Click <strong>+ New Polio Invoice</strong>. Enter Customer Name, Contact Number, and Invoice Date.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">2</td>
            <td>
                <div class="step-hdr">Set Pilgrims & Rate</div>
                <div class="step-txt">Enter the Number of Persons (e.g. <code>4</code>) and Cost Per Person (e.g. <code>PKR 1,500</code>). Total Amount calculates automatically (e.g. <code>PKR 6,000</code>).</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">3</td>
            <td>
                <div class="step-hdr">Record Initial Receipt</div>
                <div class="step-txt">Enter amount paid at counter. If full payment is made, status becomes <span class="badge b-green">Paid</span>. If partial, status becomes <span class="badge b-amber">Partially Paid</span>.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">4</td>
            <td>
                <div class="step-hdr">Print Client Receipt</div>
                <div class="step-txt">Click <strong>Print Invoice</strong> or <strong>Download PDF</strong> to generate the official stamped receipt for the passenger.</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Invoice Status</th>
                <th style="width: 35%;">Condition</th>
                <th>Financial Ledger Effect</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge b-amber">Pending</span></td>
                <td>Total Paid = 0</td>
                <td>Total invoice amount added to Outstanding Receivables on the dashboard.</td>
            </tr>
            <tr>
                <td><span class="badge b-blue">Partially Paid</span></td>
                <td>0 &lt; Total Paid &lt; Total Amount</td>
                <td>Paid amount added to Net Cash Flow; balance remains in Outstanding Receivables.</td>
            </tr>
            <tr>
                <td><span class="badge b-green">Paid</span></td>
                <td>Total Paid &ge; Total Amount</td>
                <td>Fully settled; zero outstanding balance.</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ==================== PAGE 10: CLIENTS & B2B ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 08 &bull; Clients & B2B Partner Portal</td>
        </tr>
    </table>

    <h1>08. Client Records & B2B Partner Portal</h1>

    <h2>8.1 Client Records Management (`/clients`)</h2>
    <p>
        The client directory maintains permanent records for walk-in travelers, corporate groups, and repeat pilgrims:
    </p>
    <ul>
        <li><strong>Personal Identifiers:</strong> Full Name, Father's Name, CNIC, Passport Number, Passport Expiry, and Mobile/WhatsApp.</li>
        <li><strong>Reference & Vendor:</strong> Reference category (Direct, B2B Agent, Walk-In) and assigned account executive.</li>
        <li><strong>Booking History:</strong> Direct association with all historical airline tickets, travel groups, and ledger payments.</li>
    </ul>

    <h2>8.2 B2B Agent Management & Commission Ledger (`/b2b`)</h2>
    <p>
        For partner travel agencies and sub-agents booking through Madina Shareef Travels, the B2B portal tracks:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Feature Component</th>
                <th style="width: 35%;">Configuration / Input</th>
                <th>Operational Impact</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Agency Profile</strong></td>
                <td>Agency Name, Contact Person, City, Credit Limit</td>
                <td>Establishes the trading credit line and authorized booking thresholds.</td>
            </tr>
            <tr>
                <td><strong>Commission Type</strong></td>
                <td>Percentage (e.g. 5%) or Fixed Amount per Seat</td>
                <td>Automatically calculates sub-agent commission on every issued ticket or group package.</td>
            </tr>
            <tr>
                <td><strong>Agent Statement</strong></td>
                <td>Comprehensive date-filtered transaction ledger</td>
                <td>Summarizes total bookings, gross sales, commissions credited, and net payable/receivable balance.</td>
            </tr>
            <tr>
                <td><strong>Payment Slips</strong></td>
                <td>Settlement recording with formal printable slip</td>
                <td>Documents commission payouts or credit clearances for official audit trails.</td>
            </tr>
        </tbody>
    </table>

    <div class="box box-info">
        <strong>Agency Statement Generation:</strong> Navigate to <code>/b2b-agents/{id}/statement</code> to filter bookings by custom date ranges and export a clean statement of accounts for sub-agents.
    </div>

    <div class="page-break"></div>

    <!-- ==================== PAGE 11: CRM ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 09 &bull; CRM & Sales Funnel</td>
        </tr>
    </table>

    <h1>09. CRM, Inquiry Pipeline & Follow-Ups</h1>

    <h2>9.1 The 5-Stage Sales Funnel (`/crm`)</h2>
    <p>
        The CRM module converts inquiries into confirmed bookings through a 5-stage sales funnel:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Funnel Stage</th>
                <th style="width: 35%;">Action Performed</th>
                <th>Next Step & Transition</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. New Inquiry</strong></td>
                <td>Lead captured from walk-in, phone, or WhatsApp</td>
                <td>Assigned to a sales officer for initial contact.</td>
            </tr>
            <tr>
                <td><strong>2. Contacted</strong></td>
                <td>Agent gathers passenger counts, dates, and budget</td>
                <td>Prepares tailored quotation options for hotels and flights.</td>
            </tr>
            <tr>
                <td><strong>3. Quotation Sent</strong></td>
                <td>Formal package proposal delivered to client</td>
                <td>Agent schedules a follow-up reminder call.</td>
            </tr>
            <tr>
                <td><strong>4. Negotiating</strong></td>
                <td>Adjusting package dates, room types, or payment terms</td>
                <td>Closing contract and securing advance deposit.</td>
            </tr>
            <tr>
                <td><strong>5. Won / Converted</strong></td>
                <td>Advance payment received</td>
                <td>Converted directly into an active Booking, Airline Ticket, or Group Client.</td>
            </tr>
        </tbody>
    </table>

    <h2>9.2 Follow-Up Reminders & Activity Logging</h2>
    <p>
        To ensure zero dropped inquiries:
    </p>
    <ul>
        <li><strong>Activity Logging:</strong> Every phone call, WhatsApp conversation, or office visit is logged under the lead's history.</li>
        <li><strong>Follow-Up Due Dates:</strong> Sales agents can set automated follow-up dates and times. Overdue follow-ups appear highlighted in the agent's CRM dashboard.</li>
    </ul>

    <div class="page-break"></div>

    <!-- ==================== PAGE 12: PETTY CASH ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 10 &bull; Petty Cash Daily Register</td>
        </tr>
    </table>

    <h1>10. Financial Control: Petty Cash Daily Register</h1>

    <h2>10.1 Daily Register Workflow (`/petty-cash/daily`)</h2>
    <p>
        Ensures strict cash integrity for daily counter operations:
    </p>

    <div class="box box-info">
        <strong>Daily Cash Balance Formula:</strong><br>
        <code>Closing Cash = Opening Balance + Daily Collections (Cash In) - Daily Petty Expenses (Cash Out)</code>
    </div>

    <h2>10.2 Step-by-Step Daily Balancing Procedure</h2>

    <table class="step-tbl">
        <tr>
            <td class="step-num">1</td>
            <td>
                <div class="step-hdr">Morning Opening Cash</div>
                <div class="step-txt">Cashier opens <code>/petty-cash/daily</code>. The system carries forward the previous day's verified closing balance as the Opening Cash.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">2</td>
            <td>
                <div class="step-hdr">Logging Cash Inflows & Outflows</div>
                <div class="step-txt">Click <strong>+ Add Transaction</strong>. For inflows (ticket collections, advance booking cash), select <em>Cash In</em>. For office expenses (tea, fuel, couriers), select <em>Cash Out</em> and input recipient name and description.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">3</td>
            <td>
                <div class="step-hdr">Print Transaction Voucher</div>
                <div class="step-txt">Each individual transaction generates a printable petty cash voucher with signatures for Paid By and Received By.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">4</td>
            <td>
                <div class="step-hdr">Evening Day Close</div>
                <div class="step-txt">At end of business, the cashier counts physical cash in drawer, verifies match with system balance, and clicks <strong>Close Day</strong> to lock the register.</div>
            </td>
        </tr>
    </table>

    <div class="box box-warning">
        <strong>Security Lock:</strong> Once a day register is marked as <em>Closed</em>, no further transactions can be added to that date without administrator override, preventing retroactive ledger alterations.
    </div>

    <div class="page-break"></div>

    <!-- ==================== PAGE 13: MONTHLY EXPENSES ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 11 &bull; Monthly Overhead Expenses</td>
        </tr>
    </table>

    <h1>11. Monthly Expenses & Utility Bills</h1>

    <h2>11.1 Expense Categories & Cost Centers (`/monthly-expenses`)</h2>
    <p>
        Tracks recurring office liabilities, tenancy contracts, and utility bills:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Cost Center</th>
                <th style="width: 35%;">Typical Line Items</th>
                <th>Payment Cycle</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Office Rent & Facilities</strong></td>
                <td>Main Office Tenancy, Branch Office Rent, Maintenance Charges</td>
                <td>Monthly (1st to 5th of each month)</td>
            </tr>
            <tr>
                <td><strong>Utility Bills</strong></td>
                <td>Electricity (LESCO), Gas (SNGPL), Internet/Fiber (PTCL), Water</td>
                <td>Monthly against bill due dates</td>
            </tr>
            <tr>
                <td><strong>Software & Infrastructure</strong></td>
                <td>GDS Terminals (Sabre, Amadeus), Server Hosting, Domain renewals</td>
                <td>Monthly / Annual Subscriptions</td>
            </tr>
            <tr>
                <td><strong>Legal & Licensing</strong></td>
                <td>DTS Tourism License, IATA Fees, Chamber of Commerce dues</td>
                <td>Annual / Quarterly</td>
            </tr>
        </tbody>
    </table>

    <h2>11.2 Utility Bill Tracker & Overdue Alerts</h2>
    <p>
        The Utility Bills module tracks consumer account numbers, utility providers, and due dates. The dashboard automatically flags upcoming bills (within 7 days) and triggers warning badges for overdue accounts.
    </p>

    <h2>11.3 Recurring Expenses Automation</h2>
    <p>
        Navigate to <code>/monthly-expenses/recurring</code>. Administrators define fixed recurring liabilities once (e.g. <em>Office Rent PKR 60,000 due on 1st</em>). Clicking <strong>Generate Monthly Expenses</strong> automatically creates the expense records for the current billing cycle in one step.
    </p>

    <div class="page-break"></div>

    <!-- ==================== PAGE 14: HR & SALARIES ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 12 &bull; Human Resources & Payroll</td>
        </tr>
    </table>

    <h1>12. Human Resources, Payroll & Salary Slips</h1>

    <h2>12.1 Employee Directory (`/employees`)</h2>
    <p>
        Maintains staff employment profiles:
    </p>
    <ul>
        <li><strong>Personal Details:</strong> Full Name, CNIC, Mobile Phone, Home Address, Emergency Contact.</li>
        <li><strong>Employment Terms:</strong> Designation, Department, Date of Joining, Employment Status (Active / Resigned).</li>
        <li><strong>Compensation Structure:</strong> Basic Monthly Salary, Fixed Allowances, and Authorized Deductions.</li>
    </ul>

    <h2>12.2 Monthly Payroll Generation (`/salary`)</h2>

    <table class="step-tbl">
        <tr>
            <td class="step-num">1</td>
            <td>
                <div class="step-hdr">Generate Payroll Run</div>
                <div class="step-txt">Click <strong>Generate Monthly Salaries</strong>. The system populates salary records for all active employees for the selected month with their pre-configured basic salary.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">2</td>
            <td>
                <div class="step-hdr">Apply Bonuses & Deductions</div>
                <div class="step-txt">Click <strong>Edit</strong> on an employee's salary record to add commissions, performance bonuses, advance deductions, or tax withholdings.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">3</td>
            <td>
                <div class="step-hdr">Disbursement & Settlement</div>
                <div class="step-txt">When salary is transferred via bank or cash, click <strong>Mark Paid</strong>. The payment status changes to <span class="badge b-green">Paid</span> and records the disbursement date.</div>
            </td>
        </tr>
        <tr>
            <td class="step-num">4</td>
            <td>
                <div class="step-hdr">Print Salary Slips</div>
                <div class="step-txt">Click <strong>Salary Slip</strong> to generate the official employee pay slip displaying Gross Pay, Deductions, and Net Disbursed Salary.</div>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- ==================== PAGE 15: SECURITY POLICIES ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 13 &bull; Security Policies</td>
        </tr>
    </table>

    <h1>13. Security Policies & Audit Trails</h1>

    <h2>13.1 Security Hardening Configuration (`/security-settings`)</h2>
    <p>
        Enterprise protection mechanisms designed to prevent unauthorized access and data loss:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30%;">Defense Mechanism</th>
                <th style="width: 35%;">Configuration Setting</th>
                <th>Security Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Password Complexity</strong></td>
                <td>Minimum 8 characters, uppercase, numbers, symbols</td>
                <td>Prevents weak passwords and dictionary attacks.</td>
            </tr>
            <tr>
                <td><strong>Failed Login Lockout</strong></td>
                <td>Configurable (e.g. 5 consecutive failed attempts)</td>
                <td>Locks the account automatically and records IP to Login History.</td>
            </tr>
            <tr>
                <td><strong>Session Idle Timeout</strong></td>
                <td>15, 30, or 60 Minutes of Inactivity</td>
                <td>Auto-terminates abandoned browser sessions on office workstations.</td>
            </tr>
            <tr>
                <td><strong>Emergency Revocation</strong></td>
                <td>Instant "Revoke Other Sessions" button</td>
                <td>Invalidates all active login tokens across all devices immediately.</td>
            </tr>
        </tbody>
    </table>

    <h2>13.2 Activity Logs & Login History (`/activity-logs`, `/login-history`)</h2>
    <p>
        The security engine maintains permanent, immutable records of all system interactions:
    </p>
    <ul>
        <li><strong>Audit Trail:</strong> Records User ID, Model Name, Action (Created, Updated, Deleted), and Timestamp.</li>
        <li><strong>Login History:</strong> Captures IP Address, Browser User-Agent, Date/Time, and Status (<span class="badge b-green">Success</span>, <span class="badge b-red">Failed</span>, or <span class="badge b-amber">Locked</span>).</li>
    </ul>

    <div class="page-break"></div>

    <!-- ==================== PAGE 16: MASTER PERMISSIONS ==================== -->
    <table class="header-bar">
        <tr>
            <td class="header-title">Madina Shareef Travels &bull; Operations Suite</td>
            <td class="header-sub">Chapter 14 &bull; Master Permissions Matrix</td>
        </tr>
    </table>

    <h1>14. Master Permissions Reference Matrix</h1>
    <p>Full operational mapping of all 110 system permission keys across all 12 modules:</p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Module Name</th>
                <th style="width: 45%;">Permission Key Identifier</th>
                <th>Functional Access Scope</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Dashboard</strong></td>
                <td><code>dashboard.view</code><br><code>dashboard.financial</code></td>
                <td>Dashboard access<br>Visibility into executive profits and cash totals</td>
            </tr>
            <tr>
                <td><strong>User Management</strong></td>
                <td><code>users.view</code>, <code>users.create</code><br><code>users.edit</code>, <code>users.delete</code><br><code>users.reset_password</code>, <code>users.toggle_status</code></td>
                <td>Inspect user directory<br>Create & edit staff profiles<br>Reset credentials & activate/suspend accounts</td>
            </tr>
            <tr>
                <td><strong>Roles & Permissions</strong></td>
                <td><code>roles.view</code>, <code>roles.create</code><br><code>roles.edit</code>, <code>roles.delete</code></td>
                <td>View role definitions<br>Assign & configure granular permission matrix</td>
            </tr>
            <tr>
                <td><strong>Travel Groups</strong></td>
                <td><code>travel_groups.view</code>, <code>travel_groups.create</code><br><code>travel_groups.edit</code>, <code>travel_groups.delete</code></td>
                <td>View Umrah & Tour delegations<br>Create, edit schedules, manage hotel allocations</td>
            </tr>
            <tr>
                <td><strong>Travel Vouchers</strong></td>
                <td><code>travel_vouchers.view</code>, <code>travel_vouchers.create</code><br><code>travel_vouchers.edit</code>, <code>travel_vouchers.print</code></td>
                <td>View voucher list & QR codes<br>Generate vouchers with flight numbers & comments<br>Print official pilgrim travel vouchers</td>
            </tr>
            <tr>
                <td><strong>Airline Ticketing</strong></td>
                <td><code>airline_tickets.view</code>, <code>airline_tickets.create</code><br><code>airline_tickets.edit</code>, <code>airline_tickets.delete</code><br><code>airline_tickets.payments</code></td>
                <td>Ticketing dashboard & inventory<br>Issue and modify PNRs and flight segments<br>Record customer payment installments</td>
            </tr>
            <tr>
                <td><strong>Polio Invoices</strong></td>
                <td><code>polio_invoices.view</code>, <code>polio_invoices.create</code><br><code>polio_invoices.edit</code>, <code>polio_invoices.delete</code></td>
                <td>Health certification billing<br>Record pilgrim medical payments & print receipts</td>
            </tr>
            <tr>
                <td><strong>Clients & B2B</strong></td>
                <td><code>clients.view</code>, <code>clients.create</code><br><code>b2b.view</code>, <code>b2b.payments</code></td>
                <td>Client database access<br>B2B sub-agent ledger & commission payout</td>
            </tr>
            <tr>
                <td><strong>Petty Cash</strong></td>
                <td><code>petty_cash.view</code>, <code>petty_cash.create</code></td>
                <td>Daily register inflows/outflows & day closing lock</td>
            </tr>
            <tr>
                <td><strong>Monthly Expenses</strong></td>
                <td><code>monthly_expenses.view</code>, <code>monthly_expenses.create</code></td>
                <td>Overhead utility bills, recurring liabilities, reports</td>
            </tr>
            <tr>
                <td><strong>Employees & Payroll</strong></td>
                <td><code>salary.view</code>, <code>salary.create</code></td>
                <td>Team payroll generation, pay slips, salary settlement</td>
            </tr>
            <tr>
                <td><strong>CRM Funnel</strong></td>
                <td><code>crm.view</code>, <code>crm.create</code>, <code>crm.pipeline</code></td>
                <td>Inquiry capture, lead tracking, sales funnel stages</td>
            </tr>
        </tbody>
    </table>

    <table class="footer-bar" style="margin-top: 30px;">
        <tr>
            <td>Madina Shareef Travels (Pvt) Ltd. &bull; Operations & Financial Suite v2.0</td>
            <td style="text-align: right;">End of Document &bull; {$date}</td>
        </tr>
    </table>

</body>
</html>
HTML;

echo "Compiling clean, table-based HTML into DomPDF...\n";
$pdf = Pdf::loadHTML($html);
$pdf->setPaper('a4', 'portrait');

$output = $pdf->output();

// Verify page count in output
$pageCount = preg_match_all("/\/Page\W/", $output, $matches);
echo "Resulting PDF Page Count: $pageCount pages!\n";

// Save to public web path
$publicDest = public_path('Madina_Shareef_Travels_System_Documentation_and_User_Manual.pdf');
file_put_contents($publicDest, $output);
echo "Saved to public web path: $publicDest (" . strlen($output) . " bytes)\n";

// Save to workspace root
$rootDest = base_path('../Madina_Shareef_Travels_System_Documentation_and_User_Manual.pdf');
file_put_contents($rootDest, $output);
echo "Saved to workspace root: $rootDest\n";

// Save to artifact directory
$artifactDir = 'C:\Users\Cloud\.gemini\antigravity-ide\brain\a7b09e1c-1204-4cfb-b19e-2144417f48e2';
if (is_dir($artifactDir)) {
    $artDest = $artifactDir . DIRECTORY_SEPARATOR . 'Madina_Shareef_Travels_System_Documentation_and_User_Manual.pdf';
    file_put_contents($artDest, $output);
    echo "Saved to artifact path: $artDest\n";
}

echo "SUCCESS!\n";
