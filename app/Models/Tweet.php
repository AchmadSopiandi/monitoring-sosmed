<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'tweet_id',
        'text',
        'tweet',
        'author_username',
        'author',
        'reply_count',
        'like_count',
        'repost_count',
        'permalink',
        'published_at',
        'posted_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'posted_at' => 'datetime',
        'reply_count' => 'integer',
        'like_count' => 'integer',
        'repost_count' => 'integer',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(TweetComment::class);
    }

    public function getDisplayTextAttribute(): ?string
    {
        return $this->tweet ?: $this->text;
    }
}
