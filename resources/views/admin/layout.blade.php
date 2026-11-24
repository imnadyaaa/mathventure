<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Mathventure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Poppins', 'Segoe UI', Arial, sans-serif; background: #f3efdc; color: #2f4a2f; }
        a { color: #3d6b3d; }
        .nav { background: #2f4a2f; display: flex; flex-wrap: wrap; align-items: center; gap: 6px; padding: 10px 18px; }
        .nav .brand { color: #f7f0d8; font-weight: 700; margin-right: 14px; }
        .nav a, .nav button { color: #f7f0d8; text-decoration: none; font: inherit; font-size: .9rem; padding: 7px 14px; border-radius: 999px; background: transparent; border: 0; cursor: pointer; }
        .nav a.aktif, .nav a:hover, .nav button:hover { background: rgba(255,255,255,.18); }
        .nav .kanan { margin-left: auto; display: flex; gap: 6px; align-items: center; }
        .wrap { max-width: 1000px; margin: 0 auto; padding: 24px 18px 60px; }
        h1 { margin: 0 0 4px; font-size: 1.5rem; }
        .sub { margin: 0 0 20px; color: #5f7a5f; font-size: .9rem; }
        .card { background: #fffdf3; border: 2px solid #e2dbb8; border-radius: 16px; padding: 18px; margin-bottom: 18px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 20px; }
        .stat { background: #fffdf3; border: 2px solid #e2dbb8; border-radius: 16px; padding: 16px; }
        .stat b { display: block; font-size: 1.8rem; }
        .stat span { color: #5f7a5f; font-size: .85rem; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        th, td { text-align: left; padding: 9px 10px; border-bottom: 1px solid #e8e2c4; vertical-align: top; }
        th { color: #5f7a5f; font-size: .8rem; text-transform: uppercase; }
        .scroll { overflow-x: auto; }
        .btn { display: inline-block; background: #3d6b3d; color: #fff; text-decoration: none; border: 0; border-radius: 999px; padding: 8px 16px; font: inherit; font-size: .85rem; font-weight: 600; cursor: pointer; }
        .btn:hover { background: #2f5530; }
        .btn.abu { background: #e2dbb8; color: #2f4a2f; }
        .btn.merah { background: #b3261e; }
        .btn.kecil { padding: 5px 12px; font-size: .8rem; }
        .alert { border-radius: 12px; padding: 11px 15px; margin-bottom: 16px; font-weight: 600; font-size: .9rem; }
        .alert.ok { background: #dcebd2; }
        .alert.err { background: #fde3e1; color: #b3261e; }
        .alert.err p { margin: 2px 0; }
        label { display: block; font-weight: 600; font-size: .88rem; margin: 14px 0 5px; }
        input[type=text], input[type=search], select, textarea { width: 100%; background: #fff; color: #2f4a2f; border: 2px solid #d9d2b0; border-radius: 10px; font: inherit; font-size: .92rem; padding: 9px 12px; }
        textarea { min-height: 110px; resize: vertical; }
        .hint { color: #6f866f; font-size: .8rem; margin: 4px 0 0; }
        .tag { display: inline-block; background: #dcebd2; border-radius: 999px; padding: 2px 10px; font-size: .78rem; font-weight: 600; }
        .tag.merah { background: #fde3e1; color: #b3261e; }
        .tag.kuning { background: #fbefc2; color: #7a5b00; }
        .row { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .space { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
        .kosong { color: #6f866f; text-align: center; padding: 22px 0; }
        form.inline { display: inline; }
    </style>
</head>
<body>
    <nav class="nav">
        <span class="brand">🛠️ Admin Mathventure</span>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'aktif' : '' }}">Ringkasan</a>
        <a href="{{ route('admin.levels') }}" class="{{ request()->routeIs('admin.levels', 'admin.questions.*', 'admin.materials.*') ? 'aktif' : '' }}">Soal &amp; Materi</a>
        <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'aktif' : '' }}">Siswa</a>
        <a href="{{ route('admin.chat.index') }}" class="{{ request()->routeIs('admin.chat.*') ? 'aktif' : '' }}">Laporan Chat</a>
        <div class="kanan">
            <a href="{{ route('classes.index') }}">← Ke aplikasi</a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </div>
    </nav>

    <main class="wrap">
        @if (session('ok'))
            <div class="alert ok">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert err">
                @foreach ($errors->all() as $pesan)
                    <p>{{ $pesan }}</p>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
