<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\AirlineTicket;
use App\Models\TicketFlightSegment;
use App\Models\TicketPassenger;
use App\Models\Customer;
use App\Models\B2bAgent;
use App\Models\User;
use Illuminate\Support\Str;

class AirlineTicketsModuleSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Airlines
        $airlines = [
            ['name' => 'Emirates', 'code' => 'EK'],
            ['name' => 'Qatar Airways', 'code' => 'QR'],
            ['name' => 'Turkish Airlines', 'code' => 'TK'],
            ['name' => 'Saudi Airlines', 'code' => 'SV'],
            ['name' => 'Etihad Airways', 'code' => 'EY'],
            ['name' => 'British Airways', 'code' => 'BA'],
            ['name' => 'Air France', 'code' => 'AF'],
            ['name' => 'Lufthansa', 'code' => 'LH'],
            ['name' => 'FlyDubai', 'code' => 'FZ'],
            ['name' => 'Air Arabia', 'code' => 'G9'],
        ];

        foreach ($airlines as $airline) {
            Airline::firstOrCreate(['code' => $airline['code']], [
                'name' => $airline['name'],
                'status' => true
            ]);
        }

        // 2. Create Airports
        $airports = [
            ['city' => 'Dubai', 'iata_code' => 'DXB', 'country' => 'UAE'],
            ['city' => 'Doha', 'iata_code' => 'DOH', 'country' => 'Qatar'],
            ['city' => 'Istanbul', 'iata_code' => 'IST', 'country' => 'Turkey'],
            ['city' => 'Jeddah', 'iata_code' => 'JED', 'country' => 'Saudi Arabia'],
            ['city' => 'Riyadh', 'iata_code' => 'RUH', 'country' => 'Saudi Arabia'],
            ['city' => 'London', 'iata_code' => 'LHR', 'country' => 'UK'],
            ['city' => 'Paris', 'iata_code' => 'CDG', 'country' => 'France'],
            ['city' => 'Frankfurt', 'iata_code' => 'FRA', 'country' => 'Germany'],
            ['city' => 'New York', 'iata_code' => 'JFK', 'country' => 'USA'],
            ['city' => 'Cairo', 'iata_code' => 'CAI', 'country' => 'Egypt'],
        ];

        foreach ($airports as $airport) {
            Airport::firstOrCreate(['iata_code' => $airport['iata_code']], [
                'name' => $airport['city'] . ' International Airport',
                'city' => $airport['city'],
                'country' => $airport['country'],
                'status' => true
            ]);
        }
        
        // 3. Ensure we have users and customers
        $user = User::first() ?? User::factory()->create();
        
        // Ensure customers exist
        $customers = Customer::limit(5)->get();
        if ($customers->isEmpty()) {
            Customer::create(['name' => 'John Doe', 'phone' => '123456789', 'email' => 'john@example.com']);
            Customer::create(['name' => 'Jane Smith', 'phone' => '987654321', 'email' => 'jane@example.com']);
            $customers = Customer::limit(5)->get();
        }
        
        $b2bAgents = B2bAgent::limit(5)->get();
        if ($b2bAgents->isEmpty()) {
            B2bAgent::create(['agent_name' => 'Travel Agent X', 'company_name' => 'X Travel', 'phone' => '1111111', 'status' => 'active']);
            $b2bAgents = B2bAgent::limit(5)->get();
        }

        $allAirlines = Airline::all();
        $allAirports = Airport::all();
        
        // 4. Create Tickets
        for ($i = 0; $i < 15; $i++) {
            $isB2b = rand(0, 1) == 1;
            $customer_id = $isB2b ? null : $customers->random()->id;
            $b2b_agent_id = $isB2b ? $b2bAgents->random()->id : null;
            
            $base_fare = rand(300, 1500) * 1.0;
            $taxes = rand(50, 200) * 1.0;
            $profit = rand(50, 150) * 1.0;
            $total = $base_fare + $taxes + $profit;
            
            $ticket = AirlineTicket::create([
                'ticket_number' => 'TK-' . strtoupper(Str::random(6)),
                'customer_id' => $customer_id,
                'b2b_agent_id' => $b2b_agent_id,
                'pnr' => strtoupper(Str::random(6)),
                'booking_reference' => strtoupper(Str::random(8)),
                'ticket_date' => now()->subDays(rand(0, 30))->format('Y-m-d'),
                'ticket_type' => ['One Way', 'Round Trip', 'Multi-City'][rand(0, 2)],
                'route_type' => ['Domestic', 'International'][rand(0, 1)],
                'base_fare' => $base_fare,
                'taxes' => $taxes,
                'airline_charges' => 0,
                'service_charges' => 0,
                'discount' => 0,
                'other_charges' => 0,
                'total_fare' => $total,
                'amount_paid' => $total,
                'amount_pending' => 0,
                'payment_status' => 'Paid',
                'ticket_status' => ['Issued', 'Confirmed'][rand(0, 1)],
                'notes' => 'Dummy data generated',
                'qr_token' => Str::random(40),
                'created_by' => $user->id,
            ]);
            
            // Passenger
            TicketPassenger::create([
                'ticket_id' => $ticket->id,
                'passenger_name' => 'PASSENGER ' . $i,
                'passenger_type' => 'Adult',
                'ticket_number' => $ticket->ticket_number,
            ]);
            
            // Segments
            $segmentCount = $ticket->ticket_type == 'Round Trip' ? 2 : 1;
            
            for ($s = 0; $s < $segmentCount; $s++) {
                $depAirport = $allAirports->random();
                $arrAirport = $allAirports->where('id', '!=', $depAirport->id)->random();
                $airline = $allAirlines->random();
                
                $depDate = \Carbon\Carbon::parse($ticket->ticket_date)->addDays(rand(1, 10) + ($s * 5));
                $arrDate = $depDate->copy()->addHours(rand(2, 8));
                
                TicketFlightSegment::create([
                    'ticket_id' => $ticket->id,
                    'segment_order' => $s,
                    'airline_id' => $airline->id,
                    'flight_number' => $airline->code . rand(100, 999),
                    'departure_airport_id' => $depAirport->id,
                    'arrival_airport_id' => $arrAirport->id,
                    'departure_date' => $depDate->format('Y-m-d'),
                    'departure_time' => $depDate->format('H:i'),
                    'arrival_date' => $arrDate->format('Y-m-d'),
                    'arrival_time' => $arrDate->format('H:i'),
                ]);
            }
        }
    }
}
