@extends('layouts.app')

@section('title', 'Data Komentar')

@section('content')
    <style>
        .comments-filters { grid-template-columns: repeat(6, minmax(120px, 1fr)); }
        @media (max-width: 1100px) { .comments-filters { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (max-width: 800px) { .comments-filters { grid-template-columns: 1fr; } }
    </style>
    <div class="page-header">
        <div>
            <h1>Data Komentar</h1>
            <p class="subtitle">Pantau seluruh komentar Instagram dan Twitter/X yang sudah disimpan.</p>
        </div>
        <div class="page-header-actions">
            <a class="button excel" href="{{ route('reports.export.excel', $filters) }}"><i class="bi bi-file-earmark-excel-fill"></i> Download Excel</a>
            <a class="button pdf-export" href="{{ route('reports.export.pdf', $filters) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill"></i> Download PDF</a>
        </div>
    </div>

    <form class="panel filters comments-filters" method="GET" action="{{ route('comments.index') }}">
        <label>
            Sumber
            <select name="source">
                <option value="">Semua platform</option>
                <option value="instagram" @selected(($filters['source'] ?? '') === 'instagram')>Instagram</option>
                <option value="twitter" @selected(($filters['source'] ?? '') === 'twitter')>Twitter/X</option>
            </select>
        </label>
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
            Postingan
            <select name="post_id">
                <option value="">Semua</option>
                @foreach ($posts as $post)
                    <option value="{{ $post->id }}" @selected((string) ($filters['post_id'] ?? '') === (string) $post->id)>{{ $post->title }}</option>
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
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Username, isi komentar, atau postingan">
        </label>
        <div class="actions">
            <button type="submit">Filter</button>
            <a class="button secondary" href="{{ route('comments.index') }}">Reset</a>
        </div>
    </form>

    <section class="panel">
        @include('comments.partials.table', ['comments' => $comments])

        <div class="pagination">
            {{ $comments->links() }}
        </div>
    </section>
@endsection
