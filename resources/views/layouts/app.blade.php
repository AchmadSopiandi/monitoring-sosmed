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
            --border: #d5eeee;
            --text: #1f2937;
            --muted: #6b7280;
            --brand: #2563eb;
            --brand-dark: #1d4ed8;
            --button: #0d9488;
            --button-dark: #0f766e;
            --positive: #2563eb;
            --neutral: #16a34a;
            --negative: #b91c1c;
        }

        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); }
        a { color: var(--brand); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .shell { display: grid; grid-template-columns: 260px minmax(0, 1fr); min-height: 100vh; }
        .sidebar {
            background: #1f6f73;
            border-right: 1px solid #195c60;
            color: #ffffff;
            min-height: 100vh;
            position: sticky;
            top: 0;
            align-self: start;
        }
        .brand {
            align-items: center;
            background: rgba(0, 0, 0, .08);
            border-bottom: 1px solid rgba(255, 255, 255, .18);
            display: flex;
            font-size: 18px;
            font-weight: 800;
            gap: 10px;
            height: 56px;
            letter-spacing: .1px;
            margin: 0;
            padding: 0 20px;
            white-space: nowrap;
        }
        .brand-icon { color: #ffffff; font-size: 17px; }
        .brand-accent { color: #d9ffff; }
        .nav { display: flex; flex-direction: column; gap: 4px; padding: 14px 12px; min-height: calc(100vh - 56px); }
        .nav a {
            align-items: center;
            border-radius: 8px;
            color: #e8ffff;
            display: flex;
            font-size: 13px;
            gap: 12px;
            min-height: 40px;
            padding: 0 14px;
        }
        .nav a:hover {
            background: rgba(255, 255, 255, .14);
            color: #ffffff;
            text-decoration: none;
        }
        .nav a.active {
            background: rgba(9, 55, 59, .46);
            border-radius: 12px;
            box-shadow: none;
            color: #ffffff;
            overflow: hidden;
            position: relative;
            text-decoration: none;
        }
        .nav a.active::before {
            background: #7dd3fc;
            border-radius: 12px 0 0 12px;
            content: "";
            height: 100%;
            left: 0;
            position: absolute;
            top: 0;
            width: 6px;
        }
        .nav i { font-size: 15px; width: 15px; }
        .nav .icon-dashboard { color: #ffffff; }
        .nav .icon-instagram { color: #ff36aa; }
        .nav .icon-twitter { color: #36baff; }
        .nav .icon-comments { color: #2ee6a6; }
        .nav .icon-settings { color: #ffc928; }
        .nav a.active i { color: #ffffff; }
        .nav a.active .icon-instagram { color: #ff36aa; }
        .nav a.active .icon-twitter { color: #36baff; }
        .nav a.active .icon-comments { color: #2ee6a6; }
        .content { padding: 32px; }
        .page-header { align-items: center; display: flex; gap: 16px; justify-content: space-between; margin-bottom: 24px; }
        .page-header-actions { align-items: center; display: flex; gap: 8px; }
        h1 { font-size: 28px; margin: 0 0 6px; }
        .subtitle { color: var(--muted); margin: 0; }
        .grid { display: grid; gap: 16px; }
        .stats { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom: 24px; }
        .charts { grid-template-columns: minmax(260px, .8fr) minmax(360px, 1.2fr); margin-bottom: 24px; }
        .chart-line { margin-bottom: 24px; }
        .card, .panel { background: var(--panel); border: 1px solid var(--border); border-radius: 8px; padding: 20px; }
        .pagination { margin-top: 18px; }
        .pagination nav { align-items: center; display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; }
        .pagination nav > div:first-child { display: none; }
        .pagination nav > div:last-child > div:first-child { display: none; }
        .pagination nav > div { align-items: center; display: flex; gap: 4px; }
        .pagination a, .pagination span {
            align-items: center;
            border: 1px solid #d5dee9;
            border-radius: 6px;
            display: inline-flex;
            font-size: 13px;
            justify-content: center;
            line-height: 1;
            min-height: 38px;
            min-width: 38px;
            padding: 0 11px;
        }
        .pagination svg { display: block; height: 16px !important; width: 16px !important; }
        .pagination a:hover { background: #eff6ff; text-decoration: none; }
        .pagination span[aria-current="page"] { background: #2563eb; border-color: #2563eb; color: #ffffff; }
        .pagination span[aria-disabled="true"] { color: #98a2b3; }
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
        .badge.positif { background: #dbeafe; color: var(--positive); }
        .badge.netral { background: #dcfce7; color: var(--neutral); }
        .badge.negatif { background: #fee2e2; color: var(--negative); }
        .badge.source-instagram { background: #fce7f3; color: #db2777; }
        .badge.source-twitter { background: #dbeafe; color: #1d4ed8; }
        .button, button {
            align-items: center;
            appearance: none;
            background: var(--button);
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            gap: 8px;
            min-height: 42px;
            justify-content: center;
            line-height: 1.2;
            padding: 0 16px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }
        .button:focus-visible, button:focus-visible, input:focus, select:focus, textarea:focus {
            border-color: #5eead4;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, .16);
            outline: none;
        }
        .button:hover, button:hover { background: var(--button-dark); text-decoration: none; }
        .button.secondary { background: #e5e7eb; color: var(--text); }
        .button.secondary:hover { background: #dbe3ef; color: var(--text); }
        .button.excel {
            background: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
            color: #ffffff;
        }
        .button.excel:hover {
            background: linear-gradient(135deg, #16a34a 0%, #166534 100%);
            box-shadow: 0 8px 18px rgba(34, 197, 94, .22);
            color: #ffffff;
        }
        .button.pdf-export { background: #dc2626; color: #ffffff; }
        .button.pdf-export:hover { background: #b91c1c; color: #ffffff; }
        button.danger { background: var(--negative); }
        .actions { align-items: center; display: flex; flex-wrap: wrap; gap: 8px; }
        .actions form { display: inline-flex; margin: 0; }
        .logout-sidebar-form { margin-top: auto; }
        .logout-link {
            align-items: center;
            background: #dc2626;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            display: flex;
            font: inherit;
            gap: 12px;
            justify-content: flex-start;
            min-height: 40px;
            padding: 0 14px;
            width: 100%;
        }
        .logout-link:hover {
            background: #b91c1c;
            text-decoration: none;
        }
        .form, .filters { display: grid; gap: 14px; }
        .filters { align-items: end; grid-template-columns: repeat(5, minmax(140px, 1fr)); margin-bottom: 18px; }
        .filters .wide { grid-column: span 2; }
        .filters .actions { align-self: end; min-height: 42px; }
        label { color: #111827; display: grid; font-size: 14px; font-weight: 700; gap: 8px; }
        input, select, textarea {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font: inherit;
            min-height: 42px;
            padding: 0 12px;
            width: 100%;
        }
        select { cursor: pointer; }
        textarea { min-height: 130px; padding: 11px 12px; resize: vertical; }
        .notification-stack { align-items: center; background: rgba(15, 23, 42, .18); display: flex; inset: 0; justify-content: center; padding: 16px; position: fixed; z-index: 1000; }
        .notification { align-items: center; animation: notification-in .35s ease-out both; border: 1px solid transparent; border-radius: 14px; box-shadow: 0 18px 45px rgba(31, 41, 55, .2); display: flex; flex-direction: column; gap: 12px; max-width: 515px; min-height: 270px; padding: 24px; position: relative; text-align: center; width: 100%; }
        .notification.is-leaving { animation: notification-out .25s ease-in forwards; }
        .notification > i { align-items: center; background: #d1fad0; border-radius: 50%; color: #58ed38; display: flex; font-size: 48px; height: 124px; justify-content: center; margin-top: -8px; width: 124px; }
        .notification.error > i { background: #fee2e2; color: #ef4444; }
        .notification.warning > i { background: #fef3c7; color: #f59e0b; }
        .notification.info > i { background: #dbeafe; color: #3b82f6; }
        .notification-content { line-height: 1.45; }
        .notification.success .notification-content { color: #202020; font-size: 22px; font-weight: 500; text-transform: uppercase; }
        .notification.success .notification-content::after { color: #999; content: "Selamat datang di sistem"; display: block; font-size: 14px; margin-top: 8px; text-transform: none; }
        .notification-close { background: transparent; border: 0; color: currentColor; cursor: pointer; font-size: 22px; line-height: 1; min-height: auto; opacity: .45; padding: 0; position: absolute; right: 14px; top: 10px; }
        .notification-close:hover { background: transparent; opacity: 1; }
        .notification.success { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
        .notification.error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        .notification.warning { background: #fffbeb; border-color: #fde68a; color: #92400e; }
        .notification.info { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
        @keyframes notification-in { from { opacity: 0; transform: scale(.88) translateY(14px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes notification-out { from { opacity: 1; transform: scale(1); } to { opacity: 0; transform: scale(.92); } }
        @media (prefers-reduced-motion: reduce) { .notification { animation: none; } }
        .empty { color: var(--muted); padding: 24px 12px; text-align: center; }
        .pagination { margin-top: 18px; }
        .muted { color: var(--muted); }
        .stack { display: grid; gap: 16px; }
        .report-actions { align-items: center; display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; margin-bottom: 16px; }
        td .actions { flex-wrap: nowrap; }
        td .button, td button { min-height: 36px; padding: 0 12px; }
        canvas { max-height: 340px; width: 100% !important; }

        @media (max-width: 800px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { min-height: auto; position: static; }
            .brand { height: 56px; }
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
            <div class="brand">
                <i class="bi bi-pie-chart-fill brand-icon"></i>
                <span>SIMAK<span class="brand-accent">-BAPENDA</span></span>
            </div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-house-fill icon-dashboard"></i> Dashboard</a>
                <a href="{{ route('instagram.posts.index') }}" class="{{ request()->routeIs('instagram.posts.*') ? 'active' : '' }}"><i class="bi bi-instagram icon-instagram"></i> Instagram</a>
                <a href="{{ route('twitter.tweets.index') }}" class="{{ request()->routeIs('twitter.tweets.*') ? 'active' : '' }}"><i class="bi bi-twitter icon-twitter"></i> Twitter</a>
                <a href="{{ route('comments.index') }}" class="{{ request()->routeIs('comments.*') ? 'active' : '' }}"><i class="bi bi-chat-dots-fill icon-comments"></i> Comments</a>
                <div class="logout-sidebar-form">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link logout-link"><i class="bi bi-box-arrow-right"></i> Logout</button>
                    </form>
                </div>
            </nav>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>
    @if (session('success') || session('error') || session('warning') || session('info') || session('status') || $errors->any())
    <div class="notification-stack" aria-live="polite" aria-atomic="true">
        @foreach (['success' => 'check-circle-fill', 'error' => 'x-circle-fill', 'warning' => 'exclamation-triangle-fill', 'info' => 'info-circle-fill', 'status' => 'info-circle-fill'] as $type => $icon)
            @if (session($type))
                <div class="notification {{ $type === 'status' ? 'info' : $type }}" role="{{ $type === 'error' ? 'alert' : 'status' }}">
                    <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
                    <span class="notification-content">{{ session($type) }}</span>
                    <button type="button" class="notification-close" aria-label="Tutup notifikasi">&times;</button>
                </div>
            @endif
        @endforeach
        @if ($errors->any())
            <div class="notification error" role="alert">
                <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
                <span class="notification-content">{{ $errors->first() }}</span>
                <button type="button" class="notification-close" aria-label="Tutup notifikasi">&times;</button>
            </div>
        @endif
    </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.notification').forEach((notification) => {
            const dismiss = () => {
                notification.classList.add('is-leaving');
                notification.addEventListener('animationend', () => notification.remove(), { once: true });
            };

            notification.querySelector('.notification-close').addEventListener('click', dismiss);
            window.setTimeout(dismiss, 4500);
        });
    </script>
    @yield('scripts')
</body>
</html>
