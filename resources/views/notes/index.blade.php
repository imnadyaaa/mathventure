<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catatan - Mathventure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0; min-height: 100vh;
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            color: #2f4a2f;
            /* SAMAKAN path gambar ini dengan yang dipakai di auth/layout.blade.php */
            background: linear-gradient(rgba(0,0,0,.3), rgba(0,0,0,.3)),
                        url('{{ asset('images/background.jpg') }}') center/cover fixed;
        }
        .page { max-width: 980px; margin: 0 auto; padding: 24px 20px 60px; }

        /* Bar atas */
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 22px; }
        .topbar h1 { margin: 0; color: #fff; font-size: 1.6rem; text-shadow: 0 2px 8px rgba(0,0,0,.6); }
        .btn-back {
            background: #f7f0d8; color: #2f4a2f; text-decoration: none;
            font-weight: 700; padding: 11px 20px; border-radius: 999px;
            box-shadow: 0 4px 12px rgba(0,0,0,.25); white-space: nowrap;
        }
        .spacer { width: 110px; }

        /* Pesan */
        .alert { border-radius: 14px; padding: 12px 16px; margin-bottom: 16px; font-size: .9rem; font-weight: 600; }
        .alert.ok  { background: #dcebd2; color: #2f4a2f; }
        .alert.err { background: #fde3e1; color: #b3261e; }
        .alert.err p { margin: 2px 0; }

        /* Kartu form tambah */
        .card {
            background: rgba(247, 240, 216, .95);
            border-radius: 22px; padding: 20px; margin-bottom: 26px;
            box-shadow: 0 8px 24px rgba(0,0,0,.3);
            display: flex; flex-direction: column; gap: 12px;
        }
        input[type=text], textarea {
            width: 100%; background: #fff; color: #2f4a2f;
            border: 2px solid #d9d2b0; border-radius: 12px;
            font-family: inherit; font-size: .95rem; padding: 11px 14px; outline: none;
        }
        textarea { resize: vertical; min-height: 96px; }
        input[type=text]:focus, textarea:focus { border-color: #557a46; }
        .row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; }

        .btn {
            background: #557a46; color: #fff; border: none; border-radius: 999px;
            padding: 11px 24px; font-family: inherit; font-size: .9rem; font-weight: 700;
            cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,.25);
        }
        .btn:hover { background: #466a39; }

        /* Pilihan warna */
        .colors { display: flex; gap: 10px; align-items: center; font-size: .85rem; font-weight: 600; }
        .colors label { position: relative; cursor: pointer; line-height: 0; }
        .colors input { position: absolute; opacity: 0; pointer-events: none; }
        .swatch { display: inline-block; width: 28px; height: 28px; border-radius: 50%; border: 2px solid rgba(47,74,47,.35); }
        .colors input:checked + .swatch { outline: 3px solid #557a46; outline-offset: 2px; }

        .c-krem   { background: #f7f0d8; }
        .c-hijau  { background: #dcebd2; }
        .c-kuning { background: #fff1b5; }
        .c-biru   { background: #d9eaf7; }

        /* Daftar catatan */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 18px; }
        .note {
            border-radius: 18px; padding: 18px;
            box-shadow: 0 6px 18px rgba(0,0,0,.28);
            display: flex; flex-direction: column; gap: 8px;
        }
        .note h3 { margin: 0; font-size: 1.05rem; word-break: break-word; }
        .note .body { margin: 0; white-space: pre-line; word-break: break-word; font-size: .92rem; line-height: 1.55; flex: 1; }
        .note small { opacity: .65; }

        .actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-start; margin-top: 6px; }
        details.edit { flex: 0 1 auto; }
        details.edit[open] { flex: 1 1 100%; }
        details.edit summary {
            display: inline-block; list-style: none; cursor: pointer;
            background: rgba(255,255,255,.75); padding: 8px 16px;
            border-radius: 999px; font-weight: 700; font-size: .85rem;
        }
        details.edit summary::-webkit-details-marker { display: none; }
        details.edit form { margin-top: 12px; display: flex; flex-direction: column; gap: 10px; }
        .btn-del {
            background: rgba(255,255,255,.75); color: #b3261e; border: none; border-radius: 999px;
            padding: 8px 16px; font-family: inherit; font-size: .85rem; font-weight: 700; cursor: pointer;
        }
        .btn-del:hover { background: #fde3e1; }

        .empty {
            background: rgba(247,240,216,.95); border-radius: 22px; padding: 36px 20px;
            text-align: center; font-weight: 600; box-shadow: 0 8px 24px rgba(0,0,0,.3);
        }
    </style>
</head>
<body>
@php
    $isNew = old('_form') === 'new';
@endphp

<div class="page">
    <div class="topbar">
        <a href="{{ route('classes.index') }}" class="btn-back">← Kembali</a>
        <h1>📝 Catatan Saya</h1>
        <span class="spacer"></span>
    </div>

    @if (session('status'))
        <div class="alert ok">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert err">
            @foreach ($errors->all() as $e)
                <p>• {{ $e }}</p>
            @endforeach
        </div>
    @endif

    {{-- Form catatan baru --}}
    <form class="card" method="POST" action="{{ route('notes.store') }}">
        @csrf
        <input type="hidden" name="_form" value="new">

        <input type="text" name="title" maxlength="100" required
               placeholder="Judul catatan (misalnya: Rumus luas lingkaran)"
               value="{{ $isNew ? old('title') : '' }}">

        <textarea name="body" maxlength="2000" required
                  placeholder="Tulis catatanmu di sini...">{{ $isNew ? old('body') : '' }}</textarea>

        <div class="row">
            <div class="colors">
                Warna:
                @foreach ($colors as $key => $label)
                    <label title="{{ $label }}">
                        <input type="radio" name="color" value="{{ $key }}"
                               {{ ($isNew ? old('color', 'krem') : 'krem') === $key ? 'checked' : '' }}>
                        <span class="swatch c-{{ $key }}"></span>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="btn">+ Simpan Catatan</button>
        </div>
    </form>

    {{-- Daftar catatan --}}
    @if ($notes->isEmpty())
        <div class="empty">Belum ada catatan. Yuk tulis catatan pertamamu! ✨</div>
    @else
        <div class="grid">
            @foreach ($notes as $note)
                @php $editing = old('_form') === 'note-' . $note->id; @endphp

                <article class="note c-{{ $note->color }}">
                    <h3>{{ $note->title }}</h3>
                    <p class="body">{{ $note->body }}</p>
                    <small>Diperbarui {{ $note->updated_at->format('d/m/Y H:i') }}</small>

                    <div class="actions">
                        <details class="edit" {{ $editing ? 'open' : '' }}>
                            <summary data-sfx="buka">✏️ Edit</summary>

                            <form method="POST" action="{{ route('notes.update', $note) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_form" value="note-{{ $note->id }}">

                                <input type="text" name="title" maxlength="100" required
                                       value="{{ $editing ? old('title') : $note->title }}">

                                <textarea name="body" maxlength="2000" required>{{ $editing ? old('body') : $note->body }}</textarea>

                                <div class="row">
                                    <div class="colors">
                                        @foreach ($colors as $key => $label)
                                            <label title="{{ $label }}">
                                                <input type="radio" name="color" value="{{ $key }}"
                                                       {{ ($editing ? old('color') : $note->color) === $key ? 'checked' : '' }}>
                                                <span class="swatch c-{{ $key }}"></span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <button type="submit" class="btn">Simpan perubahan</button>
                                </div>
                            </form>
                        </details>

                        <form method="POST" action="{{ route('notes.destroy', $note) }}"
                              onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-del">🗑️ Hapus</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>

<script src="{{ asset('js/sfx.js') }}"></script>
</body>
</html>