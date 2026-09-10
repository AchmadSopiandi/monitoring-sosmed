<?php

namespace App\Http\Controllers;

use App\Models\InstagramComment;
use App\Models\InstagramPost;
use App\Models\Sentiment;
use App\Repositories\InstagramCommentRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function __construct(
        private readonly InstagramCommentRepository $instagramComments,
    )
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'period' => ['nullable', 'in:today,weekly,monthly,custom'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sentiment' => ['nullable', 'in:Positif,Netral,Negatif'],
            'source' => ['nullable', 'in:instagram'],
            'post_id' => ['nullable', 'exists:instagram_posts,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        return view('comments.index', [
            'comments' => $this->paginateComments($filters, $request),
            'filters' => $filters,
            'sentiments' => Sentiment::NAMES,
            'posts' => InstagramPost::query()->latest('published_at')->get(),
        ]);
    }

    public function show(Request $request, int $comment): View
    {
        $request->validate(['source' => ['nullable', 'in:instagram']]);
        $comment = InstagramComment::query()->with(['post', 'sentiment'])->findOrFail($comment);

        return view('comments.show', compact('comment'));
    }

    private function paginateComments(array $filters, Request $request): LengthAwarePaginator
    {
        $comments = $this->instagramComments->filteredQuery($filters)
            ->latest('commented_at')->latest('id')->get()
            ->each(fn (InstagramComment $comment) => $comment->setAttribute('source', 'instagram'));

        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();

        return (new LengthAwarePaginator(
            $comments->forPage($page, $perPage)->values(),
            $comments->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        ))->appends($request->query());
    }
}
