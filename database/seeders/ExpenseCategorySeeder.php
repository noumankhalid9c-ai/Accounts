<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Utilities
            ['group' => 'Utilities', 'name' => 'Electricity Bill', 'description' => 'Office electricity and power charges'],
            ['group' => 'Utilities', 'name' => 'Gas Bill', 'description' => 'Sui gas / office gas utility'],
            ['group' => 'Utilities', 'name' => 'Telephone Bill', 'description' => 'Landline / PTCL telephone bills'],
            ['group' => 'Utilities', 'name' => 'Mobile Phone Bill', 'description' => 'Official mobile plans and postpaid bills'],
            ['group' => 'Utilities', 'name' => 'Internet Bill', 'description' => 'Broadband, optical fiber, and WiFi services'],
            ['group' => 'Utilities', 'name' => 'Water Bill', 'description' => 'Water supply / WASA utility charges'],

            // Office Expenses
            ['group' => 'Office Expenses', 'name' => 'Office Rent', 'description' => 'Monthly premises rent and lease charges'],
            ['group' => 'Office Expenses', 'name' => 'Office Maintenance', 'description' => 'Building maintenance and common service charges'],
            ['group' => 'Office Expenses', 'name' => 'Cleaning', 'description' => 'Janitorial services, detergents, and sanitization'],
            ['group' => 'Office Expenses', 'name' => 'Office Supplies', 'description' => 'General office consumables and kitchen pantry items'],
            ['group' => 'Office Expenses', 'name' => 'Stationery', 'description' => 'Pens, registers, notepads, envelopes, stamps'],
            ['group' => 'Office Expenses', 'name' => 'Printing and Photocopying', 'description' => 'Toner, paper reams, vouchers, receipt books'],
            ['group' => 'Office Expenses', 'name' => 'Drinking Water', 'description' => 'Mineral water bottles and dispenser refills'],
            ['group' => 'Office Expenses', 'name' => 'Tea and Refreshments', 'description' => 'Tea, coffee, sugar, milk, and guest refreshments'],
            ['group' => 'Office Expenses', 'name' => 'Furniture and Equipment', 'description' => 'Desks, chairs, filing cabinets, hardware upkeep'],
            ['group' => 'Office Expenses', 'name' => 'Office Repairs', 'description' => 'Plumbing, electrical repairs, painting, AC servicing'],

            // Transport and Vehicle
            ['group' => 'Transport and Vehicle', 'name' => 'Petrol / Fuel', 'description' => 'Fuel for company motorcycles and vehicles'],
            ['group' => 'Transport and Vehicle', 'name' => 'Vehicle Maintenance', 'description' => 'Oil changes, tire punctures, mechanical repairs'],
            ['group' => 'Transport and Vehicle', 'name' => 'Parking Charges', 'description' => 'Commercial parking and plaza parking tokens'],
            ['group' => 'Transport and Vehicle', 'name' => 'Tolls', 'description' => 'Motorway M-Tag and highway toll taxes'],
            ['group' => 'Transport and Vehicle', 'name' => 'Taxi / Ride-Hailing', 'description' => 'Uber, Careem, Indrive, and local transport for errands'],
            ['group' => 'Transport and Vehicle', 'name' => 'Office Vehicle Expenses', 'description' => 'Vehicle insurance, token tax, tracker subscription'],

            // Technology
            ['group' => 'Technology', 'name' => 'Software Subscriptions', 'description' => 'SaaS subscriptions, accounting, and productivity apps'],
            ['group' => 'Technology', 'name' => 'Domain Renewal', 'description' => 'Domain registration and annual renewals'],
            ['group' => 'Technology', 'name' => 'Web Hosting', 'description' => 'Web hosting cPanel, Cloud, and mail storage'],
            ['group' => 'Technology', 'name' => 'Server / VPS', 'description' => 'Cloud servers, VPS, AWS, and DigitalOcean instances'],
            ['group' => 'Technology', 'name' => 'Email Hosting', 'description' => 'Google Workspace, Microsoft 365, Zoho Mail'],
            ['group' => 'Technology', 'name' => 'Internet Services', 'description' => 'Dedicated static IPs, VPN, backup connections'],

            // Business Expenses
            ['group' => 'Business Expenses', 'name' => 'Marketing and Advertising', 'description' => 'Social media ads, flyers, banners, promotions'],
            ['group' => 'Business Expenses', 'name' => 'Bank Charges', 'description' => 'Bank service fees, cheque book fees, transaction tax'],
            ['group' => 'Business Expenses', 'name' => 'Courier', 'description' => 'TCS, Leopards, DHL document delivery and shipping'],
            ['group' => 'Business Expenses', 'name' => 'Staff Expenses', 'description' => 'Team welfare, overtime meals, medical assistance'],
            ['group' => 'Business Expenses', 'name' => 'Travel Expenses', 'description' => 'Business trips, hotel stay, tickets for official work'],
            ['group' => 'Business Expenses', 'name' => 'Professional Fees', 'description' => 'Legal advisor, external auditor, IT consultant'],
            ['group' => 'Business Expenses', 'name' => 'Licenses and Renewals', 'description' => 'DTS license, SECP filings, trade registrations'],
            ['group' => 'Business Expenses', 'name' => 'Taxes and Government Fees', 'description' => 'Withholding taxes, municipal fees, trade taxes'],
            ['group' => 'Business Expenses', 'name' => 'Insurance', 'description' => 'Office property and liability insurance'],
            ['group' => 'Business Expenses', 'name' => 'Miscellaneous', 'description' => 'Uncategorized general operational expenses'],
        ];

        foreach ($categories as $cat) {
            $existing = DB::table('expense_categories')->where('name', $cat['name'])->first();
            if ($existing) {
                DB::table('expense_categories')->where('id', $existing->id)->update([
                    'group' => $cat['group'],
                    'description' => $cat['description'] ?? $existing->description,
                    'status' => 'Active',
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('expense_categories')->insert([
                    'name' => $cat['name'],
                    'group' => $cat['group'],
                    'description' => $cat['description'],
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
