@extends('admin.layout')
@section('title', 'Soal & Materi')

@section('content')
    <h1>Soal &amp; Materi</h1>
    <p class="sub">Pilih level untuk mengelola soal atau materinya.</p>

    @forelse ($kelas as $k)
        <div class="card">
            <strong style="font-size:1.05rem">{{ $k->name }}</strong>
            <div class="scroll">
                <table style="margin-top:8px">
                    <thead>
                        <tr><th>Level</th><th>Soal</th><th>Materi</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($k->levels as $lv)
                            <tr>
                                <td>{{ $lv->level_number }}. {{ $lv->name }}</td>
                                <td>
                                    @if ($lv->questions_count > 0)
                                        {{ $lv->questions_count }} soal
                                    @else
                                        <span class="tag merah">Belum ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($lv->materials_count > 0)
                                        <span class="tag">Ada</span>
                                    @else
                                        <span class="tag kuning">Belum ada</span>
                                    @endif
                                </td>
                                <td class="row">
                                    <a class="btn kecil" href="{{ route('admin.questions.index', $lv) }}">Soal</a>
                                    <a class="btn kecil abu" href="{{ route('admin.materials.edit', $lv) }}">Materi</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="kosong">Kelas ini belum punya level.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card kosong">Belum ada kelas. Jalankan <code>php artisan db:seed --class=QuizSeeder</code> dulu.</div>
    @endforelse
@endsection
