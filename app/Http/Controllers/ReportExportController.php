<?php

namespace App\Http\Controllers;

use App\Models\Sentiment;
use App\Repositories\InstagramCommentRepository;
use App\Repositories\TweetCommentRepository;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    public function __construct(
        private readonly InstagramCommentRepository $comments,
        private readonly TweetCommentRepository $tweetComments,
        private readonly ExportService $exportService,
    )
    {
    }

    public function excel(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);
        $comments = $this->comments->filteredQuery($filters)->latest('commented_at')->get();
        $filename = 'laporan-social-monitoring-'.now()->format('Ymd-His').'.xls';

        return Response::streamDownload(function () use ($comments) {
            echo '<table border="1">';
            echo '<thead><tr><th>Tanggal</th><th>Postingan</th><th>Username</th><th>Komentar</th><th>Like</th><th>Sentimen</th></tr></thead><tbody>';

            foreach ($comments as $comment) {
                echo '<tr>';
                echo '<td>'.e(optional($comment->commented_at)->format('Y-m-d H:i:s')).'</td>';
                echo '<td>'.e($comment->post?->title).'</td>';
                echo '<td>'.e($comment->username).'</td>';
                echo '<td>'.e($comment->comment).'</td>';
                echo '<td>'.e($comment->like_count ?? '-').'</td>';
                echo '<td>'.e($this->exportService->sentimentName($comment)).'</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }

    public function pdf(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $comments = $this->comments->filteredQuery($filters)->latest('commented_at')->get();
        $counts = $this->comments->countsBySentiment($filters);

        return view('reports.pdf', [
            'comments' => $comments,
            'filters' => $filters,
            'totalCount' => $comments->count(),
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
        ]);
    }

    public function twitterExcel(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'period' => ['nullable', 'in:today,weekly,monthly,yearly,custom'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sentiment' => ['nullable', 'in:Positif,Netral,Negatif'],
            'tweet_id' => ['required', 'exists:tweets,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $comments = $this->tweetComments->filteredQuery($filters)->latest('commented_at')->get();
        $filename = 'laporan-twitter-monitoring-'.now()->format('Ymd-His').'.xls';

        return Response::streamDownload(function () use ($comments) {
            echo '<table border="1"><thead><tr><th>Tanggal</th><th>Tweet</th><th>Username</th><th>Reply</th><th>Like</th><th>Sentimen</th></tr></thead><tbody>';
            foreach ($comments as $comment) {
                echo '<tr>';
                echo '<td>'.e(optional($comment->commented_at)->format('Y-m-d H:i:s')).'</td>';
                echo '<td>'.e(str($comment->tweet?->text)->limit(120)).'</td>';
                echo '<td>'.e($comment->username).'</td>';
                echo '<td>'.e($comment->reply ?: $comment->comment).'</td>';
                echo '<td>'.e($comment->like_count ?? '-').'</td>';
                echo '<td>'.e($this->exportService->sentimentName($comment)).'</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }

    public function twitterPdf(Request $request): View
    {
        $filters = $request->validate([
            'period' => ['nullable', 'in:today,weekly,monthly,yearly,custom'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sentiment' => ['nullable', 'in:Positif,Netral,Negatif'],
            'tweet_id' => ['required', 'exists:tweets,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $comments = $this->tweetComments->filteredQuery($filters)->latest('commented_at')->get();
        $counts = $this->tweetComments->countsBySentiment($filters);

        return view('reports.twitter-pdf', [
            'comments' => $comments,
            'totalCount' => $comments->count(),
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
        ]);
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'period' => ['nullable', 'in:today,weekly,monthly,yearly,custom'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sentiment' => ['nullable', 'in:Positif,Netral,Negatif'],
            'post_id' => ['nullable', 'exists:instagram_posts,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
