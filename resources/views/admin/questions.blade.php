@extends('admin.layout')
@section('title', 'Soal')

@section('content')
    <div class="space">
        <div>
            <h1>Soal: {{ $level->schoolClass->name }}, Level {{ $level->level_number }} ({{ $level->name }})</h1>
            <p class="sub" style="margin:0">{{ $soal->count() }} soal</p>
        </div>
        <div class="row">
            <a class="btn abu" href="{{ route('admin.levels') }}">← Kembali</a>
            <a class="btn abu" href="{{ route('admin.import.form', $level) }}">Impor CSV</a>
            <a class="btn" href="{{ route('admin.questions.create', $level) }}">+ Tambah soal</a>
        </div>
    </div>

    <div class="card scroll">
        <table>
            <thead>
                <tr><th>#</th><th>Jenis</th><th>Pertanyaan</th><th>Jawaban</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($soal as $i => $q)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><span class="tag">{{ $tipeNama[$q->type] ?? $q->type }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit($q->question, 90) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($q->answer, 30) }}</td>
                        <td class="row">
                            <a class="btn kecil abu" href="{{ route('admin.questions.edit', $q) }}">Ubah</a>
                            <form method="POST" action="{{ route('admin.questions.destroy', $q) }}" class="inline"
                                  onsubmit="return confirm('Hapus soal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn kecil merah">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="kosong">Belum ada soal di level ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
