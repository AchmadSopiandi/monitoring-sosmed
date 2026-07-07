<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstagramComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'instagram_post_id',
        'sentiment_id',
        'instagram_comment_id',
        'comment_id',
        'username',
        'comment',
        'sentiment',
        'created_time',
        'like_count',
        'commented_at',
    ];

    protected $casts = [
        'commented_at' => 'datetime',
        'created_time' => 'datetime',
        'like_count' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(InstagramPost::class, 'instagram_post_id');
    }

    public function sentiment(): BelongsTo
    {
        return $this->belongsTo(Sentiment::class);
    }
}
