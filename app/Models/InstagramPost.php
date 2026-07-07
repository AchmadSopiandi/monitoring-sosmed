<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstagramPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'instagram_media_id',
        'media_id',
        'caption',
        'permalink',
        'media_type',
        'media_url',
        'thumbnail_url',
        'like_count',
        'comments_count',
        'published_at',
        'posted_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'posted_at' => 'datetime',
        'like_count' => 'integer',
        'comments_count' => 'integer',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(InstagramComment::class);
    }

    public function getTitleAttribute(): string
    {
        return str($this->caption ?: $this->instagram_media_id)->limit(80)->toString();
    }

    public function getDisplayImageAttribute(): ?string
    {
        return match ($this->media_type) {
            'VIDEO' => $this->thumbnail_url ?: $this->media_url,
            'IMAGE', 'CAROUSEL_ALBUM' => $this->media_url ?: $this->thumbnail_url,
            default => $this->media_url ?: $this->thumbnail_url,
        };
    }
}
