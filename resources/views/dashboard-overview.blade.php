@extends('layouts.app')

@section('title', 'Dashboard Social Monitoring')

@section('content')
    <style>
        .dashboard-page {
            display: grid;
            gap: 20px;
        }

        .dashboard-hero {
            align-items: center;
            background: #1f6f73;
            border: 1px solid #195c60;
            border-radius: 14px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .06);
            display: flex;
            gap: 18px;
            justify-content: space-between;
            padding: 22px 24px;
        }

        .dashboard-hero h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: .1px;
            margin-bottom: 6px;
        }

        .dashboard-hero .subtitle { color: #e0ffff; }

        .hero-actions {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .period-filter {
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 10px;
            display: inline-flex;
            gap: 4px;
            padding: 4px;
        }

        .period-filter a {
            align-items: center;
            border-radius: 7px;
            color: #e8ffff;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            min-height: 32px;
            padding: 0 12px;
        }

        .period-filter a:hover {
            background: rgba(255, 255, 255, .18);
            color: #ffffff;
            text-decoration: none;
        }

        .period-filter a.active {
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(12, 78, 81, .18);
            color: #195c60;
        }

        .logout-form {
            display: inline-flex;
            margin: 0;
        }

        .logout-button {
            background: #ef4444;
        }

        .logout-button:hover {
            background: #dc2626;
        }

        .metric-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .metric-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
            min-height: 116px;
            overflow: hidden;
            padding: 18px;
            position: relative;
        }

        .metric-card::before {
            background: var(--metric-color, #0f94d6);
            content: "";
            height: 100%;
            left: 0;
            position: absolute;
            top: 0;
            width: 4px;
        }

        .metric-top {
            align-items: flex-start;
            display: flex;
            gap: 12px;
            justify-content: space-between;
        }

        .metric-label {
            color: #667085;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .metric-value {
            color: #0f172a;
            font-size: 31px;
            font-weight: 850;
            line-height: 1.1;
            margin-top: 8px;
        }

        .metric-icon {
            align-items: center;
            background: color-mix(in srgb, var(--metric-color, #0f94d6) 12%, white);
            border-radius: 10px;
            color: var(--metric-color, #0f94d6);
            display: inline-flex;
            height: 36px;
            justify-content: center;
            width: 36px;
        }

        .chart-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .chart-grid.bottom {
            grid-template-columns: minmax(280px, .85fr) minmax(360px, 1.15fr);
        }

        .dashboard-panel {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
            padding: 18px;
        }

        .panel-title {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .panel-title h2 {
            color: #182235;
            font-size: 20px;
            font-weight: 800;
            margin: 0;
        }

        .panel-pill {
            background: #f1f6fb;
            border-radius: 999px;
            color: #667085;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 10px;
        }

        .chart-box {
            height: 260px;
            position: relative;
        }

        .chart-box.tall {
            height: 310px;
        }

        .sentiment-legend {
            border-top: 1px solid #edf2f7;
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 14px;
            padding-top: 14px;
        }

        .legend-item {
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px;
        }

        .legend-label {
            color: #667085;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .legend-value {
            font-size: 18px;
            font-weight: 800;
        }

        @media (max-width: 1100px) {
            .metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .chart-grid, .chart-grid.bottom { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .dashboard-hero { align-items: flex-start; flex-direction: column; padding: 18px; }
            .hero-actions { justify-content: flex-start; }
            .period-filter { width: 100%; }
            .period-filter a { flex: 1; justify-content: center; padding: 0 8px; }
            .metric-grid { grid-template-columns: 1fr; }
            .sentiment-legend { grid-template-columns: 1fr; }
        }
    </style>

    <div class="dashboard-page">
    <div class="dashboard-hero">
        <div>
            <h1>Dashboard Social Monitoring</h1>
            <p class="subtitle">Overview monitoring Instagram dan Twitter/X Bapenda Kota Bandung.</p>
        </div>
        <div class="hero-actions">
            <div class="period-filter" aria-label="Filter periode dashboard">
                <a href="{{ route('dashboard', ['period' => 'weekly']) }}" class="{{ $period === 'weekly' ? 'active' : '' }}">Mingguan</a>
                <a href="{{ route('dashboard', ['period' => 'monthly']) }}" class="{{ $period === 'monthly' ? 'active' : '' }}">Bulanan</a>
                <a href="{{ route('dashboard', ['period' => 'yearly']) }}" class="{{ $period === 'yearly' ? 'active' : '' }}">Tahunan</a>
            </div>
        </div>
    </div>

    <div class="metric-grid">
        <div class="metric-card" style="--metric-color: #e83e8c;">
            <div class="metric-top">
                <div>
                    <div class="metric-label">Komentar Instagram</div>
                    <div class="metric-value">{{ $instagramTotal }}</div>
                </div>
                <span class="metric-icon"><i class="bi bi-instagram"></i></span>
            </div>
        </div>
        <div class="metric-card" style="--metric-color: #1da1f2;">
            <div class="metric-top">
                <div>
                    <div class="metric-label">Komentar Twitter</div>
                    <div class="metric-value">{{ $twitterTotal }}</div>
                </div>
                <span class="metric-icon"><i class="bi bi-twitter"></i></span>
            </div>
        </div>
        <div class="metric-card" style="--metric-color: #16a34a;">
            <div class="metric-top">
                <div>
                    <div class="metric-label">Total Positif</div>
                    <div class="metric-value positive">{{ $positiveCount }}</div>
                </div>
                <span class="metric-icon"><i class="bi bi-emoji-smile-fill"></i></span>
            </div>
        </div>
        <div class="metric-card" style="--metric-color: #d97706;">
            <div class="metric-top">
                <div>
                    <div class="metric-label">Total Netral</div>
                    <div class="metric-value neutral">{{ $neutralCount }}</div>
                </div>
                <span class="metric-icon"><i class="bi bi-dash-circle-fill"></i></span>
            </div>
        </div>
        <div class="metric-card" style="--metric-color: #dc2626;">
            <div class="metric-top">
                <div>
                    <div class="metric-label">Total Negatif</div>
                    <div class="metric-value negative">{{ $negativeCount }}</div>
                </div>
                <span class="metric-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
            </div>
        </div>
    </div>

    <div class="chart-grid">
        <section class="dashboard-panel">
            <div class="panel-title">
                <h2>Sentimen Instagram</h2>
                <span class="panel-pill">{{ $instagramTotal }} komentar</span>
            </div>
            <div class="chart-box"><canvas id="instagramPie"></canvas></div>
            <div class="sentiment-legend" id="instagramPieLegend"></div>
        </section>
        <section class="dashboard-panel">
            <div class="panel-title">
                <h2>Sentimen Twitter/X</h2>
                <span class="panel-pill">{{ $twitterTotal }} komentar</span>
            </div>
            <div class="chart-box"><canvas id="twitterPie"></canvas></div>
            <div class="sentiment-legend" id="twitterPieLegend"></div>
        </section>
    </div>

    <div class="chart-grid bottom">
        <section class="dashboard-panel">
            <div class="panel-title">
                <h2>Instagram vs Twitter/X</h2>
                <span class="panel-pill">Per platform</span>
            </div>
            <div class="chart-box tall"><canvas id="comparisonBar"></canvas></div>
        </section>
        <section class="dashboard-panel">
            <div class="panel-title">
                <h2>Perkembangan Komentar</h2>
                <span class="panel-pill">Harian</span>
            </div>
            <div class="chart-box tall"><canvas id="dailyLine"></canvas></div>
        </section>
    </div>
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
        const emptyColor = '#e5eaf2';

        const hasData = (values) => values.some((value) => Number(value) > 0);
        const doughnutData = (chart) => hasData(chart.data)
            ? { labels: chart.labels, data: chart.data, colors: sentimentColors }
            : { labels: ['Belum ada data'], data: [1], colors: [emptyColor] };

        const makeDoughnut = (canvasId, chart) => {
            const data = doughnutData(chart);
            new Chart(document.getElementById(canvasId), {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: data.colors,
                        borderColor: '#ffffff',
                        borderWidth: 4,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    cutout: '68%',
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { boxWidth: 14, color: '#475467', font: { size: 11, weight: '600' } }
                        }
                    }
                }
            });
        };

        makeDoughnut('instagramPie', instagramPie);
        makeDoughnut('twitterPie', twitterPie);

        const renderSentimentLegend = (targetId, chart) => {
            document.getElementById(targetId).innerHTML = chart.labels.map((label, index) => `
                <div class="legend-item">
                    <div class="legend-label">${label}</div>
                    <div class="legend-value" style="color: ${sentimentColors[index]};">${chart.data[index] ?? 0}</div>
                </div>
            `).join('');
        };
        renderSentimentLegend('instagramPieLegend', instagramPie);
        renderSentimentLegend('twitterPieLegend', twitterPie);

        new Chart(document.getElementById('comparisonBar'), {
            type: 'bar',
            data: {
                labels: comparisonChart.labels,
                datasets: [{
                    label: 'Komentar',
                    data: comparisonChart.data,
                    backgroundColor: ['#e83e8c', '#1da1f2'],
                    borderRadius: 10,
                    maxBarThickness: 64,
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#475467', font: { weight: '700' } } },
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#667085' }, grid: { color: '#edf2f7' } }
                }
            }
        });

        new Chart(document.getElementById('dailyLine'), {
            type: 'line',
            data: {
                labels: lineChart.labels,
                datasets: [
                    { label: 'Instagram', data: lineChart.instagram, borderColor: '#e83e8c', backgroundColor: 'rgba(232, 62, 140, .12)', fill: true, tension: .35, pointRadius: 3 },
                    { label: 'Twitter/X', data: lineChart.twitter, borderColor: '#1da1f2', backgroundColor: 'rgba(29, 161, 242, .10)', fill: true, tension: .35, pointRadius: 3 }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 14, color: '#475467', font: { size: 11, weight: '600' } }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#667085' } },
                    y: { beginAtZero: true, ticks: { precision: 0, color: '#667085' }, grid: { color: '#edf2f7' } }
                }
            }
        });
    </script>
@endsection
