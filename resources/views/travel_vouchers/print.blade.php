<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Voucher - {{ $travelVoucher->voucher_number }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts for Urdu/Arabic support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { 
            background: #fff; 
            font-family: Arial, sans-serif; 
            font-size: 11px; 
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact; 
        }
        .voucher-container { 
            max-width: 950px; 
            margin: 0 auto; 
            padding: 10px;
        }
        
        /* Header section */
        .header-title { font-size: 16px; font-weight: bold; color: #000080; margin-bottom: 2px;}
        .header-label { width: 90px; display: inline-block; }
        .header-val { font-weight: normal; }
        
        /* Green Bar */
        .green-bar {
            border: 1px solid #000;
            margin-top: 5px;
            margin-bottom: 5px;
            display: flex;
        }
        .green-bar-section {
            padding: 4px 8px;
            border-right: 1px solid #000;
            font-weight: bold;
        }
        .green-bar-section:last-child { border-right: none; }
        .bg-green-dark { background-color: #198754; color: white; }
        .bg-green-light { background-color: #d1e7dd; color: #0f5132; }

        /* Tables */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .table-custom th, .table-custom td {
            border: 1px solid #000;
            padding: 2px 4px;
            text-align: center;
            vertical-align: middle;
        }
        .table-custom td {
            border-style: dotted;
            border-width: 1px 0;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }
        .table-custom tr:last-child td {
            border-bottom: 1px solid #000;
        }
        
        /* Section Headers */
        .section-header {
            background-color: #003399;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 3px;
            border: 1px solid #000;
            border-bottom: none;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        /* Sub Headers */
        .sub-header th {
            background-color: #e6e6e6;
            font-weight: bold;
            border: 1px solid #000;
        }
        
        .qr-code { width: 90px; height: 90px; }
        
        /* Urdu text */
        .urdu-text {
            font-family: 'Noto Nastaliq Urdu', serif;
            direction: rtl;
            text-align: right;
            font-size: 13px;
            line-height: 1.8;
            margin-top: 15px;
        }

        .text-start-custom { text-align: left !important; }

        @media print {
            body { padding: 0; margin: 0; }
            .no-print { display: none !important; }
            .voucher-container { width: 100%; max-width: 100%; padding: 0; margin-top: 0; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>

<div class="text-center my-3 no-print">
    <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="bi bi-printer"></i> Print Voucher</button>
    <button onclick="window.close()" class="btn btn-light btn-sm border">Close</button>
</div>

<div class="voucher-container">
    
    <!-- Top Header Area -->
    <table style="width: 100%; margin-bottom: 5px;">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: top;">
                <!-- Placeholder for Logo -->
                <img src="{{ asset('MADINA-LOGO-3.png') }}" style="height: 60px; max-width: 100%; object-fit: contain;" alt="Logo">
            </td>
            <td style="width: 70%; vertical-align: top; padding-left: 10px;">
                <div class="header-title">MADINA SHAREEF TRAVELS</div>
                
                <div style="display: flex; flex-wrap: wrap;">
                    <div style="width: 50%;">
                        <div><span class="header-label">Voucher Date:</span> <span class="header-val">{{ $travelVoucher->voucher_date->format('d/m/y') }}</span></div>
                        <div>
                            <span class="header-label">Package:</span> <span class="header-val">{{ $travelVoucher->package_number ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="header-label">PAX:</span> <span class="header-val">{{ $travelVoucher->pax }}</span>
                        </div>
                    </div>
                    <div style="width: 50%; text-align: right; padding-right: 15px;">
                        <div>Address: 211A- GT Road Opposite Brains Baghbanpura, Lahore</div>
                        <div>Whats APP: <span class="fw-bold">{{ $travelVoucher->whatsapp ?? 'N/A' }}</span></div>
                    </div>
                </div>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: top;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode(route('travel-vouchers.verify', $travelVoucher->qr_token)) }}" class="qr-code" alt="QR">
            </td>
        </tr>
    </table>

    <!-- Green Bar -->
    <div class="green-bar">
        <div class="green-bar-section bg-green-light" style="width: 35%; color: #000;">
            <span style="color: #198754;">F.Head</span> &nbsp;&nbsp; {{ strtoupper($travelVoucher->group_head ?? $travelVoucher->travelGroup->group_leader ?? 'N/A') }}
        </div>
        <div class="green-bar-section bg-green-dark" style="width: 25%; text-align: center;">
            {{ $travelVoucher->voucher_number }}
        </div>
        <div class="green-bar-section bg-green-light" style="width: 20%; color: #000;">
            <span style="color: #198754;">Grp:</span> {{ $travelVoucher->travelGroup->group_id }}
        </div>
        <div class="green-bar-section bg-green-light" style="width: 20%; color: #000;">
            <span style="color: #198754;">Flight:</span> <strong>{{ $travelVoucher->flight_numbers_summary }}</strong>
        </div>
    </div>

    <!-- FLIGHTS -->
    <div class="section-header">FLIGHT DETAILS ({{ $travelVoucher->flight_numbers_summary }})</div>
    <table class="table-custom">
        <tr class="sub-header">
            <th colspan="4" style="background-color: #003399; color: white; border: 1px solid #000;">DEPARTURE</th>
            <th colspan="4" style="background-color: #003399; color: white; border: 1px solid #000;">ARRIVAL / RETURN</th>
        </tr>
        <tr class="sub-header">
            <th>Flight</th>
            <th>Sector</th>
            <th>Departure</th>
            <th>Arrival</th>
            <th>Flight</th>
            <th>Sector</th>
            <th>Departure</th>
            <th>Arrival</th>
        </tr>
        @php
            $flights = $travelVoucher->flights;
            $depFlights = [];
            $arrFlights = [];
            $otherFlights = [];

            foreach ($flights as $flt) {
                $type = strtolower($flt['flight_type'] ?? '');
                if (str_contains($type, 'dep') || str_contains($type, 'outbound')) {
                    $depFlights[] = $flt;
                } elseif (str_contains($type, 'ret') || str_contains($type, 'arr') || str_contains($type, 'inbound')) {
                    $arrFlights[] = $flt;
                } else {
                    $otherFlights[] = $flt;
                }
            }

            if (empty($depFlights) && empty($arrFlights) && !empty($otherFlights)) {
                $depFlights[] = array_shift($otherFlights);
                if (!empty($otherFlights)) {
                    $arrFlights = $otherFlights;
                }
            } elseif (!empty($otherFlights)) {
                foreach ($otherFlights as $of) {
                    if (count($depFlights) <= count($arrFlights)) {
                        $depFlights[] = $of;
                    } else {
                        $arrFlights[] = $of;
                    }
                }
            }

            $maxRows = max(count($depFlights), count($arrFlights), 1);
            $depFlights = array_values($depFlights);
            $arrFlights = array_values($arrFlights);

            $formatDateTime = function($d, $t) {
                if (empty($d)) return '-';
                try {
                    $str = \Carbon\Carbon::parse($d)->format('d-M');
                    if (!empty($t)) {
                        $str .= ' ' . trim($t);
                    }
                    return $str;
                } catch (\Exception $e) {
                    return $d . ($t ? ' ' . $t : '');
                }
            };
        @endphp

        @if(!empty($flights))
            @for($i = 0; $i < $maxRows; $i++)
                <tr>
                    @if(isset($depFlights[$i]))
                        <td style="font-weight: bold; color: #000080;">{{ strtoupper($depFlights[$i]['flight_number'] ?? '-') }}</td>
                        <td>{{ strtoupper($depFlights[$i]['sector'] ?? '-') }}</td>
                        <td>{{ $formatDateTime($depFlights[$i]['departure_date'] ?? null, $depFlights[$i]['departure_time'] ?? null) }}</td>
                        <td>{{ $formatDateTime($depFlights[$i]['arrival_date'] ?? null, $depFlights[$i]['arrival_time'] ?? null) }}</td>
                    @else
                        <td>-</td><td>-</td><td>-</td><td>-</td>
                    @endif
                    
                    @if(isset($arrFlights[$i]))
                        <td style="font-weight: bold; color: #000080;">{{ strtoupper($arrFlights[$i]['flight_number'] ?? '-') }}</td>
                        <td>{{ strtoupper($arrFlights[$i]['sector'] ?? '-') }}</td>
                        <td>{{ $formatDateTime($arrFlights[$i]['departure_date'] ?? null, $arrFlights[$i]['departure_time'] ?? null) }}</td>
                        <td>{{ $formatDateTime($arrFlights[$i]['arrival_date'] ?? null, $arrFlights[$i]['arrival_time'] ?? null) }}</td>
                    @else
                        <td>-</td><td>-</td><td>-</td><td>-</td>
                    @endif
                </tr>
            @endfor
        @else
            <tr><td colspan="8" style="padding: 6px; color: #666;">No flight details registered for this voucher</td></tr>
        @endif
    </table>


    <!-- ACCOMMODATION -->
    <div class="section-header">ACCOMMODATION</div>
    <table class="table-custom">
        <tr class="sub-header">
            <th>City</th>
            <th class="text-start-custom">Hotel Name</th>
            <th>View</th>
            <th>Meal</th>
            <th>Conf#</th>
            <th>Room Type</th>
            <th>Checkin</th>
            <th>Checkout</th>
            <th>Nights</th>
        </tr>
        @php $totalNights = 0; @endphp
        @if(!empty($travelVoucher->snapshot_data['hotels']))
            @foreach($travelVoucher->snapshot_data['hotels'] as $hotel)
                @php $totalNights += (int)($hotel['nights'] ?? 0); @endphp
                <tr>
                    <td>{{ $hotel['city'] ?? '-' }}</td>
                    <td class="text-start-custom">{{ $hotel['hotel_name'] ?? '-' }}</td>
                    <td>{{ $hotel['view'] ?? 'Standard' }}</td>
                    <td>{{ $hotel['meal'] ?? 'RO' }}</td>
                    <td>{{ $hotel['confirmation_number'] ?? '-' }}</td>
                    <td>{{ $hotel['room_type'] ?? '-' }}</td>
                    <td>{{ $hotel['check_in'] ? \Carbon\Carbon::parse($hotel['check_in'])->format('d-m-y') : '-' }}</td>
                    <td>{{ $hotel['check_out'] ? \Carbon\Carbon::parse($hotel['check_out'])->format('d-m-y') : '-' }}</td>
                    <td>{{ $hotel['nights'] ?? '-' }}</td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="9">No hotels added</td></tr>
        @endif
    </table>
    
    <div style="text-align: right; margin-bottom: 5px;">
        <span style="display: inline-block; margin-right: 10px;">Total Nights:</span>
        <span style="display: inline-block; border: 1px solid #000; padding: 2px 15px; font-weight: bold;">{{ $totalNights > 0 ? $totalNights : '' }}</span>
    </div>

    <!-- TRANSPORT -->
    <div class="section-header">TRANSPORT DETAIL</div>
    <table class="table-custom">
        <tr class="sub-header">
            <th>Travel Date</th>
            <th>Transporter</th>
            <th>Type</th>
            <th class="text-start-custom" style="width: 40%">Description</th>
        </tr>
        @if(!empty($travelVoucher->snapshot_data['transports']))
            @foreach($travelVoucher->snapshot_data['transports'] as $transport)
                <tr>
                    <td>{{ $transport['travel_date'] ? \Carbon\Carbon::parse($transport['travel_date'])->format('d-M-y') : '-' }}</td>
                    <td>{{ $transport['transporter'] ?? '-' }}</td>
                    <td>{{ $transport['type'] ?? '-' }}</td>
                    <td class="text-start-custom">{{ $transport['description'] ?? '-' }} ({{ $transport['pickup_location'] ?? '' }} - {{ $transport['drop_location'] ?? '' }})</td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="4">No transport added</td></tr>
        @endif
    </table>

    <!-- MUTAMERS -->
    <div class="section-header">MUTAMERS</div>
    <table class="table-custom">
        <tr class="sub-header">
            <th style="width: 4%">SNO</th>
            <th style="width: 12%">Passport</th>
            <th class="text-start-custom" style="width: 30%">Mutamer Name</th>
            <th style="width: 5%">G</th>
            <th style="width: 8%">PAX</th>
            <th style="width: 8%">Bed</th>
            <th style="width: 8%">Group #</th>
            <th style="width: 15%">Visa #</th>
            <th style="width: 10%">PNR</th>
        </tr>
        @if(!empty($travelVoucher->snapshot_data['clients']))
            @foreach($travelVoucher->snapshot_data['clients'] as $index => $client)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ strtoupper($client['passport_number'] ?? '-') }}</td>
                    <td class="text-start-custom">{{ strtoupper($client['client_name']) }}</td>
                    <td>{{ strtoupper(substr($client['gender'] ?? 'M', 0, 1)) }}</td>
                    <td>{{ $client['pax_type'] }}</td>
                    <td>{{ $client['bed'] ?? 'Yes' }}</td>
                    <td>{{ $client['group_number'] ?? '-' }}</td>
                    <td>{{ $client['visa_number'] ?? '-' }}</td>
                    <td>{{ $client['pnr'] ?? '-' }}</td>
                </tr>
            @endforeach
        @else
            <tr><td colspan="9">No mutamers added</td></tr>
        @endif
    </table>

    <!-- Comments & Remarks Box -->
    <div style="margin-top: 8px; border: 1.5px solid #000080; border-radius: 4px; padding: 6px 10px; background-color: #f8fafc;">
        <div style="color: #000080; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 3px; margin-bottom: 4px;">
            Comments / Remarks:
        </div>
        <div style="font-size: 11px; line-height: 1.5; color: #111; min-height: 20px;">
            {!! nl2br(e($travelVoucher->comments ?: ($travelVoucher->special_instructions ?: 'None'))) !!}
        </div>
    </div>

    @if($travelVoucher->special_instructions && $travelVoucher->comments)
    <!-- Special Instructions -->
    <div style="margin-top: 6px; padding: 4px 8px; border: 1px dotted #666; border-radius: 4px;">
        <div style="color: #000080; font-weight: bold; font-size: 11px;">Special Instructions:</div>
        <div style="font-size: 10px; line-height: 1.4; color: #333;">
            {!! nl2br(e($travelVoucher->special_instructions)) !!}
        </div>
    </div>
    @endif


    <!-- Urdu Instructions -->
    <div class="urdu-text">
        <div style="font-weight: bold; font-size: 16px; margin-bottom: 5px;">ضروری ہدایات:-</div>
        <p style="margin-bottom: 5px;">
            ٭ سعودیہ میں معتمرین سے پاسپورٹ لینے کی کسی کو اجازت نہ ہے۔ لہٰذا اپنا پاسپورٹ اپنے پاس سنبھال کے رکھیں۔ پاسپورٹ گم ہونے کی صورت میں آؤٹ پاس اور ٹکٹ کے چارجز معتمر پر عائد ہونگے۔ 
            ٭ ہوٹل میں چیک ان اور چیک آؤٹ کا وقت ظہر 2 بجے ہے۔ جبکہ مدینہ میں ہوٹل خالی اور صفائی کی صورت میں کچھ دیر انتظار کرنا پڑ سکتا ہے۔
            ٭ معتمرین ووچر پر درج شیڈول یا ادارے کے سٹاف کی طرف سے دیئے گئے روانگی اوقات پر عمل کرنے کے پابند ہونگے۔
        </p>
        <p style="margin-bottom: 5px;">
            ٭ معتمر کو مکہ سے مدینہ، مدینہ سے مکہ، مکہ سے ائیرپورٹ روانگی سے 24 گھنٹے قبل سٹاف کو اپنا روانگی شیڈول نوٹ کروانا ہوگا۔
            ٭ مدینہ روانگی کیلئے صبح 7 بجے ہوٹل سے اپنا سامان اٹھا کر سٹاف کی طرف سے بس آمد کی بتائے گئے مقام پر آنا ضروری ہوگا۔
            ٭ مکہ سے جدہ ائیرپورٹ روانگی کیلئے 8 گھنٹے پہلے ہوٹل چھوڑنا ہوگا۔
            ٭ کسی بھی سیکٹر کی ٹرانسپورٹ چھوٹ جانے پر دوبارہ ٹرانسپورٹ فراہم نہیں کی جائے گی۔
        </p>
        <p style="margin-bottom: 5px;">
            ٭ دو بار ٹرانسپورٹ حاصل کرنے کے الگ چارجز ہونگے۔
            ٭ پرواز چھوٹ جانے کی صورت میں ادارہ ذمہ دار نہ ہوگا۔ Extra Night کے چارجز معتمر کو خود ادا کرنے ہونگے۔
            ٭ سعودی قوانین اور پالیسی پر مکمل عملدرآمد کرنے کی ذمہ داری معتمرین پر عائد ہوگی۔
            ٭ کسی بھی پریشانی کی صورت میں عازمین ووچر پر درج شدہ سعودی سٹاف کے نمبرز پر رابطہ کریں۔
        </p>
        <p style="font-weight: bold; text-align: center;">
            نوٹ : مندرجہ بالا ہدایات پر عملدرآمد کو یقینی بنائیں۔ کوتاہی کی صورت میں ہونیوالے کسی بھی نقصان کی ذمہ داری معتمرین پر ہوگی۔
        </p>
    </div>

</div>

</body>
</html>
