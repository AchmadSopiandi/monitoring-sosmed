<?php

namespace App\Repositories;

use App\Models\TweetComment;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TweetCommentRepository
{
    public function filteredQuery(array $filters = []): Builder
    {
        return TweetComment::query()
            ->with(['tweet', 'sentiment'])
            ->when($filters['sentiment'] ?? null, function (Builder $query, string $sentiment) {
                $query->where(function (Builder $nested) use ($sentiment) {
                    $nested->where('tweet_comments.sentiment', $sentiment)
                        ->orWhereHas('sentiment', fn (Builder $sentimentQuery) => $sentimentQuery->where('name', $sentiment));
                });
            })
            ->when($filters['tweet_id'] ?? null, fn (Builder $query, string $tweetId) => $query->where('tweet_id', $tweetId))
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested->where('username', 'like', "%{$search}%")
                        ->orWhere('comment', 'like', "%{$search}%")
                        ->orWhere('reply', 'like', "%{$search}%")
                        ->orWhereHas('tweet', fn (Builder $tweet) => $tweet
                            ->where('text', 'like', "%{$search}%")
                            ->orWhere('tweet', 'like', "%{$search}%"));
                });
            })
            ->when($this->dateRange($filters), function (Builder $query, array $range) {
                $query->where(function (Builder $nested) use ($range) {
                    $nested->whereBetween('commented_at', [$range[0], $range[1]])
                        ->orWhereBetween('created_time', [$range[0], $range[1]]);
                });
            });
    }

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->latest('commented_at')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function upsert(array $comment): TweetComment
    {
        return TweetComment::updateOrCreate(['tweet_reply_id' => $comment['tweet_reply_id']], $comment);
    }

    public function countsBySentiment(array $filters = []): Collection
    {
        return $this->filteredQuery($filters)
            ->selectRaw("COALESCE(tweet_comments.sentiment, sentiments.name, 'Netral') as sentiment_name, count(*) as total")
            ->leftJoin('sentiments', 'sentiments.id', '=', 'tweet_comments.sentiment_id')
            ->groupByRaw("COALESCE(tweet_comments.sentiment, sentiments.name, 'Netral')")
            ->pluck('total', 'sentiment_name');
    }

    public function dailyCounts(array $filters = []): Collection
    {
        return $this->filteredQuery($filters)
            ->selectRaw('DATE(COALESCE(commented_at, created_time)) as day, count(*) as total')
            ->where(function (Builder $query) {
                $query->whereNotNull('commented_at')
                    ->orWhereNotNull('created_time');
            })
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');
    }

    private function dateRange(array $filters): ?array
    {
        if (! empty($filters['from']) || ! empty($filters['to'])) {
            return $this->customDateRange($filters);
        }

        $period = $filters['period'] ?? null;
        $today = now();

        return match ($period) {
            'today' => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
            'weekly' => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
            'monthly' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'yearly' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            'custom' => $this->customDateRange($filters),
            default => null,
        };
    }

    private function customDateRange(array $filters): ?array
    {
        if (empty($filters['from']) && empty($filters['to'])) {
            return null;
        }

        if (! empty($filters['from']) && empty($filters['to'])) {
            return [
                CarbonImmutable::parse($filters['from'])->startOfDay(),
                CarbonImmutable::parse($filters['from'])->endOfDay(),
            ];
        }

        if (empty($filters['from']) && ! empty($filters['to'])) {
            return [
                CarbonImmutable::parse($filters['to'])->startOfDay(),
                CarbonImmutable::parse($filters['to'])->endOfDay(),
            ];
        }

        return [
            CarbonImmutable::parse($filters['from'])->startOfDay(),
            CarbonImmutable::parse($filters['to'])->endOfDay(),
        ];
    }
}
