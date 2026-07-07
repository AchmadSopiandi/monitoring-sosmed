<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Social Monitoring')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, Helvetica, sans-serif;
            --bg: #f6f7fb;
            --panel: #ffffff;
            --border: #d9dee8;
            --text: #1f2937;
            --muted: #6b7280;
            --brand: #2563eb;
            --brand-dark: #1d4ed8;
            --positive: #15803d;
            --neutral: #a16207;
            --negative: #b91c1c;
        }

        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); }
        a { color: var(--brand); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .shell { display: grid; grid-template-columns: 240px minmax(0, 1fr); min-height: 100vh; }
        .sidebar { background: #111827; color: #f9fafb; padding: 24px; }
        .brand { font-size: 20px; font-weight: 700; margin-bottom: 28px; }
        .nav { display: grid; gap: 8px; }
        .nav a { border-radius: 8px; color: #d1d5db; padding: 10px 12px; }
        .nav a.active, .nav a:hover { background: #1f2937; color: #ffffff; text-decoration: none; }
        .content { padding: 32px; }
        .page-header { align-items: center; display: flex; gap: 16px; justify-content: space-between; margin-bottom: 24px; }
        h1 { font-size: 28px; margin: 0 0 6px; }
        .subtitle { color: var(--muted); margin: 0; }
        .grid { display: grid; gap: 16px; }
        .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom: 24px; }
        .charts { grid-template-columns: minmax(260px, .8fr) minmax(360px, 1.2fr); margin-bottom: 24px; }
        .chart-line { margin-bottom: 24px; }
        .card, .panel { background: var(--panel); border: 1px solid var(--border); border-radius: 8px; padding: 20px; }
        .stat-card { color: inherit; display: block; transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease; }
        .stat-card:hover { border-color: #b8c2d6; box-shadow: 0 8px 22px rgba(31, 41, 55, .08); text-decoration: none; transform: translateY(-1px); }
        .stat-label { color: var(--muted); font-size: 14px; margin-bottom: 10px; }
        .stat-value { font-size: 34px; font-weight: 700; }
        .positive { color: var(--positive); }
        .neutral { color: var(--neutral); }
        .negative { color: var(--negative); }
        .table-wrap { overflow-x: auto; }
        table { border-collapse: collapse; min-width: 760px; width: 100%; }
        th, td { border-bottom: 1px solid var(--border); padding: 14px 12px; text-align: left; vertical-align: top; }
        th { color: var(--muted); font-size: 13px; text-transform: uppercase; }
        .badge { border-radius: 999px; display: inline-block; font-size: 12px; font-weight: 700; padding: 5px 10px; }
        .badge.positif { background: #dcfce7; color: var(--positive); }
        .badge.netral { background: #fef3c7; color: var(--neutral); }
        .badge.negatif { background: #fee2e2; color: var(--negative); }
        .button, button {
            background: var(--brand);
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            font: inherit;
            font-weight: 700;
            justify-content: center;
            padding: 10px 14px;
        }
        .button:hover, button:hover { background: var(--brand-dark); text-decoration: none; }
        .button.secondary { background: #e5e7eb; color: var(--text); }
        .button.secondary:hover { background: #dbe3ef; color: var(--text); }
        button.danger { background: var(--negative); }
        .actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .form, .filters { display: grid; gap: 14px; }
        .filters { grid-template-columns: repeat(5, minmax(140px, 1fr)); margin-bottom: 18px; }
        .filters .wide { grid-column: span 2; }
        label { display: grid; font-weight: 700; gap: 8px; }
        input, select, textarea { border: 1px solid var(--border); border-radius: 8px; font: inherit; padding: 11px 12px; width: 100%; }
        textarea { min-height: 130px; resize: vertical; }
        .error, .alert { border-radius: 8px; margin-bottom: 16px; padding: 12px 14px; }
        .error { background: #fee2e2; color: #991b1b; }
        .alert { background: #dcfce7; color: #166534; }
        .empty { color: var(--muted); padding: 24px 12px; text-align: center; }
        .pagination { margin-top: 18px; }
        .muted { color: var(--muted); }
        .stack { display: grid; gap: 16px; }
        .report-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; margin-bottom: 16px; }
        canvas { max-height: 340px; width: 100% !important; }

        @media (max-width: 800px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { padding: 18px; }
            .nav { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .content { padding: 20px; }
            .page-header { align-items: flex-start; flex-direction: column; }
            .stats, .charts, .filters { grid-template-columns: 1fr; }
            .filters .wide { grid-column: auto; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Social Monitoring</div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i> Dashboard</a>
                <a href="{{ route('instagram.posts.index') }}" class="{{ request()->routeIs('instagram.posts.*') ? 'active' : '' }}"><i class="bi bi-camera"></i> Postingan Instagram</a>
                <a href="{{ route('twitter.tweets.index') }}" class="{{ request()->routeIs('twitter.tweets.*') ? 'active' : '' }}"><i class="bi bi-twitter-x"></i> Postingan Twitter/X</a>
                <a href="{{ route('settings.api.index') }}" class="{{ request()->routeIs('settings.api.*') ? 'active' : '' }}"><i class="bi bi-gear"></i> Pengaturan API</a>
            </nav>
        </aside>

        <main class="content">
            @if (session('success'))
                <div class="alert">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
