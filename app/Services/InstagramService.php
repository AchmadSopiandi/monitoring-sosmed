<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class InstagramService
{
    public function __construct(private readonly ApiSettingService $settings)
    {
    }

    public function fetchPosts(): array
    {
        $accessToken = $this->settings->instagramToken();
        $userId = $this->settings->instagramUserId();

        if (! $accessToken || ! $userId) {
            throw new InvalidArgumentException('INSTAGRAM_ACCESS_TOKEN dan INSTAGRAM_USER_ID belum diisi di file .env.');
        }

        return array_map(fn (array $media) => [
            'instagram_media_id' => $media['id'],
            'media_id' => $media['id'],
            'caption' => $media['caption'] ?? null,
            'permalink' => $media['permalink'] ?? null,
            'media_type' => $media['media_type'] ?? null,
            'media_url' => $media['media_url'] ?? null,
            'thumbnail_url' => $media['thumbnail_url'] ?? null,
            'like_count' => $media['like_count'] ?? 0,
            'comments_count' => $media['comments_count'] ?? 0,
            'published_at' => $this->parseInstagramTimestamp($media['timestamp'] ?? null),
            'posted_at' => $this->parseInstagramTimestamp($media['timestamp'] ?? null),
        ], $this->fetchMedia($userId, $accessToken));
    }

    public function fetchCommentsForPost(string $mediaId): array
    {
        $accessToken = $this->settings->instagramToken();

        if (! $accessToken) {
            throw new InvalidArgumentException('INSTAGRAM_ACCESS_TOKEN belum diisi di file .env.');
        }

        return array_map(fn (array $comment) => [
            'instagram_comment_id' => $comment['id'],
            'comment_id' => $comment['id'],
            'username' => $comment['username'] ?? 'instagram_user',
            'comment' => $comment['text'] ?? '',
            'like_count' => $comment['like_count'] ?? null,
            'commented_at' => $this->parseInstagramTimestamp($comment['timestamp'] ?? null),
            'created_time' => $this->parseInstagramTimestamp($comment['timestamp'] ?? null),
        ], $this->fetchMediaComments($mediaId, $accessToken));
    }

    private function fetchMedia(string $userId, string $accessToken): array
    {
        return $this->get("$userId/media", [
            'fields' => 'id,caption,timestamp,permalink,media_type,media_url,thumbnail_url,like_count,comments_count',
            'limit' => config('services.instagram.media_limit'),
            'access_token' => $accessToken,
        ]);
    }

    private function fetchMediaComments(string $mediaId, string $accessToken): array
    {
        return $this->get("$mediaId/comments", [
            'fields' => 'id,text,username,timestamp,like_count',
            'limit' => config('services.instagram.comments_limit'),
            'access_token' => $accessToken,
        ]);
    }

    private function get(string $path, array $query): array
    {
        $baseUrl = rtrim(config('services.instagram.base_url'), '/');
        $version = trim(config('services.instagram.graph_version'), '/');

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->get("$baseUrl/$version/$path", $query)
                ->throw();
        } catch (RequestException $exception) {
            $message = $exception->response?->json('error.message') ?? $exception->getMessage();
            Log::error('Instagram API request failed.', [
                'path' => $path,
                'message' => $message,
                'status' => $exception->response?->status(),
            ]);

            throw new InvalidArgumentException("Instagram API gagal: $message", previous: $exception);
        }

        return $response->json('data', []);
    }

    private function parseInstagramTimestamp(?string $timestamp): ?string
    {
        if (! $timestamp) {
            return null;
        }

        return Carbon::parse($timestamp ?? now())
            ->timezone(config('app.timezone'))
            ->format('Y-m-d H:i:s');
    }
}
