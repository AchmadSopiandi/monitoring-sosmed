@extends('layouts.app')

@section('title', 'Postingan Twitter/X')

@section('content')
    <div class="page-header">
        <div>
            <h1>Postingan Twitter/X</h1>
            <p class="subtitle">Pilih tweet untuk melihat reply, statistik, grafik, dan export.</p>
        </div>
    </div>

    <form class="panel filters" method="GET" action="{{ route('twitter.tweets.index') }}">
        <label class="wide">Pencarian<input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Isi tweet atau username"></label>
        <div class="actions"><button type="submit">Cari</button><a class="button secondary" href="{{ route('twitter.tweets.index') }}">Reset</a></div>
    </form>

    <div class="row g-4">
        @forelse ($tweets as $tweet)
            <div class="col-md-6 col-xl-4">
                <article class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <p class="card-text fs-5">{{ str($tweet->display_text ?: 'Tweet tanpa teks')->limit(180) }}</p>
                        <div class="text-muted small mb-3">
                            <div><i class="bi bi-person"></i> {{ $tweet->author ?: $tweet->author_username ?: '-' }}</div>
                            <div><i class="bi bi-chat"></i> {{ $tweet->reply_count }} reply</div>
                            <div><i class="bi bi-heart"></i> {{ $tweet->like_count }} like</div>
                            <div><i class="bi bi-repeat"></i> {{ $tweet->repost_count }} repost</div>
                            <div><i class="bi bi-calendar"></i> {{ optional($tweet->posted_at ?: $tweet->published_at)->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</div>
                        </div>
                        <div class="actions mt-auto">
                            <a class="button" href="{{ route('twitter.tweets.show', $tweet) }}">Lihat Tweet</a>
                            @if ($tweet->permalink)
                                <a class="button secondary" href="{{ $tweet->permalink }}" target="_blank">Buka Tweet</a>
                            @endif
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="panel empty">Belum ada tweet Twitter/X.</div></div>
        @endforelse
    </div>

    <div class="pagination">{{ $tweets->links() }}</div>
@endsection
