<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CleanDeliverySeeder extends Seeder
{
    /**
     * Seed the application's database with 1 clean, realistic, professional entry per module.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $tablesToTruncate = [
            'crm_lead_comments',
            'crm_activities',
            'crm_followups',
            'crm_leads',
            'customers',
            'group_flights',
            'group_hotels',
            'group_transports',
            'group_clients',
            'travel_vouchers',
            'travel_groups',
            'booking_payments',
            'bookings',
            'b2b_agents',
            'ticket_passengers',
            'ticket_flight_segments',
            'ticket_invoices',
            'ticket_payments',
            'airline_tickets',
            'polio_certificate_invoices',
            'salaries',
            'employees',
            'monthly_expenses',
            'petty_cash_transactions',
            'petty_cash_days',
            'clients',
        ];

        foreach ($tablesToTruncate as $tbl) {
            try {
                DB::table($tbl)->truncate();
            } catch (\Throwable $e) {
                // Skip if table does not exist
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // 1. USERS & ROLES
        $admin = DB::table('users')->where('email', 'admin@example.com')->first();
        if (!$admin) {
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Super Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $adminId = $admin->id;
            DB::table('users')->where('id', $adminId)->update(['role_id' => 1]);
        }

        $salesStaff = DB::table('users')->where('email', 'nouman@madinashareef.com')->first();
        if (!$salesStaff) {
            $salesStaffId = DB::table('users')->insertGetId([
                'name' => 'Nouman Khalid',
                'email' => 'nouman@madinashareef.com',
                'password' => Hash::make('password123'),
                'role_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $salesStaffId = $salesStaff->id;
            DB::table('users')->where('id', $salesStaffId)->update(['role_id' => 4]);
        }

        // 2. CLIENT MANAGEMENT
        $clientId = DB::table('clients')->insertGetId([
            'company_name' => 'Al-Madina Trading Co.',
            'name' => 'Haji Muhammad Rizwan',
            'contact_person' => 'Haji Muhammad Rizwan',
            'phone' => '+92 300 1234567',
            'mobile' => '+92 300 1234567',
            'cnic' => '35201-1234567-1',
            'email' => 'rizwan@almadinatrading.com',
            'address' => 'Suite 402, Business Executive Tower, Main Boulevard',
            'city' => 'Lahore',
            'country' => 'Pakistan',
            'reference_type' => 'Direct Client',
            'notes' => 'VIP Corporate client for Umrah and executive group travel',
            'status' => 'active',
            'assigned_employee_id' => $salesStaffId,
            'created_at' => Carbon::now(),
        ]);

        // 3. B2B AGENT MANAGEMENT
        $b2bAgentId = DB::table('b2b_agents')->insertGetId([
            'name' => 'Al-Haram Tours & Travels',
            'company_name' => 'Al-Haram International Travel Agency',
            'contact_person' => 'Sheikh Tariq Mehmood',
            'contact' => '+92 321 9876543',
            'whatsapp' => '+92 321 9876543',
            'email' => 'tariq@alharamtours.pk',
            'address' => 'Shop 12, Gulberg Galleria, Gulberg III',
            'city' => 'Lahore',
            'country' => 'Pakistan',
            'notes' => 'Key B2B agency partner in Punjab region',
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 4. CLIENT DATA RECORD (BOOKING & PAYMENTS)
        $bookingId = DB::table('bookings')->insertGetId([
            'client_id' => $clientId,
            'b2b_agent_id' => $b2bAgentId,
            'assigned_user_id' => $salesStaffId,
            'service_taken' => 'Umrah VIP Package',
            'package_details' => '15-Day Premium Ramadan Umrah Package (5 Star Makkah & Madina)',
            'booking_date' => Carbon::now()->format('Y-m-d'),
            'travel_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'total_amount' => 450000.00,
            'advance_payment' => 200000.00,
            'commission_type' => 'Percentage',
            'commission_percentage' => 10.00,
            'b2b_commission' => 25000.00,
            'company_share' => 425000.00,
            'payment_status' => 'Partial',
            'b2b_commission_status' => 'Pending',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('booking_payments')->insert([
            'booking_id' => $bookingId,
            'amount' => 200000.00,
            'payment_date' => Carbon::now()->format('Y-m-d'),
            'payment_method' => 'Bank Transfer',
            'notes' => 'Initial booking advance received via Meezan Bank (FT-MEZ-20261103-9988)',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 5. GROUP / TRAVEL MODULE
        $groupId = DB::table('travel_groups')->insertGetId([
            'group_id' => 'GRP-2026-RAMADAN-01',
            'group_name' => '15-Day Executive Ramadan Umrah Group',
            'service_type' => 'Umrah',
            'destination' => 'Saudi Arabia (Makkah & Madinah)',
            'departure_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'return_date' => Carbon::now()->addDays(35)->format('Y-m-d'),
            'group_leader' => 'Maulana Hafiz Bilal',
            'assigned_team_member_id' => $salesStaffId,
            'status' => 'confirmed',
            'notes' => 'Executive 50-passenger VIP group with direct flights and 5-star hotels facing Haram.',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('group_flights')->insert([
            'travel_group_id' => $groupId,
            'flight_type' => 'departure',
            'flight_number' => 'SV-735',
            'sector' => 'LHE - JED',
            'departure_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'departure_time' => '14:30:00',
            'arrival_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'arrival_time' => '18:45:00',
            'notes' => 'Saudia direct flight LHE to JED',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('group_hotels')->insert([
            'travel_group_id' => $groupId,
            'city' => 'Makkah',
            'hotel_name' => 'Swissotel Al Maqam Makkah',
            'view' => 'Haram View',
            'meal' => 'Full Board',
            'confirmation_number' => 'HTL-MAK-78611',
            'room_type' => 'Quad',
            'check_in' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'check_out' => Carbon::now()->addDays(28)->format('Y-m-d'),
            'nights' => 8,
            'notes' => 'Directly connected to Clock Tower complex',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('group_transports')->insert([
            'travel_group_id' => $groupId,
            'travel_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'transporter' => 'SAPTCO VIP Fleet',
            'type' => 'Luxury Bus (50 Seater)',
            'description' => 'Airport to Makkah Hotel Transfer',
            'pickup_location' => 'Jeddah Hajj Terminal',
            'drop_location' => 'Swissotel Al Maqam Reception',
            'notes' => 'Luggage truck escort included',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('group_clients')->insert([
            'travel_group_id' => $groupId,
            'client_name' => 'Haji Muhammad Rizwan',
            'father_name' => 'Muhammad Bashir',
            'gender' => 'Male',
            'passport_number' => 'PA9876543',
            'passport_expiry' => Carbon::now()->addYears(5)->format('Y-m-d'),
            'phone' => '+92 300 1234567',
            'package' => 'Executive Ramadan VIP',
            'pax_type' => 'adult',
            'room_type' => 'Quad',
            'bed' => 'Single',
            'group_number' => 'GRP-01',
            'visa_number' => 'VSA-998822',
            'pnr' => 'SV-KSA901',
            'payment_status' => 'partial',
            'booking_status' => 'confirmed',
            'notes' => 'Head of family, 4 pax total',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('travel_vouchers')->insert([
            'travel_group_id' => $groupId,
            'voucher_number' => 'VCH-2026-0001',
            'voucher_type' => 'umrah',
            'voucher_date' => Carbon::now()->format('Y-m-d'),
            'status' => 'active',
            'group_head' => 'Haji Muhammad Rizwan',
            'package_number' => 'PKG-EXEC-RAMADAN',
            'pax' => 4,
            'whatsapp' => '+92 300 1234567',
            'special_instructions' => 'Wheelchair assistance requested for elderly pax upon arrival at Jeddah Airport.',
            'comments' => 'VIP Meet & Greet confirmed with ground handling supervisor.',
            'qr_token' => 'VCH-TOKEN-' . bin2hex(random_bytes(8)),
            'created_by' => $adminId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 6. AIRLINE TICKET MODULE
        $ticketId = DB::table('airline_tickets')->insertGetId([
            'ticket_number' => '214-9876543210',
            'customer_id' => null,
            'b2b_agent_id' => $b2bAgentId,
            'pnr' => 'PK-DXB786',
            'booking_reference' => 'REF-TKT-2026-001',
            'ticket_date' => Carbon::now()->format('Y-m-d'),
            'ticket_type' => 'return',
            'route_type' => 'international',
            'base_fare' => 125000.00,
            'taxes' => 25000.00,
            'total_fare' => 150000.00,
            'amount_paid' => 150000.00,
            'amount_pending' => 0.00,
            'payment_status' => 'paid',
            'ticket_status' => 'issued',
            'qr_token' => 'TKT-TOKEN-' . bin2hex(random_bytes(8)),
            'created_by' => $adminId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('ticket_passengers')->insert([
            'ticket_id' => $ticketId,
            'customer_id' => null,
            'passenger_name' => 'MR. MUHAMMAD AHSAN',
            'passenger_type' => 'adult',
            'title' => 'Mr',
            'gender' => 'male',
            'date_of_birth' => '1988-06-15',
            'passport_number' => 'PA9876543',
            'passport_expiry' => Carbon::now()->addYears(4)->format('Y-m-d'),
            'nationality' => 'Pakistani',
            'ticket_number' => '214-9876543210',
            'seat' => '14A',
            'baggage' => '40 KG',
            'meal' => 'Standard Halal Meal',
            'special_request' => 'Aisle preference if changed',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('ticket_flight_segments')->insert([
            'ticket_id' => $ticketId,
            'airline_id' => 11, // PIA
            'flight_number' => 'PK-203',
            'departure_airport_id' => 11, // LHE
            'arrival_airport_id' => 1, // DXB
            'departure_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'departure_time' => '10:00:00',
            'arrival_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'arrival_time' => '12:45:00',
            'terminal' => 'T2',
            'cabin_class' => 'Economy',
            'baggage_allowance' => '40 KG',
            'meal' => 'Hot Breakfast',
            'seat' => '14A',
            'flight_status' => 'confirmed',
            'segment_order' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('ticket_payments')->insert([
            'ticket_id' => $ticketId,
            'payment_date' => Carbon::now()->format('Y-m-d'),
            'amount' => 150000.00,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'IBFT-HBL-55442211',
            'received_by' => $adminId,
            'notes' => 'Full payment cleared for Dubai return ticket',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('ticket_invoices')->insert([
            'ticket_id' => $ticketId,
            'invoice_number' => 'TKT-INV-2026-0001',
            'invoice_date' => Carbon::now()->format('Y-m-d'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 7. TEAM / EMPLOYEES & SALARY
        $empId = DB::table('employees')->insertGetId([
            'employee_id' => 'EMP-1001',
            'name' => 'Nouman Khalid',
            'designation' => 'Senior Travel & Umrah Consultant',
            'joining_date' => '2024-01-15',
            'basic_salary' => 75000.00,
            'contact' => '+92 300 9988776',
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('salaries')->insert([
            'employee_id' => $empId,
            'salary_month' => Carbon::now()->month,
            'salary_year' => Carbon::now()->year,
            'basic_salary' => 75000.00,
            'sales_commission' => 10000.00,
            'incentive' => 2500.00,
            'advance_salary' => 0.00,
            'deduction' => 5000.00,
            'net_salary' => 82500.00,
            'status' => 'Paid',
            'payment_date' => Carbon::now()->format('Y-m-d'),
            'notes' => 'Monthly executive salary with sales performance commission',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 8. MONTHLY EXPENSES
        $expenseCategory = DB::table('expense_categories')->first();
        $catId = $expenseCategory ? $expenseCategory->id : 1;

        DB::table('monthly_expenses')->insert([
            'expense_code' => 'EXP-2026-11-001',
            'expense_title' => 'Main Office Commercial Rent',
            'category_id' => $catId,
            'expense_month' => Carbon::now()->month,
            'expense_year' => Carbon::now()->year,
            'expense_date' => Carbon::now()->format('Y-m-d'),
            'amount' => 95000.00,
            'paid_amount' => 95000.00,
            'provider_name' => 'Gulberg Commercial Real Estate Corp',
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
            'payment_date' => Carbon::now()->format('Y-m-d'),
            'notes' => 'Main office monthly commercial lease payment',
            'created_by' => $adminId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 9. PETTY CASH & DAILY CASH
        $pettyDayId = DB::table('petty_cash_days')->insertGetId([
            'date' => Carbon::now()->format('Y-m-d'),
            'opening_cash' => 25000.00,
            'cash_received' => 5000.00,
            'cash_expenses' => 1500.00,
            'closing_cash' => 28500.00,
            'actual_cash' => 28500.00,
            'cash_difference' => 0.00,
            'status' => 'open',
            'notes' => 'Daily counter cash register',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('petty_cash_transactions')->insert([
            'petty_cash_day_id' => $pettyDayId,
            'transaction_type' => 'expense',
            'date' => Carbon::now()->format('Y-m-d'),
            'category_id' => $catId,
            'description' => 'Guest hospitality refreshments & office printing supplies',
            'paid_to' => 'Al-Makkah General Store',
            'amount' => 1500.00,
            'payment_method' => 'cash',
            'reference_number' => 'RCPT-8821',
            'created_by' => $adminId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 10. POLIO CERTIFICATE INVOICES
        DB::table('polio_certificate_invoices')->insert([
            'invoice_number' => 'POLIO-2026-0001',
            'invoice_date' => Carbon::now()->format('Y-m-d'),
            'client_name' => 'Haji Muhammad Rizwan & Family',
            'phone' => '+92 300 1234567',
            'passport_number' => 'PA9876543, PA9876544, PA9876545, PA9876546',
            'persons' => 4,
            'cost_per_person' => 1250.00,
            'total_amount' => 5000.00,
            'amount_paid' => 5000.00,
            'balance_due' => 0.00,
            'payment_status' => 'paid',
            'payment_method' => 'Cash',
            'notes' => 'Polio vaccination and verification cards for 4 Umrah travelers',
            'created_by' => $adminId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 11. CRM & INQUIRIES / LEADS
        $customerId = DB::table('customers')->insertGetId([
            'customer_code' => 'CUST-2026-0001',
            'name' => 'Dr. Salman Farooq',
            'father_name' => 'Muhammad Farooq',
            'gender' => 'male',
            'dob' => '1980-04-12',
            'cnic' => '37405-1122334-1',
            'passport_number' => 'PK8822331',
            'passport_expiry' => Carbon::now()->addYears(6)->format('Y-m-d'),
            'phone' => '+92 333 4455667',
            'whatsapp' => '+92 333 4455667',
            'email' => 'dr.salman@gmail.com',
            'address' => 'House 45, Sector F-8/2',
            'city' => 'Islamabad',
            'country' => 'Pakistan',
            'preferred_service' => 'Umrah',
            'preferred_destination' => 'Makkah & Madinah',
            'travel_type' => 'family',
            'notes' => 'VIP client inquiring for premium family package',
            'assigned_to' => $salesStaffId,
            'created_by' => $adminId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $leadId = DB::table('crm_leads')->insertGetId([
            'lead_code' => 'INQ-2026-0001',
            'customer_id' => $customerId,
            'customer_name' => 'Dr. Salman Farooq',
            'phone' => '+92 333 4455667',
            'whatsapp' => '+92 333 4455667',
            'email' => 'dr.salman@gmail.com',
            'inquiry_type' => 'Umrah Package',
            'service' => 'VIP 5-Star Umrah Package',
            'destination' => 'Makkah & Madinah',
            'travel_date' => Carbon::now()->addDays(25)->format('Y-m-d'),
            'pax' => 2,
            'budget' => 600000.00,
            'expected_value' => 600000.00,
            'source' => 'WhatsApp',
            'priority' => 'high',
            'stage' => 'Quotation Sent',
            'status' => 'active',
            'assigned_to' => $salesStaffId,
            'created_by' => $adminId,
            'notes' => 'Client requested direct Haram view room in Makkah',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('crm_followups')->insert([
            'lead_id' => $leadId,
            'customer_id' => $customerId,
            'followup_date' => Carbon::now()->addDay()->format('Y-m-d'),
            'followup_time' => '11:00:00',
            'type' => 'call',
            'notes' => 'Call Dr. Salman to discuss customized 5-star hotel options in Makkah clock tower.',
            'result' => 'pending',
            'next_followup_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'assigned_to' => $salesStaffId,
            'created_by' => $adminId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('crm_activities')->insert([
            'customer_id' => $customerId,
            'lead_id' => $leadId,
            'user_id' => $salesStaffId,
            'activity_type' => 'quotation_sent',
            'description' => 'Shared official proposal for 2 Pax Executive Umrah via WhatsApp and Email.',
            'old_value' => 'New Lead',
            'new_value' => 'Quotation Sent',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('crm_lead_comments')->insert([
            'lead_id' => $leadId,
            'user_id' => $salesStaffId,
            'comment' => 'Customer is highly interested in clock tower Swissotel or Fairmont. Followup scheduled for tomorrow.',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
