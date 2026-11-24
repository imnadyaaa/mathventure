@extends('admin.layout')
@section('title', 'Laporan Chat')

@section('content')
    <h1>Laporan Chat</h1>
    <p class="sub">Pesan yang dilaporkan siswa atau sudah disembunyikan (100 terbaru).</p>

    @if ($dibisukan->isNotEmpty())
        <div class="card">
            <strong>Sedang dibisukan</strong>
            <table style="margin-top:8px">
                <tbody>
                    @foreach ($dibisukan as $u)
                        <tr>
                            <td>{{ $u->name }} <small>{{ '@' . $u->username }}</small></td>
                            <td>sampai {{ $u->muted_until->format('d M H:i') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.students.unmute', $u) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn kecil">Buka bisu</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="card scroll">
        <table>
            <thead>
                <tr><th>Pengirim</th><th>Pesan</th><th>Ruang</th><th>Laporan</th><th>Status</th><th>Tindakan</th></tr>
            </thead>
            <tbody>
                @forelse ($pesan as $p)
                    <tr>
                        <td>
                            {{ $p->user->name }}<br>
                            <small>{{ $p->created_at->format('d M H:i') }}</small>
                        </td>
                        <td style="max-width:280px; word-break:break-word">{{ $p->body }}</td>
                        <td>{{ strtoupper($p->room) }}</td>
                        <td>{{ $laporan[$p->id] ?? 0 }}</td>
                        <td>
                            @if ($p->hidden) <span class="tag merah">Disembunyikan</span> @else <span class="tag kuning">Tampil</span> @endif
                        </td>
                        <td>
                            <div class="row">
                                <form method="POST" action="{{ route('admin.chat.toggle', $p) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn kecil abu">{{ $p->hidden ? 'Tampilkan' : 'Sembunyikan' }}</button>
                                </form>
                                @if (($laporan[$p->id] ?? 0) > 0)
                                    <form method="POST" action="{{ route('admin.chat.dismiss', $p) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="btn kecil abu">Abaikan laporan</button>
                                    </form>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('admin.chat.mute', $p) }}" class="row" style="margin-top:6px">
                                @csrf
                                <select name="menit" style="width:auto; padding:5px 8px">
                                    <option value="10">10 menit</option>
                                    <option value="60">1 jam</option>
                                    <option value="1440">1 hari</option>
                                    <option value="10080">7 hari</option>
                                </select>
                                <button type="submit" class="btn kecil merah">Bisukan pengirim</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="kosong">Belum ada pesan yang dilaporkan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
