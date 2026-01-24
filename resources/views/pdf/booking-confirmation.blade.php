<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Mach Hotel — Booking Receipt</title>
  <style>
    /* Hard lock page size to prevent pagination weirdness */
    @page { margin: 12px; size: 360pt 520pt; }

    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color:#0b0f14; margin:0; }
    .sheet { border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; }

    .header {
  background:#ffffff;
  color:#0b0f14;
  padding: 10px 12px 8px;
  position: relative;
  text-align:center;
  border-bottom: 1px solid #e5e7eb;
}


    .goldline { height:5px; background:#b8923b; }

    .logo { width:80px; height:auto; display:block; margin:0 auto 6px; }
    .brand { font-weight:900; font-size:14px; letter-spacing:.03em; line-height:1.1; }
    .tag { font-size:9px; color:rgba(255,255,255,.75); margin-top:3px; }

.badge {
  position:absolute;
  right:10px;
  top:10px;
  border:2px solid #b8923b;
  color:#b8923b;
  padding:5px 8px;
  border-radius:10px;
  font-weight:900;
  letter-spacing:.10em;
  font-size:9px;
  background: #fffdf5;
  transform: rotate(-6deg);
}

    .content { padding: 10px 12px 10px; background:#fff; }
    .card { border:1px solid #e5e7eb; border-radius:10px; padding:8px; }
    .soft { background:#f8fafc; }

    .label { font-size:8px; color:#6b7280; text-transform:uppercase; letter-spacing:.06em; margin-bottom:2px; }
    .value { font-weight:800; }
    .code { font-weight:900; letter-spacing:.14em; font-size:12px; }

    table { width:100%; border-collapse:collapse; }
    td { vertical-align:top; padding:0; }

    .divider { height:1px; background:#e5e7eb; margin:8px 0; }

    .qrWrap { text-align:center; }
    .qrImg { width:78px; height:78px; display:block; margin:0 auto 5px; }
    .tiny { font-size:8px; color:#6b7280; }

    .foot {
      margin-top:8px;
      border:1px dashed #e5e7eb;
      border-radius:10px;
      padding:8px;
      font-size:8px;
      color:#6b7280;
      line-height:1.35;
    }
  </style>
</head>
<body>
@php
  $checkIn  = \Carbon\Carbon::parse($booking->check_in);
  $checkOut = \Carbon\Carbon::parse($booking->check_out);
  $nights = $checkIn->diffInDays($checkOut);
  $logoPath = public_path('assets/img/logo.png');
@endphp

<div class="sheet">
  <div class="header">
    <div class="badge">CONFIRMED</div>

    @if(file_exists($logoPath))
      <img class="logo" src="{{ $logoPath }}" alt="Mach Hotel Logo">
    @endif

    <div class="brand">Mach Hotel</div>
    <div class="tag">Booking Receipt • Pay at Counter • Dar es Salaam</div>
  </div>
  <div class="goldline"></div>

  <div class="content">

    {{-- TOP ROW: Code + Total + QR (TABLE, no flex) --}}
    <table>
      <tr>
        <td style="width: 58%; padding-right:8px;">
          <div class="card">
            <div class="label">Booking Code</div>
            <div class="code">{{ $booking->code }}</div>

            <div class="divider"></div>

            <div class="label">Status</div>
            <div class="value">{{ $booking->status }}</div>

            <div class="divider"></div>

            <div class="label">Dates</div>
<div class="label">Check-in</div>
<div class="value">{{ $checkIn->format('Y-m-d') }}</div>
<div class="tiny">From 14:00 (2:00 PM)</div>

<div style="height:6px;"></div>

<div class="label">Check-out</div>
<div class="value">{{ $checkOut->format('Y-m-d') }}</div>
<div class="tiny">Before 11:00 AM</div>

<div style="height:6px;"></div>

<div class="tiny">Nights: {{ $nights }}</div>


          </div>
        </td>

        <td style="width: 42%;">
          <div class="card soft">
            <div class="label">Total Amount</div>
            <div style="font-size:14px;font-weight:900;">
              TZS {{ number_format($booking->total_amount) }}
            </div>
            <div class="tiny">Pay at counter on arrival</div>

            <div class="divider"></div>

            <div class="qrWrap">
              <img class="qrImg" src="{{ $qrDataUri }}" alt="QR Code">
              <div class="label">Scan</div>
              <div class="tiny">{{ $booking->code }}</div>
            </div>
          </div>
        </td>
      </tr>
    </table>

    <div style="height:8px;"></div>

    {{-- Guest + Room (TABLE) --}}
    <table>
      <tr>
        <td style="width: 50%; padding-right:8px;">
          <div class="card">
            <div class="label">Guest</div>
            <div class="value">{{ $booking->guest->full_name }}</div>
            <div class="tiny">{{ $booking->guest->phone }}</div>
          </div>
        </td>
        <td style="width: 50%;">
<div class="card">
  <div class="label">Room Type</div>
  <div class="value">{{ $booking->roomType?->name ?? '—' }}</div>

  @if($booking->roomType)
    <div class="tiny">
      TZS {{ number_format($booking->roomType->price_tzs) }}/night
      • USD {{ number_format($booking->roomType->price_usd) }}
      • Cap {{ $booking->roomType->capacity }}
    </div>
    <div class="tiny">Assigned at reception</div>
  @endif
</div>

        </td>
      </tr>
    </table>

    {{-- Special requests (CLAMPED hard to prevent page 2) --}}
    @if($booking->special_requests)
      <div style="height:8px;"></div>
      <div class="card">
        <div class="label">Special Requests</div>
        <div class="tiny">
          {{ \Illuminate\Support\Str::limit($booking->special_requests, 90) }}
        </div>
      </div>
    @endif

    <div class="foot">
      <strong>Mach Hotel</strong> • Keep your booking code safe. Show this receipt at reception during check-in.<br>
      This is system-generated. Payment is made at the counter upon arrival.
    </div>

  </div>
</div>

</body>
</html>
