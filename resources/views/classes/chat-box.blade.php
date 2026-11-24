{{-- Kotak Chat. Dipanggil dari classes/index.blade.php dengan: @include('classes.chat-box') --}}

<div id="cb" class="cb">
    <div class="cb-panel" id="cbPanel" hidden>
        <div class="cb-head">
            <div class="cb-tabs">
                <button type="button" class="cb-tab is-active" data-room="semua" data-sfx="none">🌍 Semua</button>
                <button type="button" class="cb-tab" data-room="kelas" data-sfx="none" id="cbTabKelas">🏫 Kelas</button>
            </div>
            <button type="button" class="cb-x" id="cbTutup" data-sfx="tutup" aria-label="Tutup chat">✕</button>
        </div>

        <div class="cb-list" id="cbList"></div>

        <div class="cb-pilih" id="cbPilih" hidden>
            <strong>Pilih kelasmu dulu ya 🏫</strong>
            <p>Pilihan ini hanya bisa dibuat sekali dan tidak bisa diubah.</p>
            <div class="row">
                <button type="button" data-kelas="X" data-sfx="none">Kelas X</button>
                <button type="button" data-kelas="XI" data-sfx="none">Kelas XI</button>
                <button type="button" data-kelas="XII" data-sfx="none">Kelas XII</button>
            </div>
        </div>

        <p class="cb-error" id="cbError" hidden></p>
    </div>

    <form class="cb-bar" id="cbForm" autocomplete="off">
        <span class="cb-label">💬 Chat</span>
        <input type="text" id="cbInput" maxlength="200" placeholder="Ketik pesan...">
        <button type="submit" class="cb-kirim" data-sfx="none" aria-label="Kirim">➤</button>
    </form>
</div>

