@extends('admin.layout')
@section('title', 'Materi')

@section('content')
    <div class="space">
        <div>
            <h1>Materi: {{ $level->schoolClass->name }}, Level {{ $level->level_number }} ({{ $level->name }})</h1>
            <p class="sub" style="margin:0">Materi ini tampil di awal kuis level tersebut.</p>
        </div>
        <a class="btn abu" href="{{ route('admin.levels') }}">← Kembali</a>
    </div>

    <form class="card" method="POST" action="{{ route('admin.materials.update', $level) }}">
        @csrf
        @method('PUT')

        <label for="title">Judul materi</label>
        <input type="text" id="title" name="title" value="{{ old('title', $material->title) }}">

        <label for="content">Isi materi</label>
        <textarea id="content" name="content" style="min-height:320px">{{ old('content', $material->content) }}</textarea>
        <p class="hint">Baris baru dan tanda • akan tampil apa adanya.</p>

        <div class="row" style="margin-top:18px">
            <button type="submit" class="btn">Simpan materi</button>
            <a class="btn abu" href="{{ route('admin.levels') }}">Batal</a>
        </div>
    </form>
@endsection
