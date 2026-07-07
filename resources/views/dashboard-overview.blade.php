@extends('layouts.app')

@section('title', 'Dashboard Social Monitoring')

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard Social Monitoring</h1>
            <p class="subtitle">Overview monitoring Instagram dan Twitter/X Bapenda Kota Bandung.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Total Komentar Instagram</div><div class="stat-value">{{ $instagramTotal }}</div></div></div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Total Komentar Twitter</div><div class="stat-value">{{ $twitterTotal }}</div></div></div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Total Positif</div><div class="stat-value positive">{{ $positiveCount }}</div></div></div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Total Netral</div><div class="stat-value neutral">{{ $neutralCount }}</div></div></div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0 h-100"><div class="card-body"><div class="text-muted small">Total Negatif</div><div class="stat-value negative">{{ $negativeCount }}</div></div></div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <section class="panel shadow-sm">
                <h1>Sentimen Instagram</h1>
                <canvas id="instagramPie"></canvas>
                <div class="row g-2 mt-3" id="instagramPieLegend"></div>
            </section>
        </div>
        <div class="col-lg-6">
            <section class="panel shadow-sm">
                <h1>Sentimen Twitter/X</h1>
                <canvas id="twitterPie"></canvas>
                <div class="row g-2 mt-3" id="twitterPieLegend"></div>
            </section>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5"><section class="panel shadow-sm"><h1>Instagram vs Twitter/X</h1><canvas id="comparisonBar"></canvas></section></div>
        <div class="col-lg-7"><section class="panel shadow-sm"><h1>Perkembangan Komentar</h1><canvas id="dailyLine"></canvas></section></div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const instagramPie = @json($instagramPieChart);
        const twitterPie = @json($twitterPieChart);
        const comparisonChart = @json($comparisonChart);
        const lineChart = @json($lineChart);
        const sentimentColors = ['#2563eb', '#16a34a', '#dc2626'];

        new Chart(document.getElementById('instagramPie'), { type: 'pie', data: { labels: instagramPie.labels, datasets: [{ data: instagramPie.data, backgroundColor: sentimentColors }] } });
        new Chart(document.getElementById('twitterPie'), { type: 'pie', data: { labels: twitterPie.labels, datasets: [{ data: twitterPie.data, backgroundColor: sentimentColors }] } });
        const renderSentimentLegend = (targetId, chart) => {
            document.getElementById(targetId).innerHTML = chart.labels.map((label, index) => `
                <div class="col">
                    <div class="small text-muted">${label}</div>
                    <div class="fw-bold" style="color: ${sentimentColors[index]};">${chart.data[index] ?? 0}</div>
                </div>
            `).join('');
        };
        renderSentimentLegend('instagramPieLegend', instagramPie);
        renderSentimentLegend('twitterPieLegend', twitterPie);
        new Chart(document.getElementById('comparisonBar'), {
            type: 'bar',
            data: { labels: comparisonChart.labels, datasets: [{ label: 'Komentar', data: comparisonChart.data, backgroundColor: ['#2563eb', '#0f172a'] }] },
            options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
        new Chart(document.getElementById('dailyLine'), {
            type: 'line',
            data: {
                labels: lineChart.labels,
                datasets: [
                    { label: 'Instagram', data: lineChart.instagram, borderColor: '#2563eb', tension: .25 },
                    { label: 'Twitter/X', data: lineChart.twitter, borderColor: '#0f172a', tension: .25 }
                ]
            },
            options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    </script>
@endsection
