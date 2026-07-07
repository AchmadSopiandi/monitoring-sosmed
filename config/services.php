<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'instagram' => [
        'access_token' => env('INSTAGRAM_ACCESS_TOKEN'),
        'user_id' => env('INSTAGRAM_USER_ID'),
        'graph_version' => env('INSTAGRAM_GRAPH_VERSION', 'v23.0'),
        'base_url' => env('INSTAGRAM_GRAPH_BASE_URL', 'https://graph.facebook.com'),
        'media_limit' => env('INSTAGRAM_MEDIA_LIMIT', 25),
        'comments_limit' => env('INSTAGRAM_COMMENTS_LIMIT', 50),
    ],

    'twitter' => [
        'bearer_token' => env('TWITTER_BEARER_TOKEN'),
        'user_id' => env('TWITTER_USER_ID'),
        'base_url' => env('TWITTER_API_BASE_URL', 'https://api.twitter.com/2'),
        'tweet_limit' => env('TWITTER_TWEET_LIMIT', 25),
        'reply_limit' => env('TWITTER_REPLY_LIMIT', 50),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
