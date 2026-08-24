<?php

namespace App\Http\Controllers;

use App\Models\InstagramPost;
use App\Models\Sentiment;
use App\Repositories\InstagramCommentRepository;
use App\Repositories\InstagramPostRepository;
use App\Services\InstagramService;
use App\Services\SentimentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class InstagramPostController extends Controller
{
    public function __construct(
        private readonly InstagramPostRepository $posts,
        private readonly InstagramCommentRepository $comments,
        private readonly SentimentService $sentimentService,
    ) {
    }

    public function index(Request $request, InstagramService $instagram): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $this->syncPostsFromInstagram($instagram);

        return view('instagram.cards', [
            'posts' => $this->posts->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, InstagramPost $instagramPost, InstagramService $instagram): View
    {
        $filters = $request->validate([
            'period' => ['nullable', 'in:today,weekly,monthly,custom'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sentiment' => ['nullable', 'in:Positif,Netral,Negatif'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $filters['post_id'] = (string) $instagramPost->id;
        $this->syncCommentsFromInstagram($instagramPost, $instagram);
        $counts = $this->comments->countsBySentiment($filters);
        $dailyCounts = $this->comments->dailyCounts($filters);

        return view('instagram.show', [
            'post' => $instagramPost,
            'comments' => $this->comments->paginate($filters),
            'filters' => $filters,
            'sentiments' => Sentiment::NAMES,
            'totalCount' => $this->comments->filteredQuery($filters)->count(),
            'positiveCount' => (int) ($counts[Sentiment::POSITIVE] ?? 0),
            'neutralCount' => (int) ($counts[Sentiment::NEUTRAL] ?? 0),
            'negativeCount' => (int) ($counts[Sentiment::NEGATIVE] ?? 0),
            'pieChart' => [
                'labels' => Sentiment::NAMES,
                'data' => [
                    (int) ($counts[Sentiment::POSITIVE] ?? 0),
                    (int) ($counts[Sentiment::NEUTRAL] ?? 0),
                    (int) ($counts[Sentiment::NEGATIVE] ?? 0),
                ],
            ],
            'barChart' => [
                'labels' => $dailyCounts->keys()->values(),
                'data' => $dailyCounts->values(),
            ],
        ]);
    }

    public function syncPosts(InstagramService $instagram): RedirectResponse
    {
        try {
            $imported = $this->syncPostsFromInstagram($instagram);

            return back()->with('success', "{$imported} postingan Instagram diproses.");
        } catch (Throwable $exception) {
            Log::error('Failed to sync Instagram posts.', ['message' => $exception->getMessage()]);

            return back()->with('error', $exception->getMessage());
        }
    }

    private function syncPostsFromInstagram(InstagramService $instagram): int
    {
        try {
            $imported = 0;

            foreach ($instagram->fetchPosts() as $post) {
                $this->posts->upsert($post);
                $imported++;
            }

            return $imported;
        } catch (Throwable $exception) {
            Log::error('Failed to sync Instagram posts.', ['message' => $exception->getMessage()]);

            return 0;
        }
    }

    public function syncComments(InstagramPost $instagramPost, InstagramService $instagram): RedirectResponse
    {
        try {
            $imported = $this->syncCommentsFromInstagram($instagramPost, $instagram);

            return back()->with('success', "{$imported} komentar diproses dari postingan yang dipilih.");
        } catch (Throwable $exception) {
            Log::error('Failed to sync Instagram comments.', [
                'instagram_post_id' => $instagramPost->id,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', $exception->getMessage());
        }
    }

    private function syncCommentsFromInstagram(InstagramPost $instagramPost, InstagramService $instagram): int
    {
        try {
            $imported = 0;

            foreach ($instagram->fetchCommentsForPost($instagramPost->instagram_media_id) as $comment) {
                if (trim($comment['comment']) === '') {
                    continue;
                }

                $sentimentName = $this->sentimentService->analyze($comment['comment']);
                $sentiment = Sentiment::firstOrCreate(['name' => $sentimentName], ['label' => $sentimentName]);

                $this->comments->upsert([
                    ...$comment,
                    'instagram_post_id' => $instagramPost->id,
                    'sentiment_id' => $sentiment->id,
                    'sentiment' => $sentimentName,
                ]);

                $imported++;
            }

            return $imported;
        } catch (Throwable $exception) {
            Log::error('Failed to sync Instagram comments.', [
                'instagram_post_id' => $instagramPost->id,
                'message' => $exception->getMessage(),
            ]);

            return 0;
        }
    }
}
