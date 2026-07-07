@extends('layouts.app')

@section('title', 'Postingan Instagram')

@section('content')
    <div class="page-header">
        <div>
            <h1>Postingan Instagram</h1>
            <p class="subtitle">Pilih postingan untuk melihat komentar, statistik, grafik, dan export.</p>
        </div>
        <form action="{{ route('instagram.posts.sync') }}" method="POST">@csrf<button type="submit">Sinkronisasi Instagram</button></form>
    </div>

    <form class="panel filters" method="GET" action="{{ route('instagram.posts.index') }}">
        <label class="wide">Pencarian<input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Caption atau media ID"></label>
        <div class="actions"><button type="submit">Cari</button><a class="button secondary" href="{{ route('instagram.posts.index') }}">Reset</a></div>
    </form>

    <div class="row g-4">
        @forelse ($posts as $post)
            <div class="col-md-6 col-xl-4">
                <article class="card h-100 shadow-sm border-0">
                    @if ($post->display_image)
                        <img class="card-img-top js-media-preview" src="{{ $post->display_image }}" alt="Media Instagram" style="height: 240px; object-fit: cover;">
                        <div class="bg-light d-none align-items-center justify-content-center text-muted" style="height: 240px;">
                            <i class="bi bi-image fs-1"></i>
                        </div>
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 240px;">
                            <i class="bi bi-image fs-1"></i>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <p class="card-text">{{ str($post->caption ?: 'Tanpa caption')->limit(130) }}</p>
                        <div class="text-muted small mb-3">
                            <div><i class="bi bi-heart"></i> {{ $post->like_count ?? 0 }} like</div>
                            <div><i class="bi bi-chat"></i> {{ $post->comments_count ?? 0 }} komentar</div>
                            <div><i class="bi bi-calendar"></i> {{ optional($post->posted_at ?: $post->published_at)->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</div>
                        </div>
                        <div class="actions mt-auto">
                            <a class="button" href="{{ route('instagram.posts.show', $post) }}">Lihat Komentar</a>
                            @if ($post->permalink)
                                <a class="button secondary" href="{{ $post->permalink }}" target="_blank">Buka di Instagram</a>
                            @endif
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="panel empty">Belum ada postingan. Klik Sinkronisasi Instagram.</div></div>
        @endforelse
    </div>

    <div class="pagination">{{ $posts->links() }}</div>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('.js-media-preview').forEach((image) => {
            image.addEventListener('error', () => {
                image.classList.add('d-none');
                image.nextElementSibling?.classList.replace('d-none', 'd-flex');
            }, { once: true });
        });
    </script>
@endsection
