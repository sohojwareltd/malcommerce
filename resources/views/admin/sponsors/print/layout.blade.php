<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 16px;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }
        h1 {
            margin: 0 0 4px 0;
            font-size: 18px;
            font-weight: 700;
        }
        .meta {
            margin: 0 0 12px 0;
            color: #000;
        }
        .meta p { margin: 2px 0; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        th {
            font-weight: 700;
            text-align: left;
            background: #fff;
        }
        td.num, th.num { text-align: right; }
        .tabular-nums { font-variant-numeric: tabular-nums; }
        img.photo {
            display: block;
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 1px solid #000;
        }
        .muted { color: #333; }
        .toolbar {
            margin-bottom: 16px;
        }
        .toolbar button {
            padding: 6px 12px;
            font-size: 12px;
            border: 1px solid #000;
            background: #fff;
            cursor: pointer;
        }
        .toolbar button:hover { background: #eee; }
        @media print {
            body { padding: 0; }
            .toolbar { display: none; }
        }
    </style>
    @stack('print-head')
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print</button>
    </div>
    @yield('content')
</body>
</html>
