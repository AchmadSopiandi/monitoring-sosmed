<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory;

    public const SENTIMENTS = ['Positif', 'Netral', 'Negatif'];
    private const POSITIVE_KEYWORDS = [
        'bagus',
        'baik',
        'mantap',
        'cepat',
        'puas',
        'keren',
        'terbantu',
        'membantu',
        'mudah',
        'jelas',
        'ramah',
        'responsif',
        'respon cepat',
        'lancar',
        'hebat',
        'suka',
        'makasih',
        'terima kasih',
        'thanks',
        'thank you',
        'bermanfaat',
        'sukses',
        'rekomendasi',
        'recommended',
        'top',
        'the best',
        'best',
        'love',
    ];
    private const NEGATIVE_KEYWORDS = [
        'buruk',
        'jelek',
        'lambat',
        'lama',
        'error',
        'gagal',
        'susah',
        'sulit',
        'kecewa',
        'bingung',
        'ribet',
        'tidak jelas',
        'ga jelas',
        'gak jelas',
        'nggak jelas',
        'parah',
        'komplain',
        'masalah',
        'macet',
        'down',
        'kurang',
        'mahal',
        'bohong',
        'hoax',
        'penipuan',
        'tipu',
        'menipu',
        'sampah',
        'aneh',
        'payah',
        'goblok',
        'gblk',
        'tolol',
        'bodoh',
        'bego',
        'anjing',
        'anjir',
        'anjay',
        'jir',
        'jirr',
        'bangsat',
        'kampret',
        'kontol',
        'memek',
        'ngentot',
        'tai',
        'taik',
        'busuk',
        'najis',
        'benci',
        'hate',
    ];
    private const NEGATIONS = [
        'tidak',
        'tak',
        'bukan',
        'belum',
        'kurang',
        'ga',
        'gak',
        'nggak',
        'ngga',
        'engga',
        'enggak',
    ];

    public const UPDATED_AT = null;

    protected $fillable = [
        'username',
        'comment',
        'instagram_comment_id',
        'instagram_media_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Comment $comment) {
            $comment->sentiment = self::analyzeSentiment($comment->comment);
        });
    }

    public static function analyzeSentiment(string $comment): string
    {
        $comment = self::normalizeText($comment);
        $score = 0;

        foreach (self::POSITIVE_KEYWORDS as $keyword) {
            if (! self::containsKeyword($comment, $keyword)) {
                continue;
            }

            $score += self::hasNegationBefore($comment, $keyword) ? -1 : 1;
        }

        foreach (self::NEGATIVE_KEYWORDS as $keyword) {
            if (! self::containsKeyword($comment, $keyword)) {
                continue;
            }

            $score -= self::hasNegationBefore($comment, $keyword) ? -1 : 1;
        }

        return match (true) {
            $score > 0 => 'Positif',
            $score < 0 => 'Negatif',
            default => 'Netral',
        };
    }

    private static function normalizeText(string $text): string
    {
        $text = Str::lower($text);
        $text = preg_replace('/[^\pL\pN\s]+/u', ' ', $text) ?? $text;

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    private static function containsKeyword(string $comment, string $keyword): bool
    {
        $keyword = preg_quote(self::normalizeText($keyword), '/');

        return (bool) preg_match("/(^|\s){$keyword}(\s|$)/u", $comment);
    }

    private static function hasNegationBefore(string $comment, string $keyword): bool
    {
        $words = explode(' ', $comment);
        $keywordWords = explode(' ', self::normalizeText($keyword));
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
