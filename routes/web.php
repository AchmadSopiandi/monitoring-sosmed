<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstagramPostController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\ApiSettingController;

Route::get('/assets/login-pattern.png', function () {
    return response()->file(resource_path('image/login-pattern.png'), [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->name('login.pattern');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/instagram/posts', [InstagramPostController::class, 'index'])->name('instagram.posts.index');
    Route::post('/instagram/posts/sync', [InstagramPostController::class, 'syncPosts'])->name('instagram.posts.sync');
    Route::get('/instagram/posts/{instagramPost}', [InstagramPostController::class, 'show'])->name('instagram.posts.show');
    Route::post('/instagram/posts/{instagramPost}/comments/sync', [InstagramPostController::class, 'syncComments'])->name('instagram.posts.comments.sync');
    Route::get('/twitter/tweets', [TwitterController::class, 'index'])->name('twitter.tweets.index');
    Route::post('/twitter/tweets/sync', [TwitterController::class, 'syncTweets'])->name('twitter.tweets.sync');
    Route::get('/twitter/tweets/{tweet}', [TwitterController::class, 'show'])->name('twitter.tweets.show');
    Route::post('/twitter/tweets/{tweet}/replies/sync', [TwitterController::class, 'syncReplies'])->name('twitter.tweets.replies.sync');
    Route::get('/settings/api', [ApiSettingController::class, 'index'])->name('settings.api.index');
    Route::put('/settings/api', [ApiSettingController::class, 'update'])->name('settings.api.update');
    Route::post('/settings/api/test', [ApiSettingController::class, 'test'])->name('settings.api.test');
    Route::resource('comments', CommentController::class)->only(['index', 'show']);
    Route::get('/reports/export/excel', [ReportExportController::class, 'excel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportExportController::class, 'pdf'])->name('reports.export.pdf');
    Route::get('/reports/twitter/export/excel', [ReportExportController::class, 'twitterExcel'])->name('reports.twitter.export.excel');
    Route::get('/reports/twitter/export/pdf', [ReportExportController::class, 'twitterPdf'])->name('reports.twitter.export.pdf');
});
