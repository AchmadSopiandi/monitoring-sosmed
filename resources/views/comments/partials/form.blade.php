@csrf

<label>
    Username
    <input type="text" name="username" value="{{ old('username', $comment->username ?? '') }}" required>
</label>

<label>
    Komentar
    <textarea name="comment" required>{{ old('comment', $comment->comment ?? '') }}</textarea>
</label>

@isset($comment)
    <p><strong>Sentimen saat ini:</strong> {{ $comment->sentiment }}</p>
@endisset

<div class="actions">
    <button type="submit">{{ $submitLabel }}</button>
    <a class="button secondary" href="{{ route('comments.index') }}">Batal</a>
</div>
