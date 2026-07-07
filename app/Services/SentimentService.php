<?php

namespace App\Services;

use App\Services\Sentiment\SentimentAnalyzer;

class SentimentService
{
    public function __construct(private readonly SentimentAnalyzer $analyzer)
    {
    }

    public function analyze(string $text): string
    {
        return $this->analyzer->analyze($text);
    }
}
