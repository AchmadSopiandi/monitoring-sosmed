<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'key',
        'value',
        'instagram_token',
        'instagram_user_id',
        'facebook_page_id',
        'twitter_bearer_token',
        'twitter_api_key',
        'twitter_api_secret',
        'twitter_client_id',
        'twitter_client_secret',
        'is_encrypted',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];
}
