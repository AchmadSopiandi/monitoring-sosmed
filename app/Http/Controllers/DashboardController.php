<?php

namespace App\Http\Controllers;

use App\Models\Sentiment;
use App\Repositories\InstagramCommentRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly InstagramCommentRepository $instagramComments,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'period' => ['nullable', 'in:weekly,monthly,yearly'],
        ]);
        $period = $filters['period'] ?? 'monthly';
        $filters = ['period' => $period];

        $instagramCounts = $this->instagramComments->countsBySentiment($filters);
        $instagramTotal = $this->instagramComments->filteredQuery($filters)->count();
        $instagramDaily = $this->instagramComments->dailyCounts($filters);
        $lineLabels = $instagramDaily->keys()->sort()->values();

        return view('dashboard-overview', [
            'instagramTotal' => $instagramTotal,
            'period' => $period,
            'positiveCount' => (int) ($instagramCounts[Sentiment::POSITIVE] ?? 0),
            'neutralCount' => (int) ($instagramCounts[Sentiment::NEUTRAL] ?? 0),
            'negativeCount' => (int) ($instagramCounts[Sentiment::NEGATIVE] ?? 0),
            'instagramPieChart' => [
                'labels' => Sentiment::NAMES,
                'data' => [
                    (int) ($instagramCounts[Sentiment::POSITIVE] ?? 0),
                    (int) ($instagramCounts[Sentiment::NEUTRAL] ?? 0),
                    (int) ($instagramCounts[Sentiment::NEGATIVE] ?? 0),
                ],
            ],
            'lineChart' => [
                'labels' => $lineLabels,
                'instagram' => $lineLabels->map(fn ($day) => (int) ($instagramDaily[$day] ?? 0)),
            ],
        ]);
    }
}
