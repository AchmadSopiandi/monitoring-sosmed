@extends('layouts.app')

@section('title', 'Pengaturan API')

@section('content')
    <div class="page-header">
        <div>
            <h1>Pengaturan API</h1>
            <p class="subtitle">Token dapat disimpan di database dan tetap fallback ke file .env.</p>
        </div>
        <div class="actions">
            <form action="{{ route('settings.api.test') }}" method="POST">
                @csrf
                <button type="submit">Tes Koneksi</button>
            </form>
        </div>
    </div>

    <form class="panel stack" action="{{ route('settings.api.update') }}" method="POST">
        @csrf
        @method('PUT')

        <h1>Instagram</h1>
        <label>
            Access Token
            <textarea name="instagram_token">{{ old('instagram_token', $settings->instagram_token) }}</textarea>
        </label>
        <label>
            Instagram User ID
            <input type="text" name="instagram_user_id" value="{{ old('instagram_user_id', $settings->instagram_user_id) }}">
        </label>
        <label>
            Facebook Page ID
            <input type="text" name="facebook_page_id" value="{{ old('facebook_page_id', $settings->facebook_page_id) }}">
        </label>

        <h1>Twitter/X</h1>
        <label>
            Bearer Token
            <textarea name="twitter_bearer_token">{{ old('twitter_bearer_token', $settings->twitter_bearer_token) }}</textarea>
        </label>
        <label>
            API Key
            <input type="text" name="twitter_api_key" value="{{ old('twitter_api_key', $settings->twitter_api_key) }}">
        </label>
        <label>
            API Secret
            <textarea name="twitter_api_secret">{{ old('twitter_api_secret', $settings->twitter_api_secret) }}</textarea>
        </label>
        <label>
            Client ID
            <input type="text" name="twitter_client_id" value="{{ old('twitter_client_id', $settings->twitter_client_id) }}">
        </label>
        <label>
            Client Secret
            <textarea name="twitter_client_secret">{{ old('twitter_client_secret', $settings->twitter_client_secret) }}</textarea>
        </label>

        <div class="actions">
            <button type="submit">Simpan</button>
        </div>
    </form>
@endsection
