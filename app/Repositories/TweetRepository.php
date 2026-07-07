<?php

namespace App\Repositories;

use App\Models\Tweet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TweetRepository
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Tweet::query()
            ->withCount('comments')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('text', 'like', "%{$search}%")
                        ->orWhere('tweet', 'like', "%{$search}%")
                        ->orWhere('tweet_id', 'like', "%{$search}%")
                        ->orWhere('author_username', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function upsert(array $tweet): Tweet
    {
        return Tweet::updateOrCreate(['tweet_id' => $tweet['tweet_id']], $tweet);
    }
}
