<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentiment extends Model
{
    use HasFactory;

    public const POSITIVE = 'Positif';
    public const NEUTRAL = 'Netral';
    public const NEGATIVE = 'Negatif';
    public const NAMES = [self::POSITIVE, self::NEUTRAL, self::NEGATIVE];

    protected $fillable = ['name', 'label', 'color'];
}
