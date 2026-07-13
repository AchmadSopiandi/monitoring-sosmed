@extends('layouts.app')

@section('title', 'Detail Postingan Instagram')

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $post->title }}</h1>
            <p class="subtitle">Komentar tersimpan dari postingan Instagram yang dipilih.</p>
        </div>
        <div class="actions">
            <form action="{{ route('instagram.posts.comments.sync', $post) }}" method="POST">
                @csrf
                <button type="submit">Sync Komentar</button>
            </form>
            <a class="button secondary" href="{{ route('instagram.posts.index') }}">Kembali</a>
        </div>
    </div>

    <section class="panel stack">
        @if ($post->display_image)
            <img src="{{ $post->display_image }}" alt="Preview Instagram" class="rounded shadow-sm js-media-preview" style="max-height: 360px; object-fit: cover; width: 100%;">
            <div class="bg-light d-none align-items-center justify-content-center text-muted rounded" style="height: 260px;">
                <i class="bi bi-image fs-1"></i>
            </div>
        @endif
        <p><strong>Caption:</strong> {{ $post->caption ?: '-' }}</p>
        <p><strong>Tanggal posting:</strong> {{ optional($post->published_at)->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</p>
        <p><strong>Jumlah Like:</strong> {{ $post->like_count ?? 0 }}</p>
        <p><strong>Jumlah Komentar:</strong> {{ $post->comments_count ?? $totalCount }}</p>
        @if ($post->permalink)
            <p><strong>Link:</strong> <a href="{{ $post->permalink }}" target="_blank">{{ $post->permalink }}</a></p>
        @endif
    </section>

    <form class="panel filters" method="GET" action="{{ route('instagram.posts.show', $post) }}">
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
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Username atau isi komentar">
        </label>
        <div class="actions">
            <button type="submit">Filter</button>
            <a class="button secondary" href="{{ route('instagram.posts.show', $post) }}">Reset</a>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Jumlah Komentar</div><div class="stat-value">{{ $totalCount }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Positif</div><div class="stat-value positive">{{ $positiveCount }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Netral</div><div class="stat-value neutral">{{ $neutralCount }}</div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Negatif</div><div class="stat-value negative">{{ $negativeCount }}</div></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <section class="panel shadow-sm">
                <h1>Pie Chart Sentimen</h1>
                <canvas id="sentimentPie"></canvas>
                <div class="row g-2 mt-3">
                    <div class="col"><div class="small text-muted">Positif</div><div class="fw-bold" style="color: #2563eb;">{{ $positiveCount }}</div></div>
                    <div class="col"><div class="small text-muted">Netral</div><div class="fw-bold" style="color: #16a34a;">{{ $neutralCount }}</div></div>
                    <div class="col"><div class="small text-muted">Negatif</div><div class="fw-bold" style="color: #dc2626;">{{ $negativeCount }}</div></div>
                </div>
            </section>
        </div>
        <div class="col-lg-6"><section class="panel shadow-sm"><h1>Bar Chart Jumlah Komentar</h1><canvas id="commentBar"></canvas></section></div>
    </div>

    <div class="report-actions">
        <a class="button excel" href="{{ route('reports.export.excel', array_merge(request()->query(), ['post_id' => $post->id])) }}"><i class="bi bi-file-earmark-excel-fill"></i>Export Excel</a>
        <a class="button secondary" href="{{ route('reports.export.pdf', array_merge(request()->query(), ['post_id' => $post->id])) }}" target="_blank">Export PDF</a>
    </div>

    <section class="panel">
        @include('comments.partials.table', ['comments' => $comments, 'detail' => true])

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
        document.querySelectorAll('.js-media-preview').forEach((image) => {
            image.addEventListener('error', () => {
                image.classList.add('d-none');
                image.nextElementSibling?.classList.replace('d-none', 'd-flex');
            }, { once: true });
        });
        new Chart(document.getElementById('sentimentPie'), { type: 'pie', data: { labels: pieChart.labels, datasets: [{ data: pieChart.data, backgroundColor: ['#2563eb', '#16a34a', '#dc2626'] }] } });
        new Chart(document.getElementById('commentBar'), {
            type: 'bar',
            data: { labels: barChart.labels, datasets: [{ label: 'Komentar', data: barChart.data, backgroundColor: '#2563eb' }] },
            options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    </script>
@endsection
