<?php

namespace App\Repositories;

use App\Models\InstagramComment;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class InstagramCommentRepository
{
    public function filteredQuery(array $filters = []): Builder
    {
        return InstagramComment::query()
            ->with(['post', 'sentiment'])
            ->when($filters['sentiment'] ?? null, function (Builder $query, string $sentiment) {
                $query->where(function (Builder $nested) use ($sentiment) {
                    $nested->where('instagram_comments.sentiment', $sentiment)
                        ->orWhereHas('sentiment', fn (Builder $sentimentQuery) => $sentimentQuery->where('name', $sentiment));
                });
            })
            ->when($filters['post_id'] ?? null, fn (Builder $query, string $postId) => $query->where('instagram_post_id', $postId))
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested->where('username', 'like', "%{$search}%")
                        ->orWhere('comment', 'like', "%{$search}%")
                        ->orWhereHas('post', fn (Builder $post) => $post->where('caption', 'like', "%{$search}%"));
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

    public function countsBySentiment(array $filters = []): Collection
    {
        return $this->filteredQuery($filters)
            ->selectRaw("COALESCE(instagram_comments.sentiment, sentiments.name, 'Netral') as sentiment_name, count(*) as total")
            ->leftJoin('sentiments', 'sentiments.id', '=', 'instagram_comments.sentiment_id')
            ->groupByRaw("COALESCE(instagram_comments.sentiment, sentiments.name, 'Netral')")
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

    public function upsert(array $comment): InstagramComment
    {
        return InstagramComment::updateOrCreate(
            ['instagram_comment_id' => $comment['instagram_comment_id']],
            $comment
        );
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
