<div class="table-wrap">
    <table>
        <thead>
            <tr>
                @unless ($detail ?? false)
                    <th>Sumber</th>
                @endunless
                <th>Username</th>
                <th>Komentar</th>
                @unless ($detail ?? false)
                    <th>Postingan</th>
                    <th>Like</th>
                @endunless
                <th>Tanggal</th>
                <th>Sentimen</th>
                @unless ($detail ?? false)
                    <th>Aksi</th>
                @endunless
            </tr>
        </thead>
        <tbody>
            @forelse ($comments as $comment)
                <tr>
                    @unless ($detail ?? false)
                        <td>
                            @if ($comment instanceof \App\Models\TweetComment)
                                <span class="badge source-twitter"><i class="bi bi-twitter-x"></i> Twitter/X</span>
                            @else
                                <span class="badge source-instagram"><i class="bi bi-instagram"></i> Instagram</span>
                            @endif
                        </td>
                    @endunless
                    <td>{{ $comment->username }}</td>
                    <td>{{ $comment->comment }}</td>
                    @unless ($detail ?? false)
                        <td>
                            @if ($comment instanceof \App\Models\TweetComment && $comment->tweet)
                                <a href="{{ route('twitter.tweets.show', $comment->tweet) }}">{{ str($comment->tweet->display_text)->limit(80) }}</a>
                            @elseif ($comment->post)
                                <a href="{{ route('instagram.posts.show', $comment->post) }}">{{ $comment->post->title }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $comment->like_count ?? '-' }}</td>
                    @endunless
                    <td>{{ optional($comment->commented_at ?? $comment->created_time)->timezone(config('app.timezone'))->format('d M Y H:i') }} WIB</td>
                    <td>
                        @php($sentimentName = $comment->getRawOriginal('sentiment') ?: 'Netral')
                        <span class="badge {{ strtolower($sentimentName) }}">
                            {{ $sentimentName }}
                        </span>
                    </td>
                    @unless ($detail ?? false)
                        <td>
                            <div class="actions">
                                <a class="button secondary" href="{{ route('comments.show', ['comment' => $comment, 'source' => $comment instanceof \App\Models\TweetComment ? 'twitter' : 'instagram']) }}">Detail</a>
                            </div>
                        </td>
                    @endunless
                </tr>
            @empty
                <tr>
                    <td class="empty" colspan="{{ ($detail ?? false) ? 4 : 8 }}">Belum ada komentar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
