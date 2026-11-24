@extends('admin.layout')
@section('title', 'Siswa')

@section('content')
    <div class="space">
        <div>
            <h1>Siswa</h1>
            <p class="sub" style="margin:0">Diurutkan dari XP tertinggi.</p>
        </div>
        <form method="GET" action="{{ route('admin.students.index') }}" class="row">
            <input type="search" name="q" value="{{ $cari }}" placeholder="Cari nama atau username" style="width:240px">
            <button type="submit" class="btn">Cari</button>
        </form>
    </div>

    <div class="card">
        <div class="row">
            <strong>Ekspor nilai semua siswa:</strong>
            <a class="btn kecil" href="{{ route('admin.students.export') }}">Unduh CSV (Excel Indonesia)</a>
            <a class="btn kecil abu" href="{{ route('admin.students.export', ['pemisah' => 'koma']) }}">Unduh CSV (pemisah koma)</a>
        </div>
        <p class="hint">Kalau di Excel semua data menumpuk di satu kolom, pakai tombol satunya lagi.</p>
    </div>

    <div class="card scroll">
        <table>
            <thead>
                <tr><th>Nama</th><th>Kelas</th><th>Level</th><th>XP</th><th>Level lulus</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($siswa as $s)
                    <tr>
                        <td>
                            {{ $s->name }}<br>
                            <small>{{ '@' . $s->username }}</small>
                            @if ($s->is_moderator) <span class="tag">Moderator</span> @endif
                            @if ($s->muted_until && $s->muted_until->isFuture()) <span class="tag merah">Dibisukan</span> @endif
                        </td>
                        <td>{{ $s->kelas ?? '-' }}</td>
                        <td>{{ $s->level }}</td>
                        <td>{{ $s->xp }}</td>
                        <td>{{ $lulus[$s->id] ?? 0 }}</td>
                        <td><a class="btn kecil abu" href="{{ route('admin.students.show', $s) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="kosong">Tidak ada siswa{{ $cari !== '' ? ' yang cocok' : '' }}.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($siswa->hasPages())
            <div class="row" style="justify-content:center; margin-top:14px">
                @if ($siswa->previousPageUrl())
                    <a class="btn kecil abu" href="{{ $siswa->previousPageUrl() }}">← Sebelumnya</a>
                @endif
                <span>Halaman {{ $siswa->currentPage() }} dari {{ $siswa->lastPage() }}</span>
                @if ($siswa->nextPageUrl())
                    <a class="btn kecil abu" href="{{ $siswa->nextPageUrl() }}">Berikutnya →</a>
                @endif
            </div>
        @endif
    </div>
@endsection
