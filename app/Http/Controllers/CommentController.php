<?php

namespace App\Http\Controllers;

use App\Models\InstagramComment;
use App\Models\InstagramPost;
use App\Models\Sentiment;
use App\Models\TweetComment;
use App\Repositories\InstagramCommentRepository;
use App\Repositories\TweetCommentRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function __construct(
        private readonly InstagramCommentRepository $instagramComments,
        private readonly TweetCommentRepository $tweetComments,
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
            'source' => ['nullable', 'in:instagram,twitter'],
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
        $source = $request->validate(['source' => ['nullable', 'in:instagram,twitter']])['source'] ?? 'instagram';
        $comment = $source === 'twitter'
            ? TweetComment::query()->with(['tweet', 'sentiment'])->findOrFail($comment)
            : InstagramComment::query()->with(['post', 'sentiment'])->findOrFail($comment);

        return view('comments.show', compact('comment'));
    }

    private function paginateComments(array $filters, Request $request): LengthAwarePaginator
    {
        $source = $filters['source'] ?? (! empty($filters['post_id']) ? 'instagram' : null);
        $comments = collect();

        if ($source !== 'twitter') {
            $comments = $comments->merge(
                $this->instagramComments->filteredQuery($filters)->latest('commented_at')->latest('id')->get()
                    ->each(fn (InstagramComment $comment) => $comment->setAttribute('source', 'instagram'))
            );
        }

        if ($source !== 'instagram') {
            $twitterFilters = $filters;
            unset($twitterFilters['post_id']);

            $comments = $comments->merge(
                $this->tweetComments->filteredQuery($twitterFilters)->latest('commented_at')->latest('id')->get()
                    ->each(fn (TweetComment $comment) => $comment->setAttribute('source', 'twitter'))
            );
        }

        $comments = $comments->sortByDesc(
            fn (InstagramComment|TweetComment $comment) => $comment->commented_at ?? $comment->created_time ?? $comment->created_at
        )->values();

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
