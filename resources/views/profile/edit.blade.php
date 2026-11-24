@extends('auth.layout')
@section('title', 'Edit Profil')

@section('content')
    <style>
        .avatar-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 0 0 20px; }
        .av { position: relative; display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer; }
        .av input { position: absolute; opacity: 0; pointer-events: none; }
        .av img { width: 100%; max-width: 68px; aspect-ratio: 1; border-radius: 50%; display: block; border: 2px solid rgba(47, 74, 47, .15); }
        .av input:checked + img { outline: 4px solid #557a46; outline-offset: 2px; }
        .av-nama { font-size: .68rem; font-weight: 600; color: #2f4a2f; line-height: 1.2; }
        .av.terkunci { cursor: not-allowed; }
        .av.terkunci img { filter: grayscale(1); opacity: .4; }
        .av-kunci { font-size: .65rem; font-weight: 700; color: #7a6a3a; }
        .info-level { font-size: .8rem; margin: -10px 0 14px; color: #4a5a40; }
    </style>

    <h1 class="title">Edit Profil</h1>
    <p class="sub">Ubah data akunmu di sini.</p>

    @if (session('status'))
        <p style="background:#dcebd2; color:#2f4a2f; border-radius:12px; padding:10px; font-size:.85rem; margin:0 0 18px;">
            {{ session('status') }}
        </p>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="error" style="margin-bottom:14px">
                @foreach ($errors->all() as $e)
                    <p>• {{ $e }}</p>
                @endforeach
            </div>
        @endif

        {{-- Pilihan avatar --}}
        <p class="sub" style="margin:0 auto 12px;">Pilih avatarmu</p>
        <div class="avatar-grid">
            @foreach (config('avatars') as $kunci => $a)
                @php $terbuka = $user->level >= $a['level']; @endphp
                <label class="av {{ $terbuka ? '' : 'terkunci' }}"
                       title="{{ $a['nama'] }}{{ $terbuka ? '' : ' (terbuka di Level ' . $a['level'] . ')' }}">
                    <input type="radio" name="avatar" value="{{ $kunci }}"
                           {{ old('avatar', $user->avatar ?? 'rubah') === $kunci ? 'checked' : '' }}
                           {{ $terbuka ? '' : 'disabled' }}>
                    <img src="{{ asset('avatar/' . $kunci . '.svg') }}" alt="{{ $a['nama'] }}">
                    <span class="av-nama">{{ $a['nama'] }}</span>
                    @unless ($terbuka)
                        <span class="av-kunci">🔒 Level {{ $a['level'] }}</span>
                    @endunless
                </label>
            @endforeach
        </div>
        <p class="info-level">Kamu sekarang di Level {{ $user->level }}. Naik level untuk membuka avatar baru! ⭐</p>

        <div class="field">
            <input type="text" name="name" placeholder="Nama lengkap"
                   value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="field">
            <input type="text" name="username" placeholder="Username"
                   value="{{ old('username', $user->username) }}" required>
        </div>

        <p class="sub" style="margin:22px auto 12px;">Ganti password (kosongkan jika tidak ingin mengubah)</p>

        <div class="field">
            <input type="password" name="current_password" placeholder="Password lama" autocomplete="current-password">
        </div>

        <div class="field">
            <input type="password" name="password" placeholder="Password baru" autocomplete="new-password">
        </div>

        <div class="field">
            <input type="password" name="password_confirmation" placeholder="Ulangi password baru" autocomplete="new-password">
        </div>

        <div style="height:14px"></div>
        <button type="submit">Simpan</button>
    </form>

    <p class="alt"><a href="{{ route('classes.index') }}">← Kembali ke pilih kelas</a></p>
@endsection