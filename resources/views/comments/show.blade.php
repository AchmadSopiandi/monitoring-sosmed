@extends('layouts.app')

@section('title', 'Detail Komentar')

@section('content')
    <div class="page-header">
        <div>
            <h1>Detail Komentar</h1>
            <p class="subtitle">Informasi lengkap komentar Instagram.</p>
        </div>
        <a class="button secondary" href="{{ route('comments.index') }}">Kembali</a>
    </div>

    <section class="panel">
        <p><strong>Sumber:</strong> Instagram</p>
        <p><strong>Postingan:</strong> {{ $comment->post?->title ?? '-' }}</p>
        <p><strong>Username:</strong> {{ $comment->username }}</p>
        <p><strong>Komentar:</strong> {{ $comment->comment }}</p>
        <p><strong>Like:</strong> {{ $comment->like_count ?? '-' }}</p>
        <p><strong>Sentimen:</strong> {{ $comment->getRawOriginal('sentiment') ?: 'Netral' }}</p>
        <p><strong>Dibuat:</strong> {{ optional($comment->commented_at ?? $comment->created_time)->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</p>

    </section>
@endsection
