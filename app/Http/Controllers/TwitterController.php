<?php

namespace App\Http\Controllers;

use App\Models\Sentiment;
use App\Models\Tweet;
use App\Repositories\TweetCommentRepository;
use App\Repositories\TweetRepository;
use App\Services\SentimentService;
use App\Services\TwitterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TwitterController extends Controller
{
    public function __construct(
        private readonly TweetRepository $tweets,
        private readonly TweetCommentRepository $comments,
        private readonly SentimentService $sentimentService,
    ) {
    }

    public function index(Request $request, TwitterService $twitter): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $this->syncTweetsFromTwitter($twitter);

        return view('twitter.cards', [
            'tweets' => $this->tweets->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, Tweet $tweet, TwitterService $twitter): View
    {
        $filters = $request->validate([
            'period' => ['nullable', 'in:today,weekly,monthly,custom'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sentiment' => ['nullable', 'in:Positif,Netral,Negatif'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $filters['tweet_id'] = (string) $tweet->id;
        $this->syncRepliesFromTwitter($tweet, $twitter);
        $counts = $this->comments->countsBySentiment($filters);
        $dailyCounts = $this->comments->dailyCounts($filters);

        return view('twitter.show', [
            'tweet' => $tweet,
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

    public function syncTweets(TwitterService $twitter): RedirectResponse
    {
        try {
            $imported = $this->syncTweetsFromTwitter($twitter);

            return back()->with('success', "{$imported} tweet diproses.");
        } catch (Throwable $exception) {
            Log::error('Failed to sync Twitter/X tweets.', ['message' => $exception->getMessage()]);

            return back()->with('error', $exception->getMessage());
        }
    }

    private function syncTweetsFromTwitter(TwitterService $twitter): int
    {
        try {
            $imported = 0;

            foreach ($twitter->fetchTweets() as $tweet) {
                $this->tweets->upsert($tweet);
                $imported++;
            }

            return $imported;
        } catch (Throwable $exception) {
            Log::error('Failed to sync Twitter/X tweets.', ['message' => $exception->getMessage()]);

            return 0;
        }
    }

    public function syncReplies(Tweet $tweet, TwitterService $twitter): RedirectResponse
    {
        try {
            $imported = $this->syncRepliesFromTwitter($tweet, $twitter);

            return back()->with('success', "{$imported} reply diproses dari tweet yang dipilih.");
        } catch (Throwable $exception) {
            Log::error('Failed to sync Twitter/X replies.', [
                'tweet_id' => $tweet->id,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', $exception->getMessage());
        }
    }

    private function syncRepliesFromTwitter(Tweet $tweet, TwitterService $twitter): int
    {
        try {
            $imported = 0;

            foreach ($twitter->fetchRepliesForTweet($tweet->tweet_id) as $reply) {
                if (trim($reply['comment']) === '') {
                    continue;
                }

                $sentimentName = $this->sentimentService->analyze($reply['comment']);
                $sentiment = Sentiment::firstOrCreate(['name' => $sentimentName], ['label' => $sentimentName]);

                $this->comments->upsert([
                    ...$reply,
                    'tweet_id' => $tweet->id,
                    'sentiment_id' => $sentiment->id,
                    'sentiment' => $sentimentName,
                ]);

                $imported++;
            }

            return $imported;
        } catch (Throwable $exception) {
            Log::error('Failed to sync Twitter/X replies.', [
                'tweet_id' => $tweet->id,
                'message' => $exception->getMessage(),
            ]);

            return 0;
        }
    }
}
