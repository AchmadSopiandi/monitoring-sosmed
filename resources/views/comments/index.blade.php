@extends('layouts.app')

@section('title', 'Data Komentar')

@section('content')
    <div class="page-header">
        <div>
            <h1>Data Komentar</h1>
            <p class="subtitle">Cari dan filter komentar Instagram yang sudah disimpan.</p>
        </div>
        <a class="button" href="{{ route('instagram.posts.index') }}">Ambil Komentar</a>
    </div>

    <form class="panel filters" method="GET" action="{{ route('comments.index') }}">
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
