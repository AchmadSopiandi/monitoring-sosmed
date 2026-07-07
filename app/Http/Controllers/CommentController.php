<?php

namespace App\Http\Controllers;

use App\Models\InstagramComment;
use App\Models\InstagramPost;
use App\Models\Sentiment;
use App\Repositories\InstagramCommentRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function __construct(private readonly InstagramCommentRepository $comments)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'period' => ['nullable', 'in:today,weekly,monthly,custom'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sentiment' => ['nullable', 'in:Positif,Netral,Negatif'],
            'post_id' => ['nullable', 'exists:instagram_posts,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        return view('comments.index', [
            'comments' => $this->comments->paginate($filters),
            'filters' => $filters,
            'sentiments' => Sentiment::NAMES,
            'posts' => InstagramPost::query()->latest('published_at')->get(),
        ]);
    }

    public function show(InstagramComment $comment): View
    {
        return view('comments.show', compact('comment'));
    }
}
