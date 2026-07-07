@extends('layouts.app')

@section('title', 'Postingan Instagram')

@section('content')
    <div class="page-header">
        <div>
            <h1>Postingan Instagram</h1>
            <p class="subtitle">Ambil daftar postingan, pilih satu, lalu sinkronkan komentarnya.</p>
        </div>
        <form action="{{ route('instagram.posts.sync') }}" method="POST">
            @csrf
            <button type="submit">Sync Postingan</button>
        </form>
    </div>

    <form class="panel filters" method="GET" action="{{ route('instagram.posts.index') }}">
        <label class="wide">
            Pencarian
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Caption postingan">
        </label>
        <div class="actions">
            <button type="submit">Cari</button>
            <a class="button secondary" href="{{ route('instagram.posts.index') }}">Reset</a>
        </div>
    </form>

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Postingan</th>
                        <th>Tanggal</th>
                        <th>Komentar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td>
                                <a href="{{ route('instagram.posts.show', $post) }}">{{ $post->title }}</a>
                                @if ($post->permalink)
                                    <div><a class="muted" href="{{ $post->permalink }}" target="_blank">Buka Instagram</a></div>
                                @endif
                            </td>
                            <td>{{ optional($post->published_at)->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</td>
                            <td>{{ $post->comments_count }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button secondary" href="{{ route('instagram.posts.show', $post) }}">Pilih</a>
                                    <form action="{{ route('instagram.posts.comments.sync', $post) }}" method="POST">
                                        @csrf
                                        <button type="submit">Sync Komentar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="4">Belum ada postingan. Klik Sync Postingan untuk mengambil daftar dari Instagram.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $posts->links() }}
        </div>
    </section>
@endsection
