<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Twitter/X Social Monitoring</title>
    <style>
        body { color: #111827; font-family: Arial, Helvetica, sans-serif; margin: 32px; }
        .header { align-items: center; border-bottom: 2px solid #111827; display: flex; gap: 16px; padding-bottom: 16px; }
        .logo { align-items: center; border: 2px solid #111827; border-radius: 8px; display: grid; font-weight: 700; height: 72px; justify-items: center; width: 72px; }
        .summary { display: grid; gap: 12px; grid-template-columns: repeat(4, 1fr); margin-top: 24px; }
        .card { border: 1px solid #d1d5db; border-radius: 8px; padding: 14px; }
        .value { font-size: 24px; font-weight: 700; margin-top: 6px; }
        table { border-collapse: collapse; margin-top: 12px; width: 100%; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
        canvas { max-height: 280px; max-width: 520px; }
        .actions { margin-bottom: 16px; text-align: right; }
        button { background: #2563eb; border: 0; border-radius: 8px; color: #fff; cursor: pointer; font: inherit; font-weight: 700; padding: 10px 14px; }
        @media print { .actions { display: none; } }
    </style>
</head>
<body>
    <div class="actions"><button onclick="window.print()">Print / Save PDF</button></div>
    <header class="header">
        <div class="logo">BAPENDA</div>
        <div>
            <h1>Laporan Twitter/X Social Monitoring</h1>
            <p>Tanggal cetak: {{ now()->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</p>
        </div>
    </header>
    <section class="summary">
        <div class="card"><div>Jumlah Reply</div><div class="value">{{ $totalCount }}</div></div>
        <div class="card"><div>Positif</div><div class="value">{{ $positiveCount }}</div></div>
        <div class="card"><div>Netral</div><div class="value">{{ $neutralCount }}</div></div>
        <div class="card"><div>Negatif</div><div class="value">{{ $negativeCount }}</div></div>
    </section>
    <h2>Grafik Sentimen</h2>
    <canvas id="sentimentPie"></canvas>
    <h2>Daftar Reply</h2>
    <table>
        <thead><tr><th>Tanggal</th><th>Tweet</th><th>Username</th><th>Reply</th><th>Like</th><th>Sentimen</th></tr></thead>
        <tbody>
            @forelse ($comments as $comment)
                <tr>
                    <td>{{ optional($comment->commented_at)->timezone(config('app.timezone'))->format('d M Y H:i') }}</td>
                    <td>{{ str($comment->tweet?->text)->limit(120) }}</td>
                    <td>{{ $comment->username }}</td>
                    <td>{{ $comment->reply ?: $comment->comment }}</td>
                    <td>{{ $comment->like_count ?? '-' }}</td>
                    <td>{{ $comment->getRawOriginal('sentiment') ?: 'Netral' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada reply untuk filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const pieChart = @json($pieChart);
        new Chart(document.getElementById('sentimentPie'), {
            type: 'pie',
            data: { labels: pieChart.labels, datasets: [{ data: pieChart.data, backgroundColor: ['#2563eb', '#16a34a', '#dc2626'] }] }
        });
    </script>
</body>
</html>
