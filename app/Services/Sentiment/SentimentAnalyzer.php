<?php

namespace App\Services\Sentiment;

use App\Models\Sentiment;
use Illuminate\Support\Str;

class SentimentAnalyzer
{
    private const POSITIVE_KEYWORDS = [
        'bagus', 'baik', 'mantap', 'cepat', 'puas', 'keren', 'terbantu',
        'membantu', 'mudah', 'jelas', 'ramah', 'responsif', 'respon cepat',
        'lancar', 'hebat', 'suka', 'makasih', 'terima kasih', 'thanks',
        'thank you', 'bermanfaat', 'sukses', 'rekomendasi', 'recommended',
        'top', 'the best', 'best', 'love',
    ];

    private const NEGATIVE_KEYWORDS = [
        'buruk', 'jelek', 'lambat', 'lama', 'error', 'gagal', 'susah',
        'sulit', 'kecewa', 'bingung', 'ribet', 'tidak jelas', 'ga jelas',
        'gak jelas', 'nggak jelas', 'parah', 'komplain', 'masalah', 'macet',
        'down', 'kurang', 'mahal', 'bohong', 'hoax', 'penipuan', 'tipu',
        'menipu', 'sampah', 'aneh', 'payah', 'bodoh', 'bego', 'busuk',
        'najis', 'benci', 'hate',
    ];

    private const NEGATIONS = [
        'tidak', 'tak', 'bukan', 'belum', 'kurang', 'ga', 'gak', 'nggak',
        'ngga', 'engga', 'enggak',
    ];

    public function analyze(string $comment): string
    {
        $comment = $this->normalizeText($comment);
        $score = 0;

        foreach (self::POSITIVE_KEYWORDS as $keyword) {
            if ($this->containsKeyword($comment, $keyword)) {
                $score += $this->hasNegationBefore($comment, $keyword) ? -1 : 1;
            }
        }

        foreach (self::NEGATIVE_KEYWORDS as $keyword) {
            if ($this->containsKeyword($comment, $keyword)) {
                $score -= $this->hasNegationBefore($comment, $keyword) ? -1 : 1;
            }
        }

        return match (true) {
            $score > 0 => Sentiment::POSITIVE,
            $score < 0 => Sentiment::NEGATIVE,
            default => Sentiment::NEUTRAL,
        };
    }

    private function normalizeText(string $text): string
    {
        $text = Str::lower($text);
        $text = preg_replace('/[^\pL\pN\s]+/u', ' ', $text) ?? $text;

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    private function containsKeyword(string $comment, string $keyword): bool
    {
        $keyword = preg_quote($this->normalizeText($keyword), '/');

        return (bool) preg_match("/(^|\s){$keyword}(\s|$)/u", $comment);
    }

    private function hasNegationBefore(string $comment, string $keyword): bool
    {
        $words = explode(' ', $comment);
        $keywordWords = explode(' ', $this->normalizeText($keyword));
        $keywordLength = count($keywordWords);

        for ($index = 0; $index <= count($words) - $keywordLength; $index++) {
            if (array_slice($words, $index, $keywordLength) !== $keywordWords) {
                continue;
            }

            $before = array_slice($words, max(0, $index - 3), min(3, $index));

            return count(array_intersect($before, self::NEGATIONS)) > 0;
        }

        return false;
    }
}
