<?php

namespace App\Http\Controllers;

use App\Models\Sentiment;
use App\Models\InstagramComment;
use App\Repositories\InstagramCommentRepository;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    public function __construct(
        private readonly InstagramCommentRepository $comments,
        private readonly ExportService $exportService,
    )
    {
    }

    public function excel(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);
        $comments = $this->commentsForExport($filters);
        $filename = 'laporan-social-monitoring-'.now()->format('Ymd-His').'.xls';

        return Response::streamDownload(function () use ($comments) {
            echo '<table border="1">';
            echo '<thead><tr><th>Tanggal</th><th>Sumber</th><th>Postingan</th><th>Username</th><th>Komentar</th><th>Like</th><th>Sentimen</th></tr></thead><tbody>';

            foreach ($comments as $comment) {
                echo '<tr>';
                echo '<td>'.e(optional($comment->commented_at ?? $comment->created_time)->format('Y-m-d H:i:s')).'</td>';
                echo '<td>Instagram</td>';
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
        $comments = $this->commentsForExport($filters);
        $counts = $comments->countBy(fn (object $comment) => $this->exportService->sentimentName($comment));

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

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'period' => ['nullable', 'in:today,weekly,monthly,yearly,custom'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sentiment' => ['nullable', 'in:Positif,Netral,Negatif'],
            'source' => ['nullable', 'in:instagram'],
            'post_id' => ['nullable', 'exists:instagram_posts,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);
    }

    private function commentsForExport(array $filters): Collection
    {
        return $this->comments->filteredQuery($filters)->latest('commented_at')->get()
            ->each(fn (InstagramComment $comment) => $comment->setAttribute('source', 'instagram'))
            ->values();
    }
}
