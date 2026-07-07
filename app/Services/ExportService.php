<?php

namespace App\Services;

use App\Models\Sentiment;

class ExportService
{
    public function sentimentName(object $comment): string
    {
        return $comment->getRawOriginal('sentiment') ?: Sentiment::NEUTRAL;
    }
}
