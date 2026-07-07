<?php

namespace App\Http\Controllers;

use App\Models\Sentiment;
use App\Repositories\InstagramCommentRepository;
use App\Repositories\TweetCommentRepository;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly InstagramCommentRepository $instagramComments,
        private readonly TweetCommentRepository $tweetComments,
    ) {
    }

    public function index(): View
    {
        $instagramCounts = $this->instagramComments->countsBySentiment();
        $twitterCounts = $this->tweetComments->countsBySentiment();
        $instagramTotal = $this->instagramComments->filteredQuery()->count();
        $twitterTotal = $this->tweetComments->filteredQuery()->count();
        $instagramDaily = $this->instagramComments->dailyCounts();
        $twitterDaily = $this->tweetComments->dailyCounts();
        $lineLabels = $instagramDaily->keys()->merge($twitterDaily->keys())->unique()->sort()->values();

        return view('dashboard-overview', [
            'instagramTotal' => $instagramTotal,
            'twitterTotal' => $twitterTotal,
            'positiveCount' => (int) ($instagramCounts[Sentiment::POSITIVE] ?? 0) + (int) ($twitterCounts[Sentiment::POSITIVE] ?? 0),
            'neutralCount' => (int) ($instagramCounts[Sentiment::NEUTRAL] ?? 0) + (int) ($twitterCounts[Sentiment::NEUTRAL] ?? 0),
            'negativeCount' => (int) ($instagramCounts[Sentiment::NEGATIVE] ?? 0) + (int) ($twitterCounts[Sentiment::NEGATIVE] ?? 0),
            'instagramPieChart' => [
                'labels' => Sentiment::NAMES,
                'data' => [
                    (int) ($instagramCounts[Sentiment::POSITIVE] ?? 0),
                    (int) ($instagramCounts[Sentiment::NEUTRAL] ?? 0),
                    (int) ($instagramCounts[Sentiment::NEGATIVE] ?? 0),
                ],
            ],
            'twitterPieChart' => [
                'labels' => Sentiment::NAMES,
                'data' => [
                    (int) ($twitterCounts[Sentiment::POSITIVE] ?? 0),
                    (int) ($twitterCounts[Sentiment::NEUTRAL] ?? 0),
                    (int) ($twitterCounts[Sentiment::NEGATIVE] ?? 0),
                ],
            ],
            'comparisonChart' => [
                'labels' => ['Instagram', 'Twitter/X'],
                'data' => [$instagramTotal, $twitterTotal],
            ],
            'lineChart' => [
                'labels' => $lineLabels,
                'instagram' => $lineLabels->map(fn ($day) => (int) ($instagramDaily[$day] ?? 0)),
                'twitter' => $lineLabels->map(fn ($day) => (int) ($twitterDaily[$day] ?? 0)),
            ],
        ]);
    }
}
