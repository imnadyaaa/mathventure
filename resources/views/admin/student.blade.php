@extends('admin.layout')
@section('title', $siswa->name)

@section('content')
    <div class="space">
        <div>
            <h1>{{ $siswa->name }}</h1>
            <p class="sub" style="margin:0">{{ '@' . $siswa->username }}</p>
        </div>
        <a class="btn abu" href="{{ route('admin.students.index') }}">← Kembali</a>
    </div>

    <div class="grid">
        <div class="stat"><b>{{ $siswa->level }}</b><span>Level pemain</span></div>
        <div class="stat"><b>{{ $siswa->xp }}</b><span>XP</span></div>
        <div class="stat"><b>{{ $siswa->kelas ?? '-' }}</b><span>Kelas di chat</span></div>
    </div>

    <div class="card">
        <div class="row">
            <form method="POST" action="{{ route('admin.students.moderator', $siswa) }}" class="inline">
                @csrf
                <button type="submit" class="btn kecil abu">
                    {{ $siswa->is_moderator ? 'Cabut moderator chat' : 'Jadikan moderator chat' }}
                </button>
            </form>
            @if ($siswa->muted_until && $siswa->muted_until->isFuture())
                <span class="tag merah">Dibisukan sampai {{ $siswa->muted_until->format('d M H:i') }}</span>
                <form method="POST" action="{{ route('admin.students.unmute', $siswa) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn kecil">Buka bisu</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card scroll">
        <strong>Nilai kuis</strong>
        <table style="margin-top:8px">
            <thead>
                <tr><th>Kelas</th><th>Level</th><th>Skor</th><th>Bintang</th><th>Benar</th><th>Status</th><th>Terakhir</th></tr>
            </thead>
            <tbody>
                @forelse ($nilai as $n)
                    <tr>
                        <td>{{ $n->nama_kelas }}</td>
                        <td>{{ $n->level_number }}. {{ $n->nama_level }}</td>
                        <td>{{ $n->score }}</td>
                        <td>{{ str_repeat('★', (int) $n->stars) ?: '-' }}</td>
                        <td>{{ $n->correct_count }}</td>
                        <td>
                            @if ($n->passed) <span class="tag">Lulus</span> @else <span class="tag merah">Belum lulus</span> @endif
                        </td>
                        <td>{{ \Illuminate\Support\Carbon::parse($n->updated_at)->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="kosong">Siswa ini belum mengerjakan kuis.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
