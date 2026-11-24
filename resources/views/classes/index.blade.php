<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mathventure - Pilih Kelas</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            background-image:
                linear-gradient(rgba(20, 45, 25, 0.25), rgba(20, 45, 25, 0.25)),
                url('/image/forest-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #263b26;
        }

        .page {
            min-height: 100vh;
            padding: 30px 50px;
            position: relative;
        }

        /* =========================
           HEADER
        ========================= */

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 28px;
            font-weight: bold;
            background: rgba(40, 70, 45, 0.85);
            padding: 12px 24px;
            border-radius: 15px;
        }

        .notes-button {
            background: rgb(244, 234, 210, 0.95);
            color: #344d35;
            padding: 15px 25px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .notes-button:hover {
            background: #fff5dc;
        }

        /* =========================
           JUDUL
        ========================= */

        .welcome {
            text-align: center;
            margin-top: 70px;
            margin-bottom: 40px;
        }

        .welcome h1 {
            font-size: 42px;
            color: white;
            text-shadow: 2px 3px 5px rgba(0, 0, 0, 0.5);
        }

        .welcome p {
            margin-top: 12px;
            font-size: 18px;
            color: white;
            text-shadow: 1px 2px 3px rgba(0, 0, 0, 0.5);
        }

        /* =========================
           KELAS
        ========================= */

        .classes {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .class-card {
            width: 280px;
            padding: 35px 25px;
            background: rgba(250, 243, 220, 0.95);
            border-radius: 22px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            transition: 0.3s;
        }

        .class-card:hover {
            transform: translateY(-8px);
        }

        .class-card h2 {
            font-size: 28px;
            margin-bottom: 25px;
            color: #304b32;
        }

        .class-button {
            display: inline-block;
            padding: 12px 30px;
            background: #557744;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
        }

        .class-button:hover {
            background: #3f5d32;
        }

        /* =========================
           CHAT
        ========================= */

        .chat-bar {
            position: fixed;
            right: 30px;
            bottom: 25px;

            width: 390px;
            padding: 12px;

            display: flex;
            align-items: center;
            gap: 10px;

            background: #f4ead2;
            border-radius: 30px;

            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.25);
        }

        .chat-title {
            font-weight: bold;
            color: #344d35;
            padding-left: 10px;
        }

        .chat-input {
            flex: 1;
            border: none;
            outline: none;

            padding: 12px 15px;
            border-radius: 25px;

            background: white;
        }

        .chat-button {
            border: none;
            background: #557744;
            color: white;

            width: 40px;
            height: 40px;

            border-radius: 50%;

            cursor: pointer;
        }

        .btn-keluar {
        background: #070602;
        color: #2f4a2f;
        border: none;
        border-radius: 999px;
        padding: 12px 22px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,.25);
        }

        .btn-keluar:hover { background: #ece3c2; }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 768px) {

            .page {
                padding: 20px;
            }

            .welcome {
                margin-top: 50px;
            }

            .welcome h1 {
                font-size: 32px;
            }

            .chat-bar {
                left: 20px;
                right: 20px;
                width: auto;
            }
        }

        /* =========================
           PENGATURAN
        ========================= */

        .btn-pengaturan {
            background: rgba(47, 74, 47, 0.95); 
            color: #fff; 
            border: none;
            border-radius: 16px;
            padding: 14px 24px;
            font-size: 1.3rem; 
            font-weight: 800; cursor: pointer;
        }

        .pg-overlay {
            position: fixed; 
            inset: 0; 
            z-index: 100;
            background: rgba(0, 0, 0, .5);
            display: flex; 
            align-items: center; 
            justify-content: center;
        }

        .pg-overlay[hidden] { display: none; }
        .pg-card {
            position: relative; 
            width: 100%; 
            max-width: 380px;
            margin: 20px;
            background: #f7f0d8;
            color: #2f4a2f;
            border-radius: 24px;
            padding: 28px 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .4);
        }

        .pg-card h2 { margin: 0 0 18px; text-align: center; }
        .pg-close {
            position: absolute; 
            top: 14px; 
            right: 16px;
            background: none; 
            border: none; 
            font-size: 1.2rem;
            color: #2f4a2f; 
            cursor: pointer;
        }

        .pg-profil {
            display: flex; 
            align-items: center; 
            gap: 14px;
            background: #fff; 
            border-radius: 16px; 
            padding: 12px 14px; 
            margin-bottom: 14px;
        }

        .pg-avatar {
            width: 46px; 
            height: 46px; 
            border-radius: 50%; 
            object-fit:cover; 
            background: #fff; 
            color: #fff; 
            font-weight: 800; 
            font-size: 1.3rem;
            display: flex; 
            align-items: center; 
            justify-content: center;
        }

        .pg-item {
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            width: 100%; 
            margin-top: 8px; 
            padding: 12px 16px;
            background: #fff; color: #2f4a2f; border: none; border-radius: 14px;
            font-family: inherit; font-size: 1rem; font-weight: 600;
            text-decoration: none; cursor: pointer;
        }
        
        .pg-item:hover { background: #ece3c2; }
        .pg-keluar { color: #b3261e; margin-top: 14px; }

        .xp-card {
            width: 100%; max-width: 360px; margin: 18px auto 0;
            background: rgba(247, 240, 216, .92); color: #2f4a2f;
            border-radius: 18px; padding: 12px 18px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .25);
        }

        .xp-top {
            display: flex; justify-content: space-between;
            font-weight: 700; font-size: .95rem; margin-bottom: 8px;
        }
        
        .xp-bar { height: 12px; background: #e3dcc0; border-radius: 999px; overflow: hidden; }
        .xp-fill {
            height: 100%; background: linear-gradient(90deg, #7fb08a, #557a46);
            border-radius: 999px; transition: width .6s ease;
        }
    </style>
</head>


<body>

    <div class="page">

        <!-- HEADER -->

        <div class="header">

            <button type="button" id="btnPengaturan" class="btn-pengaturan" data-sfx="buka">⚙️ Pengaturan</button>
            
            <a href="{{ route('notes.index') }}" class="notes-button">
                📝 Catatan
            </a>


        </div>


        <!-- WELCOME -->

        <div class="welcome">

            <h1>Selamat Datang di Mathventure</h1>

            <p>
                Silakan pilih kelas untuk memulai pembelajaran matematika.
            </p>

            <div class="xp-card">
            <div class="xp-top">
                <span>⭐ Level {{ auth()->user()->level }}</span>
                <span>
                    {{ auth()->user()->isMaxLevel()
                        ? 'Level Maksimal 🏆'
                        : auth()->user()->xpInLevel() . ' / ' . \App\Models\User::XP_PER_LEVEL . ' XP' }}
                </span>
            </div>
            <div class="xp-bar">
                <div class="xp-fill" style="width: {{ auth()->user()->xpProgressPercent() }}%"></div>
            </div>
            </div>

        </div>


        <!-- KELAS -->

        <div class="classes">

            @foreach ($classes as $class)

                <div class="class-card">

                    <h2>{{ $class->name }}</h2>

                    <a href="{{ route('levels.index', $class->id) }}" class="class-button">
                    Pilih Kelas →
                    </a>

                </div>

            @endforeach

        </div>
    </div>
    
    <audio id="musikLatar" src="{{ asset('audio/musik.mp3') }}" loop></audio>

    <div id="modalPengaturan" class="pg-overlay" hidden>
        <div class="pg-card">
            <button type="button" class="pg-close" id="tutupPengaturan" data-sfx="tutup" aria-label="Tutup">✕
            </button>

            <p style="text-align:center; font-size:.75rem; margin-top:14px; opacity:.7;">
            Musik: "Epic Departure" oleh Of Far Different Nature (CC-BY 4.0)
            </p>
            <h2>⚙️ Pengaturan</h2>

            <div class="pg-profil">
                <img class="pg-avatar" src="{{ auth()->user()->avatarUrl() }}" alt="Avatar">
                <div>
                    <strong>{{ auth()->user()->name }}</strong><br>
                    <small>{{ '@' . auth()->user()->username }} · Level {{ auth()->user()->level }} · {{ auth()->user()->xp }} XP</small>
                </div>
            </div>

            <a href="/profile" class="pg-item">✏️ Edit Profil</a>
            @if(auth()->user()->is_admin)
            <a href="{{ route('admin.dashboard') }}" class="pg-item">🛠️ Panel Admin</a>
             @endif

            <label class="pg-item">
                <span>🎵 Musik latar</span>
                <input type="checkbox" id="toggleMusik">
            </label>

            <div class="pg-item">
                <span>🔊 Volume</span>
                <input type="range" id="volumeMusik" min="0" max="1" step="0.05">
            </div>

            <label class="pg-item">
                <span>🔔 Efek suara</span>
                <input type="checkbox" id="toggleSfx" checked>
            </label>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="pg-item pg-keluar">🚪 Keluar</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalPengaturan');
        const musik = document.getElementById('musikLatar');
        const toggleMusik = document.getElementById('toggleMusik');
        const volume = document.getElementById('volumeMusik');
        const toggleSfx = document.getElementById('toggleSfx');

        const simpan = (k, v) => { try { localStorage.setItem(k, v); } catch (e) {} };
        const ambil = (k, d) => { try { const v = localStorage.getItem(k); return v === null ? d : v; } catch (e) { return d; } };

        // Buka / tutup panel
        document.getElementById('btnPengaturan').onclick = () => modal.hidden = false;
        document.getElementById('tutupPengaturan').onclick = () => modal.hidden = true;
        modal.onclick = (e) => { if (e.target === modal) modal.hidden = true; };
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') modal.hidden = true; });

        // Pulihkan pengaturan tersimpan
        musik.volume = parseFloat(ambil('mv_volume', '0.5'));
        volume.value = musik.volume;
        toggleMusik.checked = ambil('mv_musik', 'off') === 'on';
        toggleSfx.checked = ambil('mv_sfx', 'on') === 'on';

        musik.addEventListener('loadedmetadata', () => {
            musik.currentTime = parseFloat(ambil('mv_waktu', '0')) || 0;
        });
        window.addEventListener('beforeunload', () => simpan('mv_waktu', musik.currentTime));

        // Browser memblokir suara otomatis, jadi mulai setelah klik pertama
        document.addEventListener('click', () => {
            if (toggleMusik.checked && musik.paused) musik.play().catch(() => {});
        }, { once: true });

        toggleMusik.onchange = () => {
            simpan('mv_musik', toggleMusik.checked ? 'on' : 'off');
            toggleMusik.checked ? musik.play().catch(() => {}) : musik.pause();
        };
        volume.oninput = () => { musik.volume = volume.value; simpan('mv_volume', volume.value); };
        toggleSfx.onchange = () => {
        simpan('mv_sfx', toggleSfx.checked ? 'on' : 'off');
        if (toggleSfx.checked) SFX.benar();
};
    </script>

    <script src="{{ asset('js/sfx.js') }}"></script>
    @include('classes.chat-box')

</body>

</html>