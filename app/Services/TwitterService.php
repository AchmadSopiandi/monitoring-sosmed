<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TwitterService
{
    public function __construct(private readonly ApiSettingService $settings)
    {
    }

    public function fetchTweets(): array
    {
        $bearerToken = $this->bearerToken();
        $userId = $this->userId();

        $payload = $this->get("users/{$userId}/tweets", [
            'max_results' => $this->limit('tweet_limit'),
            'tweet.fields' => 'created_at,public_metrics,conversation_id',
            'expansions' => 'author_id',
            'user.fields' => 'username,name',
            'exclude' => 'retweets,replies',
        ], $bearerToken);

        $users = $this->usersById($payload);

        return collect($payload['data'] ?? [])
            ->map(fn (array $tweet) => $this->mapTweet($tweet, $users))
            ->values()
            ->all();
    }

    public function fetchRepliesForTweet(string $tweetId): array
    {
        $bearerToken = $this->bearerToken();

        $payload = $this->get('tweets/search/recent', [
            'query' => "conversation_id:{$tweetId} -is:retweet",
            'max_results' => $this->limit('reply_limit', 10),
            'tweet.fields' => 'created_at,public_metrics,conversation_id,in_reply_to_user_id',
            'expansions' => 'author_id',
            'user.fields' => 'username,name',
        ], $bearerToken);

        $users = $this->usersById($payload);

        return collect($payload['data'] ?? [])
            ->filter(fn (array $reply) => ($reply['id'] ?? null) !== $tweetId)
            ->map(fn (array $reply) => $this->mapReply($reply, $users))
            ->values()
            ->all();
    }

    private function bearerToken(): string
    {
        $bearerToken = $this->settings->twitterBearerToken();

        if (! $bearerToken) {
            throw new RuntimeException('TWITTER_BEARER_TOKEN belum diisi di file .env atau Pengaturan API.');
        }

        return $bearerToken;
    }

    private function userId(): string
    {
        $userId = $this->settings->twitterUserId();

        if (! $userId) {
            throw new RuntimeException('TWITTER_USER_ID belum diisi di file .env.');
        }

        return $userId;
    }

    private function get(string $path, array $query, string $bearerToken): array
    {
        $baseUrl = rtrim(config('services.twitter.base_url'), '/');

        try {
            return Http::timeout(30)
                ->acceptJson()
                ->withToken($bearerToken)
                ->get("{$baseUrl}/{$path}", $query)
                ->throw()
                ->json();
        } catch (RequestException $exception) {
            $message = $exception->response?->json('detail')
                ?? $exception->response?->json('title')
                ?? $exception->getMessage();

            Log::error('Twitter/X API request failed.', [
                'path' => $path,
                'message' => $message,
                'status' => $exception->response?->status(),
            ]);

            throw new RuntimeException("Twitter/X API gagal: {$message}", previous: $exception);
        }
    }

    private function mapTweet(array $tweet, array $users): array
    {
        $metrics = $tweet['public_metrics'] ?? [];
        $author = $users[$tweet['author_id'] ?? ''] ?? [];
        $username = $author['username'] ?? null;
        $createdAt = $this->parseTimestamp($tweet['created_at'] ?? null);

        return [
            'tweet_id' => $tweet['id'],
            'text' => $tweet['text'] ?? null,
            'tweet' => $tweet['text'] ?? null,
            'author_username' => $username,
            'author' => $author['name'] ?? $username,
            'reply_count' => (int) ($metrics['reply_count'] ?? 0),
            'like_count' => (int) ($metrics['like_count'] ?? 0),
            'repost_count' => (int) ($metrics['retweet_count'] ?? 0),
            'permalink' => $username ? "https://x.com/{$username}/status/{$tweet['id']}" : "https://x.com/i/web/status/{$tweet['id']}",
            'published_at' => $createdAt,
            'posted_at' => $createdAt,
        ];
    }

    private function mapReply(array $reply, array $users): array
    {
        $metrics = $reply['public_metrics'] ?? [];
        $author = $users[$reply['author_id'] ?? ''] ?? [];

        return [
            'tweet_reply_id' => $reply['id'],
            'reply_id' => $reply['id'],
            'username' => $author['username'] ?? null,
            'comment' => $reply['text'] ?? '',
            'reply' => $reply['text'] ?? '',
            'like_count' => (int) ($metrics['like_count'] ?? 0),
            'commented_at' => $this->parseTimestamp($reply['created_at'] ?? null),
            'created_time' => $this->parseTimestamp($reply['created_at'] ?? null),
        ];
    }

    private function usersById(array $payload): array
    {
        return collect($payload['includes']['users'] ?? [])->keyBy('id')->all();
    }

    private function limit(string $key, int $minimum = 5): int
    {
        return max($minimum, min(100, (int) config("services.twitter.{$key}", 25)));
    }

    private function parseTimestamp(?string $timestamp): ?string
    {
        if (! $timestamp) {
            return null;
        }

        return Carbon::parse($timestamp)
            ->timezone(config('app.timezone'))
            ->format('Y-m-d H:i:s');
    }
}
