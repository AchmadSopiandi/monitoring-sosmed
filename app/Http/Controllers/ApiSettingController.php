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
        return back()->with('success', implode(' ', $messages));
    }
}
