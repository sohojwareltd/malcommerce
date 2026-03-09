<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sales Report ({{ $dateFrom }} to {{ $dateTo }})</title>
    <style>
        :root { color-scheme: light; }
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; margin: 20px; color: #111827; }
        h1 { margin: 0 0 6px; font-size: 22px; }
        .sub { margin: 0 0 14px; color: #4b5563; font-size: 12px; }
        .meta { font-size: 12px; color: #374151; margin: 0 0 14px; }
        .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin: 14px 0 18px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; }
        .label { font-size: 11px; color: #6b7280; }
        .value { font-size: 16px; font-weight: 700; margin-top: 6px; }
        .value.green { color: #047857; }
        .value.red { color: #b91c1c; }
        .value.amber { color: #b45309; }
        .section { margin-top: 18px; }
        .section h2 { font-size: 14px; margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f9fafb; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: .02em; color: #4b5563; }
        td.r, th.r { text-align: right; }
        .muted { color: #6b7280; }
        .footer { margin-top: 16px; font-size: 11px; color: #6b7280; }
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .section { break-inside: avoid; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 12px;">
        <button type="button" onclick="window.print()" style="padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;background:#111827;color:#fff;cursor:pointer;">Print</button>
        <button type="button" onclick="window.close()" style="padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;color:#111827;cursor:pointer;margin-left:6px;">Close</button>
    </div>

    <h1>Sales Report</h1>
    <p class="sub">Date range: <strong>{{ $dateFrom }}</strong> to <strong>{{ $dateTo }}</strong></p>
    <p class="meta">
        Generated: {{ now()->format('Y-m-d H:i') }}
        @if(!empty($filters['status']) || !empty($filters['product_id']) || !empty($filters['product_type']))
            <span class="muted">|</span>
            Filters:
            @if(!empty($filters['status'])) Status={{ $filters['status'] }} @endif
            @if(!empty($filters['product_type'])) Type={{ $filters['product_type'] }} @endif
            @if(!empty($filters['product_id'])) Product ID={{ $filters['product_id'] }} @endif
        @endif
    </p>

    <div class="grid">
        <div class="card">
            <div class="label">Total Orders</div>
            <div class="value">{{ $stats['total_orders'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="label">Revenue (Delivered)</div>
            <div class="value green">৳{{ number_format($stats['revenue'] ?? 0, 2) }}</div>
        </div>
        <div class="card">
            <div class="label">Pending Revenue</div>
            <div class="value amber">৳{{ number_format($stats['pending_revenue'] ?? 0, 2) }}</div>
        </div>
        <div class="card">
            <div class="label">Avg Order (Delivered)</div>
            <div class="value">৳{{ number_format($stats['average_order_value'] ?? 0, 2) }}</div>
        </div>
        <div class="card">
            <div class="label">Items Sold</div>
            <div class="value">{{ $stats['total_items_sold'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="label">Expenses</div>
            <div class="value red">৳{{ number_format($stats['total_expenses'] ?? 0, 2) }}</div>
        </div>
        <div class="card" style="grid-column: span 3;">
            <div class="label">Profit (Revenue − Expenses)</div>
            @php($profit = $stats['profit'] ?? 0)
            <div class="value {{ $profit >= 0 ? 'green' : 'red' }}">৳{{ number_format($profit, 2) }}</div>
        </div>
    </div>

    <div class="section">
        <h2>By Product Type</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th class="r">Orders</th>
                    <th class="r">Qty</th>
                    <th class="r">Revenue</th>
                    <th class="r">Pending</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($stats['by_product_type'] ?? []) as $type => $data)
                    <tr>
                        <td>{{ $data['name'] ?? ucfirst($type) }}</td>
                        <td class="r">{{ $data['count'] ?? 0 }}</td>
                        <td class="r">{{ $data['quantity'] ?? 0 }}</td>
                        <td class="r">৳{{ number_format($data['revenue'] ?? 0, 2) }}</td>
                        <td class="r">৳{{ number_format($data['pending_revenue'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>By Product</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="r">Orders</th>
                    <th class="r">Qty</th>
                    <th class="r">Revenue</th>
                    <th class="r">Pending</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($stats['by_product'] ?? collect()) as $row)
                    <tr>
                        <td>{{ $row['name'] ?? 'Unknown' }}</td>
                        <td class="r">{{ $row['count'] ?? 0 }}</td>
                        <td class="r">{{ $row['quantity'] ?? 0 }}</td>
                        <td class="r">৳{{ number_format($row['revenue'] ?? 0, 2) }}</td>
                        <td class="r">৳{{ number_format($row['pending_revenue'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Orders by Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="r">Count</th>
                    <th class="r">Revenue</th>
                    <th class="r">Pending</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($stats['by_status'] ?? []) as $status => $data)
                    <tr>
                        <td>{{ ucfirst($status) }}</td>
                        <td class="r">{{ $data['count'] ?? 0 }}</td>
                        <td class="r">৳{{ number_format($data['revenue'] ?? 0, 2) }}</td>
                        <td class="r">৳{{ number_format($data['pending_revenue'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Customer</th>
                    <th class="r">Qty</th>
                    <th class="r">Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $o)
                    <tr>
                        <td>{{ $o->order_number }}</td>
                        <td>{{ $o->created_at->format('Y-m-d') }}</td>
                        <td>{{ $o->product?->name ?? '—' }}</td>
                        <td>{{ ($o->product && $o->product->is_digital) ? 'Digital' : 'Physical' }}</td>
                        <td>{{ $o->customer_name }}</td>
                        <td class="r">{{ $o->quantity }}</td>
                        <td class="r">৳{{ number_format($o->total_price, 2) }}</td>
                        <td>{{ ucfirst($o->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Note: Revenue is calculated from delivered orders only; pending revenue includes pending/processing/shipped.
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 200);
        });
    </script>
</body>
</html>