<style>
    .cb {
        position: fixed; right: 20px; bottom: 20px; z-index: 90;
        width: min(360px, calc(100vw - 24px));
        display: flex; flex-direction: column; gap: 10px;
        font-family: inherit; color: #2f4a2f;
    }

    /* Panel */
    .cb-panel {
        background: #f7f0d8; border-radius: 22px; overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .4);
        display: flex; flex-direction: column;
    }
    .cb-panel[hidden] { display: none; }
    .cb-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 12px; background: rgba(47, 74, 47, .08);
    }
    .cb-tabs { display: flex; gap: 6px; }
    .cb-tab {
        border: none; background: transparent; color: #2f4a2f;
        font-family: inherit; font-size: .85rem; font-weight: 700;
        padding: 7px 14px; border-radius: 999px; cursor: pointer;
    }
    .cb-tab.is-active { background: #557a46; color: #fff; }
    .cb-x { border: none; background: none; font-size: 1.05rem; color: #2f4a2f; cursor: pointer; padding: 4px 8px; }

    /* Daftar pesan */
    .cb-list {
        height: 300px; overflow-y: auto; padding: 10px 12px;
        display: flex; flex-direction: column; gap: 10px;
    }
    .cb-kosong { margin: auto; text-align: center; opacity: .7; font-weight: 600; font-size: .9rem; }
    .cb-msg { display: flex; gap: 8px; align-items: flex-start; }
    .cb-msg.mine { flex-direction: row-reverse; }
    .cb-ava { width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0; background: #fff; }
    .cb-bub { background: #fff; border-radius: 14px; padding: 7px 11px; max-width: 78%; min-width: 0; }
    .cb-msg.mine .cb-bub { background: #dcebd2; }
    .cb-meta { display: flex; gap: 6px; align-items: baseline; font-size: .72rem; }
    .cb-lv { opacity: .65; }
    .cb-time { opacity: .5; margin-left: auto; padding-left: 8px; }
    .cb-text { font-size: .9rem; line-height: 1.4; word-break: break-word; white-space: pre-wrap; }
    .cb-aksi { display: flex; gap: 8px; margin-top: 4px; }
    .cb-mini { border: none; background: none; cursor: pointer; font-size: .72rem; opacity: .55; padding: 0; color: #2f4a2f; }
    .cb-mini:hover { opacity: 1; }

    /* Pilih kelas */
    .cb-pilih { padding: 24px 18px; text-align: center; }
    .cb-pilih[hidden] { display: none; }
    .cb-pilih p { margin: 6px 0 0; font-size: .82rem; opacity: .8; }
    .cb-pilih .row { display: flex; gap: 8px; justify-content: center; margin-top: 14px; }
    .cb-pilih button {
        border: none; background: #557a46; color: #fff; border-radius: 999px;
        padding: 9px 16px; font-family: inherit; font-size: .85rem; font-weight: 700; cursor: pointer;
    }
    .cb-pilih button:hover { background: #466a39; }

    /* Pesan error / info */
    .cb-error { margin: 0; padding: 8px 14px; font-size: .8rem; font-weight: 600; background: #fde3e1; color: #b3261e; }
    .cb-error.ok { background: #dcebd2; color: #2f4a2f; }
    .cb-error[hidden] { display: none; }

    /* Bar ketik (sama seperti tampilan sebelumnya) */
    .cb-bar {
        display: flex; align-items: center; gap: 10px;
        background: #f7f0d8; border-radius: 999px; padding: 8px 10px 8px 18px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .35);
    }
    .cb-label { font-weight: 700; white-space: nowrap; }
    .cb-bar input {
        flex: 1; min-width: 0; border: none; border-radius: 999px; padding: 11px 16px;
        font-family: inherit; font-size: .95rem; outline: none; background: #fff; color: #2f4a2f;
    }
    .cb-kirim {
        width: 40px; height: 40px; flex-shrink: 0; border: none; border-radius: 50%;
        background: #557a46; color: #fff; font-size: 1rem; cursor: pointer;
    }
    .cb-kirim:hover { background: #466a39; }
</style>

<script>
    (function () {
        const ME = {
            username: @json(auth()->user()->username),        kelas: @json(auth()->user()->kelas),
            moderator: {{ auth()->user()->is_moderator ? 'true' : 'false' }}
        };
        const URL_CHAT = "{{ route('chat.index') }}";
        const TOKEN = "{{ csrf_token() }}";

        const panel = document.getElementById('cbPanel');
        const list = document.getElementById('cbList');
        const pilih = document.getElementById('cbPilih');
        const boxError = document.getElementById('cbError');
        const form = document.getElementById('cbForm');
        const input = document.getElementById('cbInput');
        const tabKelas = document.getElementById('cbTabKelas');
        const tabs = document.querySelectorAll('.cb-tab');

        let room = 'semua';
        let kelas = ME.kelas;
        let timer = null;
        let lastSig = '';
        let timerPesan = null;

        const bunyi = (n) => { if (window.SFX && SFX[n]) SFX[n](); };

        function el(tag, kelasCss, teks) {
            const e = document.createElement(tag);
            if (kelasCss) e.className = kelasCss;
            if (teks) e.textContent = teks;
            return e;
        }

        function jam(iso) {
            return new Date(iso).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }

        async function api(url, method, data) {
            const res = await fetch(url, {
                method: method,
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data ? JSON.stringify(data) : undefined
            });
            let json = null;
            try { json = await res.json(); } catch (e) {}
            return { ok: res.ok, status: res.status, json: json };
        }

        function tampilPesan(teks, sukses) {
            clearTimeout(timerPesan);
            boxError.textContent = teks;
            boxError.className = 'cb-error' + (sukses ? ' ok' : '');
            boxError.hidden = false;
            timerPesan = setTimeout(function () { boxError.hidden = true; }, 4000);
        }

        function pesanError(r) {
            if (r.status === 429) return 'Pelan-pelan ya, tunggu sebentar ⏳';
            if (r.json && r.json.errors) return Object.values(r.json.errors).flat().join(' ');
            if (r.json && r.json.message) return r.json.message;
            return 'Terjadi kesalahan. Coba lagi.';
        }

        function perbaruiTabKelas() {
            tabKelas.textContent = '🏫 ' + (kelas ? 'Kelas ' + kelas : 'Kelas');
        }

        function mini(label, judul, aksi) {
            const b = el('button', 'cb-mini', label);
            b.type = 'button';
            b.title = judul;
            b.dataset.sfx = 'none';
            b.addEventListener('click', aksi);
            return b;
        }

        function elPesan(m) {
            const baris = el('div', 'cb-msg' + (m.mine ? ' mine' : ''));
            const ava = el('img', 'cb-ava');
            ava.src = m.avatar;
            ava.alt = '';

            const bubble = el('div', 'cb-bub');
            const meta = el('div', 'cb-meta');
            meta.append(el('strong', '', m.username), el('span', 'cb-lv', 'Lv ' + m.level), el('span', 'cb-time', jam(m.time)));
            bubble.append(meta, el('div', 'cb-text', m.body));

            const aksi = el('div', 'cb-aksi');
            if (!m.mine) aksi.append(mini('⚑ Laporkan', 'Laporkan pesan ini', function () { lapor(m); }));
            if (ME.moderator) {
                aksi.append(mini('🗑 Hapus', 'Sembunyikan pesan', function () { moderasi(m, '', 'DELETE'); }));
                if (!m.mine) aksi.append(mini('🔇 Bisukan', 'Bisukan pengirim 10 menit', function () { moderasi(m, '/bisukan', 'POST'); }));
            }
            if (aksi.children.length) bubble.append(aksi);

            baris.append(ava, bubble);
            return baris;
        }

        function render(pesan, keBawah) {
            const dekatBawah = list.scrollHeight - list.scrollTop - list.clientHeight < 60;
            list.replaceChildren();
            if (!pesan.length) {
                list.append(el('p', 'cb-kosong', 'Belum ada pesan. Sapa temanmu! 👋'));
            } else {
                pesan.forEach(function (m) { list.append(elPesan(m)); });
            }
            if (keBawah || dekatBawah) list.scrollTop = list.scrollHeight;
        }

        function tampilPilih(tampil) {
            pilih.hidden = !tampil;
            list.hidden = tampil;
        }

        async function muat(paksa) {
            if (room === 'kelas' && !kelas) { tampilPilih(true); return; }
            tampilPilih(false);

            const diminta = room;
            const r = await api(URL_CHAT + '?room=' + diminta, 'GET');
            if (diminta !== room) return; // tab sudah berpindah

            if (r.status === 409) { kelas = null; tampilPilih(true); return; }
            if (!r.ok) { tampilPesan('Chat gagal dimuat. Coba lagi sebentar.'); return; }

            const sig = diminta + ':' + r.json.messages.map(function (m) { return m.id; }).join(',');
            if (!paksa && sig === lastSig) return;
            lastSig = sig;
            render(r.json.messages, paksa);
        }

        function bukaPanel() {
            if (!panel.hidden) return;
            panel.hidden = false;
            lastSig = '';
            muat(true);
            timer = setInterval(function () { if (!document.hidden) muat(false); }, 3000);
        }

        function tutupPanel() {
            panel.hidden = true;
            clearInterval(timer);
            timer = null;
        }

        async function lapor(m) {
            if (!confirm('Laporkan pesan ini ke moderator?')) return;
            const r = await api(URL_CHAT + '/' + m.id + '/laporkan', 'POST');
            tampilPesan(r.ok ? 'Terima kasih, laporanmu sudah dikirim. 🙏' : pesanError(r), r.ok);
            if (r.ok) muat(true);
        }

        async function moderasi(m, akhiran, method) {
            const r = await api(URL_CHAT + '/' + m.id + akhiran, method);
            if (r.ok) { muat(true); } else { tampilPesan(pesanError(r)); }
        }

        // Tab Semua / Kelas
        tabs.forEach(function (t) {
            t.addEventListener('click', function () {
                room = t.dataset.room;
                tabs.forEach(function (x) { x.classList.toggle('is-active', x === t); });
                lastSig = '';
                muat(true);
            });
        });

        // Pilih kelas (sekali saja)
        pilih.querySelectorAll('button[data-kelas]').forEach(function (b) {
            b.addEventListener('click', async function () {
                const pilihan = b.dataset.kelas;
                if (!confirm('Kamu di Kelas ' + pilihan + '? Pilihan ini tidak bisa diubah lagi.')) return;
                const r = await api(URL_CHAT + '/kelas', 'POST', { kelas: pilihan });
                if (r.ok) {
                    kelas = r.json.kelas;
                    perbaruiTabKelas();
                    muat(true);
                } else {
                    tampilPesan(pesanError(r));
                }
            });
        });

        // Kirim pesan
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const teks = input.value.trim();
            if (!teks) return;
            bukaPanel();
            if (room === 'kelas' && !kelas) return;

            const r = await api(URL_CHAT, 'POST', { room: room, body: teks });
            if (r.ok) {
                input.value = '';
                bunyi('xp');
                muat(true);
            } else {
                tampilPesan(pesanError(r));
                bunyi('salah');
            }
        });

        input.addEventListener('focus', bukaPanel);
        document.querySelector('.cb-label').addEventListener('click', function () {
            panel.hidden ? bukaPanel() : tutupPanel();
        });
        document.getElementById('cbTutup').addEventListener('click', tutupPanel);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !panel.hidden) tutupPanel();
        });

        perbaruiTabKelas();
    })();
</script>