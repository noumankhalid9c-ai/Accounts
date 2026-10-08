<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    public function index()
    {
        $airlines = Airline::latest()->get();
        return view('airlines.index', compact('airlines'));
    }

    public function create()
    {
        return view('airlines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airlines',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('airlines', 'public');
        }

        $data['status'] = $request->has('status');

        Airline::create($data);

        return redirect()->route('airlines.index')->with('success', 'Airline created successfully.');
    }

    public function edit(Airline $airline)
    {
        return view('airlines.edit', compact('airline'));
    }

    public function update(Request $request, Airline $airline)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airlines,code,' . $airline->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact' => 'nullable|string|max:255',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('airlines', 'public');
        }

        $data['status'] = $request->has('status');

        $airline->update($data);

        return redirect()->route('airlines.index')->with('success', 'Airline updated successfully.');
    }

    public function destroy(Airline $airline)
    {
        // Don't actually delete to preserve history, just deactivate
        $airline->update(['status' => false]);
        return redirect()->route('airlines.index')->with('success', 'Airline deactivated successfully.');
    }
}
