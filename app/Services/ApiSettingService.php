<?php

namespace App\Services;

use App\Models\ApiSetting;

class ApiSettingService
{
    public function current(): ApiSetting
    {
        return ApiSetting::firstOrCreate(
            ['provider' => 'social_monitoring', 'key' => 'default'],
            ['value' => null]
        );
    }

    public function update(array $settings): ApiSetting
    {
        $apiSetting = $this->current();
        $apiSetting->fill($settings);
        $apiSetting->save();

        return $apiSetting;
    }

    public function instagramToken(): ?string
    {
        return $this->current()->instagram_token ?: config('services.instagram.access_token');
    }

    public function instagramUserId(): ?string
    {
        return $this->current()->instagram_user_id ?: config('services.instagram.user_id');
    }

    public function twitterBearerToken(): ?string
    {
        return $this->current()->twitter_bearer_token ?: config('services.twitter.bearer_token');
    }

    public function twitterUserId(): ?string
    {
        return config('services.twitter.user_id');
    }
}
