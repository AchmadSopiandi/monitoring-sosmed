@extends('layouts.app')

@section('title', 'Tweet Twitter/X')

@section('content')
    <div class="page-header">
        <div>
            <h1>Tweet Twitter/X</h1>
            <p class="subtitle">Ambil daftar tweet, pilih salah satu, lalu sinkronkan reply.</p>
        </div>
    </div>

    <form class="panel filters" method="GET" action="{{ route('twitter.tweets.index') }}">
        <label class="wide">
            Pencarian
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Isi tweet atau username">
        </label>
        <div class="actions">
            <button type="submit">Cari</button>
            <a class="button secondary" href="{{ route('twitter.tweets.index') }}">Reset</a>
        </div>
    </form>

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tweet</th>
                        <th>Username</th>
                        <th>Tanggal</th>
                        <th>Reply</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tweets as $tweet)
                        <tr>
                            <td>
                                <a href="{{ route('twitter.tweets.show', $tweet) }}">{{ str($tweet->display_text ?: 'Tweet tanpa teks')->limit(90) }}</a>
                                @if ($tweet->permalink)
                                    <div><a class="muted" href="{{ $tweet->permalink }}" target="_blank">Buka Twitter/X</a></div>
                                @endif
                            </td>
                            <td>{{ $tweet->author_username ?? '-' }}</td>
                            <td>{{ optional($tweet->published_at)->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</td>
                            <td>{{ $tweet->comments_count }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button secondary" href="{{ route('twitter.tweets.show', $tweet) }}">Pilih</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="5">Belum ada tweet Twitter/X.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $tweets->links() }}
        </div>
    </section>
@endsection
