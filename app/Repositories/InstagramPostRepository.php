<?php

namespace App\Repositories;

use App\Models\InstagramPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InstagramPostRepository
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return InstagramPost::query()
            ->withCount('comments')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('caption', 'like', "%{$search}%")
                        ->orWhere('instagram_media_id', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function latestWithCommentCounts(int $limit = 12): Collection
    {
        return InstagramPost::query()
            ->withCount('comments')
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    public function upsert(array $post): InstagramPost
    {
        return InstagramPost::updateOrCreate(
            ['instagram_media_id' => $post['instagram_media_id']],
            $post
        );
    }
}
