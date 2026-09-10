<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Van settlement #{{ $s->id }} · {{ $s->license_plate }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font: 13px/1.45 system-ui, -apple-system, "Segoe UI", sans-serif; color: #0f172a; margin: 24px; }
        h1 { font-size: 18px; margin: 0; }
        .muted { color: #64748b; }
        .head { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid #0f172a; padding-bottom: 8px; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
        td.n, th.n { text-align: right; font-variant-numeric: tabular-nums; }
        .short { color: #be2a2a; font-weight: 600; }
        .over { color: #9a6a03; font-weight: 600; }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; margin-top: 16px; }
        .sign { margin-top: 48px; display: grid; grid-template-columns: 1fr 1fr; gap: 48px; }
        .sign div { border-top: 1px solid #0f172a; padding-top: 4px; }
        .status { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; background: #f1f5f9; }
        @media print { body { margin: 0; } .noprint { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="head">
        <div>
            <h1>{{ $business }} — van settlement</h1>
            <div class="muted">{{ $s->license_plate }} · {{ \Illuminate\Support\Carbon::parse($s->settlement_date)->format('l, d M Y') }} · #{{ $s->id }}</div>
        </div>
        <span class="status">{{ str_replace('_', ' ', $s->status) }}</span>
    </div>

    <div class="muted">Sellers: {{ implode(', ', $sellers) ?: '—' }} · Counted by {{ trim($s->first_name.' '.$s->last_name) }}</div>

    <table>
        <thead>
            <tr><th>Product</th><th class="n">Loaded today</th><th class="n">Sold today</th><th class="n">System</th><th class="n">Counted</th><th class="n">Difference</th></tr>
        </thead>
        <tbody>
            @forelse($lines as $l)
                <tr>
                    <td>{{ $l['name'] }} <span class="muted">{{ $l['sku'] }}</span></td>
                    <td class="n">{{ rtrim(rtrim(number_format($l['loaded_today'], 2), '0'), '.') }}</td>
                    <td class="n">{{ rtrim(rtrim(number_format($l['sold_today'], 2), '0'), '.') }}</td>
                    <td class="n">{{ rtrim(rtrim(number_format($l['qty'], 2), '0'), '.') }}</td>
                    <td class="n">{{ rtrim(rtrim(number_format($l['counted'], 2), '0'), '.') }}</td>
                    <td class="n {{ $l['diff'] < 0 ? 'short' : ($l['diff'] > 0 ? 'over' : '') }}">{{ $l['diff'] == 0 ? '—' : ($l['diff'] > 0 ? '+' : '').rtrim(rtrim(number_format($l['diff'], 2), '0'), '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No stock on the van.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="grid">
        <table>
            <tr><th>Money</th><th class="n">Expected</th><th class="n">Handed in</th></tr>
            <tr><td>Cash</td><td class="n">{{ $money($s->expected_cash) }}</td><td class="n {{ $s->counted_cash < $s->expected_cash ? 'short' : '' }}">{{ $money($s->counted_cash) }}</td></tr>
            <tr><td>Cheques</td><td class="n">{{ $money($s->expected_cheques) }}</td><td class="n {{ $s->counted_cheques < $s->expected_cheques ? 'short' : '' }}">{{ $money($s->counted_cheques) }}</td></tr>
        </table>
        <table>
            <tr><th>Stock variance</th><th class="n">Value at cost</th></tr>
            <tr><td>Short</td><td class="n short">{{ $money($s->stock_short_value) }}</td></tr>
            <tr><td>Over</td><td class="n over">{{ $money($s->stock_over_value) }}</td></tr>
            @if($s->odometer_end)<tr><td>Odometer at return</td><td class="n">{{ number_format($s->odometer_end) }} km</td></tr>@endif
        </table>
    </div>

    @if($s->notes)<p><b>Notes:</b> {{ $s->notes }}</p>@endif

    <div class="sign">
        <div>Seller signature</div>
        <div>Store keeper / manager</div>
    </div>
</body>
</html>
