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
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:travel_groups.view')->only(['index', 'show']);
        $this->middleware('permission:travel_groups.create')->only(['create', 'store', 'storeClient', 'storeFlight', 'storeHotel', 'storeTransport']);
        $this->middleware('permission:travel_groups.edit')->only(['edit', 'update', 'updateClient', 'updateFlight', 'updateHotel', 'updateTransport']);
        $this->middleware('permission:travel_groups.delete')->only(['destroy', 'destroyClient', 'destroyFlight', 'destroyHotel', 'destroyTransport']);
    }

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

    public function updateClient(Request $request, TravelGroup $travelGroup, GroupClient $client)
    {
        if ($client->travel_group_id !== $travelGroup->id) {
            abort(404);
        }

        $data = $request->validate([
            'client_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:100',
            'passport_expiry' => 'nullable|date',
            'phone' => 'nullable|string|max:100',
            'package' => 'nullable|string|max:255',
            'pax_type' => 'required|string|max:50',
            'room_type' => 'nullable|string|max:100',
            'bed' => 'nullable|string|max:100',
            'group_number' => 'nullable|string|max:100',
            'visa_number' => 'nullable|string|max:100',
            'pnr' => 'nullable|string|max:100',
            'payment_status' => 'required|string|max:50',
            'booking_status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $client->update($data);
        return back()->with('success', 'Client updated successfully!');
    }

    public function destroyClient(TravelGroup $travelGroup, GroupClient $client)
    {
        if ($client->travel_group_id !== $travelGroup->id) {
            abort(404);
        }

        $client->delete();
        return back()->with('success', 'Client removed successfully!');
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

    public function updateFlight(Request $request, TravelGroup $travelGroup, GroupFlight $flight)
    {
        if ($flight->travel_group_id !== $travelGroup->id) {
            abort(404);
        }

        $data = $request->validate([
            'flight_type' => 'required|string|max:50',
            'flight_number' => 'nullable|string|max:100',
            'sector' => 'nullable|string|max:255',
            'departure_date' => 'nullable|date',
            'departure_time' => 'nullable|string|max:50',
            'arrival_date' => 'nullable|date',
            'arrival_time' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $flight->update($data);
        return back()->with('success', 'Flight updated successfully!');
    }

    public function destroyFlight(TravelGroup $travelGroup, GroupFlight $flight)
    {
        if ($flight->travel_group_id !== $travelGroup->id) {
            abort(404);
        }

        $flight->delete();
        return back()->with('success', 'Flight removed successfully!');
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

    public function updateHotel(Request $request, TravelGroup $travelGroup, GroupHotel $hotel)
    {
        if ($hotel->travel_group_id !== $travelGroup->id) {
            abort(404);
        }

        $data = $request->validate([
            'city' => 'nullable|string|max:100',
            'hotel_name' => 'nullable|string|max:255',
            'view' => 'nullable|string|max:100',
            'meal' => 'nullable|string|max:100',
            'confirmation_number' => 'nullable|string|max:100',
            'room_type' => 'nullable|string|max:100',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'nights' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $hotel->update($data);
        return back()->with('success', 'Hotel updated successfully!');
    }

    public function destroyHotel(TravelGroup $travelGroup, GroupHotel $hotel)
    {
        if ($hotel->travel_group_id !== $travelGroup->id) {
            abort(404);
        }

        $hotel->delete();
        return back()->with('success', 'Hotel removed successfully!');
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

    public function updateTransport(Request $request, TravelGroup $travelGroup, GroupTransport $transport)
    {
        if ($transport->travel_group_id !== $travelGroup->id) {
            abort(404);
        }

        $data = $request->validate([
            'travel_date' => 'nullable|date',
            'transporter' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'pickup_location' => 'nullable|string|max:255',
            'drop_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $transport->update($data);
        return back()->with('success', 'Transport updated successfully!');
    }

    public function destroyTransport(TravelGroup $travelGroup, GroupTransport $transport)
    {
        if ($transport->travel_group_id !== $travelGroup->id) {
            abort(404);
        }

        $transport->delete();
        return back()->with('success', 'Transport removed successfully!');
    }

    public function edit(TravelGroup $travelGroup)
    {
        return view('travel_groups.edit', compact('travelGroup'));
    }

    public function update(Request $request, TravelGroup $travelGroup)
    {
        $data = $request->validate([
            'group_id' => 'required|string|unique:travel_groups,group_id,' . $travelGroup->id,
            'group_name' => 'required|string|max:255',
            'service_type' => 'required|string',
            'destination' => 'nullable|string',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'group_leader' => 'nullable|string',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $travelGroup->update($data);
        return redirect()->route('travel-groups.show', $travelGroup->id)->with('success', 'Travel Group updated successfully!');
    }

    public function destroy(TravelGroup $travelGroup)
    {
        $travelGroup->delete();
        return redirect()->route('travel-groups.index')->with('success', 'Travel Group deleted successfully!');
    }
}
