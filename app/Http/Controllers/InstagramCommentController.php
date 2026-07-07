<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class InstagramCommentController extends Controller
{
    public function sync(): RedirectResponse
    {
        return redirect()
            ->route('instagram.posts.index')
            ->with('error', 'Pilih salah satu postingan Instagram terlebih dahulu sebelum mengambil komentar.');
    }
}
