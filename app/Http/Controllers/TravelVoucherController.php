<?php

namespace App\Http\Controllers;

use App\Models\TravelGroup;
use App\Models\TravelVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class TravelVoucherController extends Controller
{
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
            'special_instructions' => 'nullable|string',
        ]);
        
        $data['qr_token'] = Str::random(32);
        
        // Snapshot the current state of the group
        $travelGroup->load(['clients', 'flights', 'hotels', 'transports']);
        $data['snapshot_data'] = $travelGroup->toArray();
        
        $voucher = $travelGroup->vouchers()->create($data);
        return redirect()->route('travel-vouchers.show', $voucher)->with('success', 'Voucher Created!');
    }

    public function show(TravelVoucher $travelVoucher)
    {
        return view('travel_vouchers.show', compact('travelVoucher'));
    }

    public function print(TravelVoucher $travelVoucher)
    {
        return view('travel_vouchers.print', compact('travelVoucher'));
    }

    public function verify($token)
    {
        $voucher = TravelVoucher::with('travelGroup')->where('qr_token', $token)->firstOrFail();
        return view('travel_vouchers.verify', compact('voucher'));
    }
}
