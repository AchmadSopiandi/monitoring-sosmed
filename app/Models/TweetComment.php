<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TweetComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tweet_id',
        'sentiment_id',
        'tweet_reply_id',
        'reply_id',
        'username',
        'comment',
        'reply',
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

    public function tweet(): BelongsTo
    {
        return $this->belongsTo(Tweet::class);
    }

    public function sentiment(): BelongsTo
    {
        return $this->belongsTo(Sentiment::class);
    }
}
