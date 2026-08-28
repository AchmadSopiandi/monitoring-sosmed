@extends('layouts.app')

@section('title', 'Detail Tweet Twitter/X')

@section('content')
    <style>
        .detail-filters { grid-template-columns: repeat(4, minmax(150px, 1fr)); }
        .detail-filters .wide { grid-column: span 2; }
        .detail-filters .actions { align-items: center; grid-column: span 2; justify-content: space-between; }
        .detail-filters .filter-actions, .detail-filters .export-actions { align-items: center; display: flex; flex-wrap: wrap; gap: 8px; }
        .detail-filters .actions .button, .detail-filters .actions button { min-height: 40px; }
        .detail-chart-panel { height: 440px; }
        .detail-chart-panel canvas { height: 300px !important; max-height: 300px; }
        @media (max-width: 1100px) { .detail-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); } .detail-filters .wide { grid-column: span 2; } }
        @media (max-width: 800px) { .detail-filters { grid-template-columns: 1fr; } .detail-filters .wide, .detail-filters .actions { grid-column: auto; } .detail-filters .actions { justify-content: flex-start; } .detail-chart-panel { height: auto; min-height: 400px; } }
    </style>
    <div class="page-header">
        <div>
            <h1>{{ str($tweet->display_text ?: 'Detail Tweet')->limit(90) }}</h1>
            <p class="subtitle">Reply tersimpan dari tweet Twitter/X yang dipilih.</p>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route('twitter.tweets.index') }}">Kembali</a>
        </div>
    </div>

    <section class="panel stack">
        <p><strong>Isi Tweet:</strong> {{ $tweet->display_text ?: '-' }}</p>
        <p><strong>Username:</strong> {{ $tweet->author_username ?? '-' }}</p>
        <p><strong>Tanggal tweet:</strong> {{ optional($tweet->published_at)->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</p>
        <p><strong>Jumlah Reply:</strong> {{ $tweet->reply_count }}</p>
        <p><strong>Jumlah Like:</strong> {{ $tweet->like_count }}</p>
        <p><strong>Jumlah Repost:</strong> {{ $tweet->repost_count }}</p>
        @if ($tweet->permalink)
            <p><strong>Link:</strong> <a href="{{ $tweet->permalink }}" target="_blank">{{ $tweet->permalink }}</a></p>
        @endif
    </section>

    <form class="panel filters detail-filters" method="GET" action="{{ route('twitter.tweets.show', $tweet) }}">
        <label>
            Periode
            <select name="period">
                <option value="">Semua</option>
                <option value="today" @selected(($filters['period'] ?? '') === 'today')>Hari Ini</option>
                <option value="weekly" @selected(($filters['period'] ?? '') === 'weekly')>Mingguan</option>
                <option value="monthly" @selected(($filters['period'] ?? '') === 'monthly')>Bulanan</option>
                <option value="custom" @selected(($filters['period'] ?? '') === 'custom')>Custom Tanggal</option>
            </select>
        </label>
        <label>
            Sentimen
            <select name="sentiment">
                <option value="">Semua</option>
                @foreach ($sentiments as $sentiment)
                    <option value="{{ $sentiment }}" @selected(($filters['sentiment'] ?? '') === $sentiment)>{{ $sentiment }}</option>
                @endforeach
            </select>
        </label>
        <label>
            Dari
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}">
        </label>
        <label>
            Sampai
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}">
        </label>
        <label class="wide">
            Pencarian
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Username atau isi reply">
        </label>
        <div class="actions">
            <div class="filter-actions">
                <button type="submit">Filter</button>
                <a class="button secondary" href="{{ route('twitter.tweets.show', $tweet) }}">Reset</a>
            </div>
            <div class="export-actions">
                <a class="button excel" href="{{ route('reports.twitter.export.excel', array_merge(request()->query(), ['tweet_id' => $tweet->id])) }}"><i class="bi bi-file-earmark-excel-fill"></i> Download Excel</a>
                <a class="button pdf-export" href="{{ route('reports.twitter.export.pdf', array_merge(request()->query(), ['tweet_id' => $tweet->id])) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill"></i> Download PDF</a>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Jumlah Reply</div><div class="stat-value">{{ $totalCount }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Positif</div><div class="stat-value positive">{{ $positiveCount }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Netral</div><div class="stat-value neutral">{{ $neutralCount }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Negatif</div><div class="stat-value negative">{{ $negativeCount }}</div></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <section class="panel shadow-sm detail-chart-panel">
                <h1>Pie Chart Sentimen</h1>
                <canvas id="sentimentPie"></canvas>
                <div class="row g-2 mt-3">
                    <div class="col"><div class="small text-muted">Positif</div><div class="fw-bold" style="color: #2563eb;">{{ $positiveCount }}</div></div>
                    <div class="col"><div class="small text-muted">Netral</div><div class="fw-bold" style="color: #16a34a;">{{ $neutralCount }}</div></div>
                    <div class="col"><div class="small text-muted">Negatif</div><div class="fw-bold" style="color: #dc2626;">{{ $negativeCount }}</div></div>
                </div>
            </section>
        </div>
        <div class="col-lg-6"><section class="panel shadow-sm detail-chart-panel"><h1>Bar Chart Jumlah Reply</h1><canvas id="replyBar"></canvas></section></div>
    </div>

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Reply</th>
                        <th>Like</th>
                        <th>Sentimen</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comments as $comment)
                        <tr>
                            <td>{{ $comment->username ?? '-' }}</td>
                            <td>{{ $comment->reply ?: $comment->comment }}</td>
                            <td>{{ $comment->like_count ?? '-' }}</td>
                            @php($sentimentName = $comment->getRawOriginal('sentiment') ?: 'Netral')
                            <td><span class="badge {{ strtolower($sentimentName) }}">{{ $sentimentName }}</span></td>
                            <td>{{ optional($comment->commented_at)->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="5">Belum ada reply untuk tweet ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $comments->links() }}
        </div>
    </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const pieChart = @json($pieChart);
        const barChart = @json($barChart);
        new Chart(document.getElementById('sentimentPie'), { type: 'pie', data: { labels: pieChart.labels, datasets: [{ data: pieChart.data, backgroundColor: ['#2563eb', '#16a34a', '#dc2626'] }] } });
        new Chart(document.getElementById('replyBar'), {
            type: 'bar',
            data: { labels: barChart.labels, datasets: [{ label: 'Reply', data: barChart.data, backgroundColor: '#2563eb' }] },
            options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    </script>
@endsection
