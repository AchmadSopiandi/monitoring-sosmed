@extends('layouts.app')

@section('title', 'Dashboard Social Monitoring')

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard Social Monitoring</h1>
            <p class="subtitle">Ringkasan komentar dan sentimen media sosial Bapenda Kota Bandung.</p>
        </div>
        <a class="button" href="{{ route('instagram.posts.index') }}">Kelola Postingan</a>
    </div>

    <div class="grid stats">
        <div class="card stat-card">
            <div class="stat-label">Jumlah Komentar</div>
            <div class="stat-value">{{ $totalCount }}</div>
        </div>
        <div class="card stat-card">
            <div class="stat-label">Komentar Positif</div>
            <div class="stat-value positive">{{ $positiveCount }}</div>
        </div>
        <div class="card stat-card">
            <div class="stat-label">Komentar Netral</div>
            <div class="stat-value neutral">{{ $neutralCount }}</div>
        </div>
        <div class="card stat-card">
            <div class="stat-label">Komentar Negatif</div>
            <div class="stat-value negative">{{ $negativeCount }}</div>
        </div>
    </div>

    <div class="grid charts">
        <section class="panel">
            <h1>Sentimen</h1>
            <canvas id="sentimentPie"></canvas>
        </section>

        <section class="panel">
            <h1>Komentar per Postingan</h1>
            <canvas id="postBar"></canvas>
        </section>
    </div>

    <section class="panel chart-line">
        <h1>Komentar Harian</h1>
        <canvas id="dailyLine"></canvas>
    </section>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const pieChart = @json($pieChart);
        const barChart = @json($barChart);
        const lineChart = @json($lineChart);

        new Chart(document.getElementById('sentimentPie'), {
            type: 'pie',
            data: {
                labels: pieChart.labels,
                datasets: [{ data: pieChart.data, backgroundColor: ['#2563eb', '#16a34a', '#dc2626'] }]
            }
        });

        new Chart(document.getElementById('postBar'), {
            type: 'bar',
            data: {
                labels: barChart.labels,
                datasets: [{ label: 'Komentar', data: barChart.data, backgroundColor: '#2563eb' }]
            },
            options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        new Chart(document.getElementById('dailyLine'), {
            type: 'line',
            data: {
                labels: lineChart.labels,
                datasets: [{ label: 'Komentar', data: lineChart.data, borderColor: '#2563eb', tension: .25 }]
            },
            options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    </script>
@endsection
{{--
<html>

<head>
<title>Monitoring Sentimen Instagram</title>
</head>

<body>

<h1>Dashboard Monitoring Sentimen</h1>

<p>Proyek Magang - Bapenda Kota Bandung</p>

<hr>

<p>😊 Positif : 0</p>
<p>😐 Netral : 0</p>
<p>😠 Negatif : 0</p>

</body>

</html>
--}}
