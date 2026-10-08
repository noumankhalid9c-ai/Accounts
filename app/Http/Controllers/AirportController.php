<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    public function index()
    {
        $airports = Airport::latest()->get();
        return view('airports.index', compact('airports'));
    }

    public function create()
    {
        return view('airports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'iata_code' => 'required|string|max:10|unique:airports',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status');

        Airport::create($data);

        return redirect()->route('airports.index')->with('success', 'Airport created successfully.');
    }

    public function edit(Airport $airport)
    {
        return view('airports.edit', compact('airport'));
    }

    public function update(Request $request, Airport $airport)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'iata_code' => 'required|string|max:10|unique:airports,iata_code,' . $airport->id,
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status');

        $airport->update($data);

        return redirect()->route('airports.index')->with('success', 'Airport updated successfully.');
    }

    public function destroy(Airport $airport)
    {
        $airport->update(['status' => false]);
        return redirect()->route('airports.index')->with('success', 'Airport deactivated successfully.');
    }
}
