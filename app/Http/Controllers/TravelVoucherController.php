<?php

namespace App\Http\Controllers;

use App\Models\TravelGroup;
use App\Models\TravelVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class TravelVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['verify']);
        $this->middleware('permission:travel_vouchers.view')->only(['show', 'print']);
        $this->middleware('permission:travel_vouchers.create')->only(['create', 'store']);
        $this->middleware('permission:travel_vouchers.edit')->only(['edit', 'update']);
        $this->middleware('permission:travel_vouchers.delete')->only(['destroy']);
    }

    public function create(TravelGroup $travelGroup)
    {
        $travelGroup->load(['clients', 'flights', 'hotels', 'transports']);
        return view('travel_vouchers.create', compact('travelGroup'));
    }

    public function store(Request $request, TravelGroup $travelGroup)
    {
        $data = $request->validate([
            'voucher_number' => 'required|unique:travel_vouchers',
            'voucher_type' => 'required|string',
            'voucher_date' => 'required|date',
            'group_head' => 'nullable|string',
            'package_number' => 'nullable|string',
            'pax' => 'nullable|integer',
            'whatsapp' => 'nullable|string',
            'comments' => 'nullable|string',
            'special_instructions' => 'nullable|string',

            // Direct flight inputs from voucher create form
            'dep_flight_number' => 'nullable|string',
            'dep_sector' => 'nullable|string',
            'dep_date' => 'nullable|date',
            'dep_time' => 'nullable|string',
            'ret_flight_number' => 'nullable|string',
            'ret_sector' => 'nullable|string',
            'ret_date' => 'nullable|date',
            'ret_time' => 'nullable|string',
        ]);

        // If departure flight number or details provided, sync/create into travelGroup->flights
        if (!empty($request->dep_flight_number) || !empty($request->dep_sector)) {
            $depFlight = $travelGroup->flights()->where('flight_type', 'Departure')->first();
            $depData = [
                'flight_type' => 'Departure',
                'flight_number' => $request->dep_flight_number,
                'sector' => $request->dep_sector,
                'departure_date' => $request->dep_date,
                'departure_time' => $request->dep_time,
            ];
            if ($depFlight) {
                $depFlight->update(array_filter($depData, fn($v) => !is_null($v)));
            } else {
                $travelGroup->flights()->create($depData);
            }
        }

        // If return flight number or details provided, sync/create into travelGroup->flights
        if (!empty($request->ret_flight_number) || !empty($request->ret_sector)) {
            $retFlight = $travelGroup->flights()->where(function($q) {
                $q->where('flight_type', 'Return')->orWhere('flight_type', 'Arrival');
            })->first();
            $retData = [
                'flight_type' => 'Return',
                'flight_number' => $request->ret_flight_number,
                'sector' => $request->ret_sector,
                'departure_date' => $request->ret_date,
                'departure_time' => $request->ret_time,
            ];
            if ($retFlight) {
                $retFlight->update(array_filter($retData, fn($v) => !is_null($v)));
            } else {
                $travelGroup->flights()->create($retData);
            }
        }

        $voucherData = [
            'voucher_number' => $data['voucher_number'],
            'voucher_type' => $data['voucher_type'],
            'voucher_date' => $data['voucher_date'],
            'group_head' => $data['group_head'] ?? null,
            'package_number' => $data['package_number'] ?? null,
            'pax' => $data['pax'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'comments' => $data['comments'] ?? null,
            'special_instructions' => $data['special_instructions'] ?? null,
            'qr_token' => Str::random(32),
            'created_by' => auth()->id(),
        ];

        // Freshly reload relations including any updated/created flights
        $travelGroup->load(['clients', 'flights', 'hotels', 'transports']);
        $voucherData['snapshot_data'] = $travelGroup->toArray();

        $voucher = $travelGroup->vouchers()->create($voucherData);
        return redirect()->route('travel-vouchers.show', $voucher)->with('success', 'Travel Voucher Created with all flight and group details!');
    }

    public function show(TravelVoucher $travelVoucher)
    {
        $travelVoucher->load('travelGroup.flights');
        return view('travel_vouchers.show', compact('travelVoucher'));
    }

    public function edit(TravelVoucher $travelVoucher)
    {
        $travelGroup = $travelVoucher->travelGroup;
        $travelGroup->load(['clients', 'flights', 'hotels', 'transports']);
        return view('travel_vouchers.edit', compact('travelVoucher', 'travelGroup'));
    }

    public function update(Request $request, TravelVoucher $travelVoucher)
    {
        $data = $request->validate([
            'voucher_type' => 'required|string',
            'voucher_date' => 'required|date',
            'group_head' => 'nullable|string',
            'package_number' => 'nullable|string',
            'pax' => 'nullable|integer',
            'whatsapp' => 'nullable|string',
            'comments' => 'nullable|string',
            'special_instructions' => 'nullable|string',

            'dep_flight_number' => 'nullable|string',
            'dep_sector' => 'nullable|string',
            'dep_date' => 'nullable|date',
            'dep_time' => 'nullable|string',
            'ret_flight_number' => 'nullable|string',
            'ret_sector' => 'nullable|string',
            'ret_date' => 'nullable|date',
            'ret_time' => 'nullable|string',
        ]);

        $travelGroup = $travelVoucher->travelGroup;
        if ($travelGroup) {
            if (!empty($request->dep_flight_number)) {
                $depFlight = $travelGroup->flights()->where('flight_type', 'Departure')->first();
                if ($depFlight) {
                    $depFlight->update([
                        'flight_number' => $request->dep_flight_number,
                        'sector' => $request->dep_sector,
                        'departure_date' => $request->dep_date,
                        'departure_time' => $request->dep_time,
                    ]);
                } else {
                    $travelGroup->flights()->create([
                        'flight_type' => 'Departure',
                        'flight_number' => $request->dep_flight_number,
                        'sector' => $request->dep_sector,
                        'departure_date' => $request->dep_date,
                        'departure_time' => $request->dep_time,
                    ]);
                }
            }

            if (!empty($request->ret_flight_number)) {
                $retFlight = $travelGroup->flights()->where(function($q) {
                    $q->where('flight_type', 'Return')->orWhere('flight_type', 'Arrival');
                })->first();
                if ($retFlight) {
                    $retFlight->update([
                        'flight_number' => $request->ret_flight_number,
                        'sector' => $request->ret_sector,
                        'departure_date' => $request->ret_date,
                        'departure_time' => $request->ret_time,
                    ]);
                } else {
                    $travelGroup->flights()->create([
                        'flight_type' => 'Return',
                        'flight_number' => $request->ret_flight_number,
                        'sector' => $request->ret_sector,
                        'departure_date' => $request->ret_date,
                        'departure_time' => $request->ret_time,
                    ]);
                }
            }

            $travelGroup->load(['clients', 'flights', 'hotels', 'transports']);
            $travelVoucher->snapshot_data = $travelGroup->toArray();
        }

        $travelVoucher->update([
            'voucher_type' => $data['voucher_type'],
            'voucher_date' => $data['voucher_date'],
            'group_head' => $data['group_head'] ?? null,
            'package_number' => $data['package_number'] ?? null,
            'pax' => $data['pax'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'comments' => $data['comments'] ?? null,
            'special_instructions' => $data['special_instructions'] ?? null,
            'snapshot_data' => $travelVoucher->snapshot_data,
        ]);

        return redirect()->route('travel-vouchers.show', $travelVoucher)->with('success', 'Voucher updated successfully!');
    }

    public function print(TravelVoucher $travelVoucher)
    {
        $travelVoucher->load('travelGroup.flights');
        return view('travel_vouchers.print', compact('travelVoucher'));
    }

    public function verify($token)
    {
        $voucher = TravelVoucher::with(['travelGroup.flights'])->where('qr_token', $token)->firstOrFail();
        return view('travel_vouchers.verify', compact('voucher'));
    }
}

