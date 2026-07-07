<?php

namespace App\Http\Controllers;

use App\Services\ApiSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiSettingController extends Controller
{
    public function index(ApiSettingService $settings): View
    {
        return view('settings.api', ['settings' => $settings->current()]);
    }

    public function update(Request $request, ApiSettingService $settings): RedirectResponse
    {
        $validated = $request->validate([
            'instagram_token' => ['nullable', 'string'],
            'instagram_user_id' => ['nullable', 'string', 'max:255'],
            'facebook_page_id' => ['nullable', 'string', 'max:255'],
            'twitter_bearer_token' => ['nullable', 'string'],
            'twitter_api_key' => ['nullable', 'string', 'max:255'],
            'twitter_api_secret' => ['nullable', 'string'],
            'twitter_client_id' => ['nullable', 'string', 'max:255'],
            'twitter_client_secret' => ['nullable', 'string'],
        ]);

        $settings->update($validated);

        return back()->with('success', 'Pengaturan API berhasil disimpan.');
    }

    public function test(ApiSettingService $settings): RedirectResponse
    {
        $messages = [];
        $messages[] = $settings->instagramToken() && $settings->instagramUserId()
            ? 'Instagram siap dites.'
            : 'Instagram belum lengkap.';
        $messages[] = $settings->twitterBearerToken()
            ? 'Twitter/X siap dites.'
            : 'Twitter/X belum lengkap.';

        return back()->with('success', implode(' ', $messages));
    }
}
