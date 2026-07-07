@extends('layouts.app')

@section('title', 'Tambah Komentar')

@section('content')
    <div class="page-header">
        <div>
            <h1>Tambah Komentar</h1>
            <p class="subtitle">Input komentar Instagram baru.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <section class="panel">
        <form class="form" action="{{ route('comments.store') }}" method="POST">
            @include('comments.partials.form', ['submitLabel' => 'Simpan'])
        </form>
    </section>
@endsection
