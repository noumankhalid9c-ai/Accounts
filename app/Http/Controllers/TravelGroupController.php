<?php

namespace App\Http\Controllers;

use App\Models\TravelGroup;
use App\Models\GroupClient;
use App\Models\GroupFlight;
use App\Models\GroupHotel;
use App\Models\GroupTransport;
use Illuminate\Http\Request;
use Str;

class TravelGroupController extends Controller
{
    public function index()
    {
        $groups = TravelGroup::withCount(['clients', 'vouchers'])->orderBy('id', 'desc')->get();
        return view('travel_groups.index', compact('groups'));
    }

    public function create()
    {
        return view('travel_groups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'group_id' => 'required|string|unique:travel_groups',
            'group_name' => 'required|string|max:255',
            'service_type' => 'required|string',
            'destination' => 'nullable|string',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'group_leader' => 'nullable|string',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        
        TravelGroup::create($data);
        return redirect()->route('travel-groups.index')->with('success', 'Group Created Successfully!');
    }

    public function show(TravelGroup $travelGroup)
    {
        $travelGroup->load(['clients', 'flights', 'hotels', 'transports', 'vouchers']);
        return view('travel_groups.show', compact('travelGroup'));
    }

    public function storeClient(Request $request, TravelGroup $travelGroup)
    {
        $data = $request->validate([
            'client_name' => 'required|string|max:255',
            'father_name' => 'nullable|string',
            'gender' => 'nullable|string',
            'passport_number' => 'nullable|string',
            'passport_expiry' => 'nullable|date',
            'phone' => 'nullable|string',
            'package' => 'nullable|string',
            'pax_type' => 'required|string',
            'room_type' => 'nullable|string',
            'bed' => 'nullable|string',
            'group_number' => 'nullable|string',
            'visa_number' => 'nullable|string',
            'pnr' => 'nullable|string',
            'payment_status' => 'required|string',
            'booking_status' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        $travelGroup->clients()->create($data);
        return back()->with('success', 'Client Added!');
    }

    public function storeFlight(Request $request, TravelGroup $travelGroup)
    {
        $data = $request->validate([
            'flight_type' => 'required|string',
            'flight_number' => 'nullable|string',
            'sector' => 'nullable|string',
            'departure_date' => 'nullable|date',
            'departure_time' => 'nullable|string',
            'arrival_date' => 'nullable|date',
            'arrival_time' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $travelGroup->flights()->create($data);
        return back()->with('success', 'Flight Added!');
    }

    public function storeHotel(Request $request, TravelGroup $travelGroup)
    {
        $data = $request->validate([
            'city' => 'nullable|string',
            'hotel_name' => 'nullable|string',
            'view' => 'nullable|string',
            'meal' => 'nullable|string',
            'confirmation_number' => 'nullable|string',
            'room_type' => 'nullable|string',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'nights' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);
        $travelGroup->hotels()->create($data);
        return back()->with('success', 'Hotel Added!');
    }

    public function storeTransport(Request $request, TravelGroup $travelGroup)
    {
        $data = $request->validate([
            'travel_date' => 'nullable|date',
            'transporter' => 'nullable|string',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'pickup_location' => 'nullable|string',
            'drop_location' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $travelGroup->transports()->create($data);
        return back()->with('success', 'Transport Added!');
    }
}
