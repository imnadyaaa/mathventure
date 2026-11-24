<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Level {{ $level->level_number }} {{ $level->name }} - Mathventure</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        [hidden] { display: none !important; }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            color: #2f4a2f;
            background-image:
                linear-gradient(rgba(20, 45, 25, 0.35), rgba(20, 45, 25, 0.35)),
                url('{{ asset('image/forest-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .qz-page { max-width: 820px; margin: 0 auto; padding: 18px 16px 50px; }

        /* Bar atas */
        .qz-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
        .qz-keluar {
            background: #f7f0d8; color: #2f4a2f; text-decoration: none; font-weight: 700;
            padding: 10px 18px; border-radius: 999px; box-shadow: 0 4px 12px rgba(0, 0, 0, .25); white-space: nowrap;
        }
        .qz-judul { text-align: center; color: #fff; text-shadow: 0 2px 6px rgba(0, 0, 0, .6); line-height: 1.3; }
        .qz-judul strong { display: block; font-size: 1.1rem; }
        .qz-judul span { font-size: .85rem; opacity: .9; }
        .qz-progres { min-width: 90px; text-align: right; color: #fff; font-weight: 700; text-shadow: 0 2px 6px rgba(0, 0, 0, .6); }

        /* Kartu */
        .qz-card {
            background: rgba(247, 240, 216, .96); border-radius: 26px; padding: 26px 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
        }
        .qz-card h2 { font-size: 1.5rem; margin-bottom: 6px; }
        .qz-sub { color: #4a5a40; margin-bottom: 16px; }

        .qz-info { display: flex; gap: 10px; margin: 16px 0; }
        .qz-info div { flex: 1; background: #fff; border-radius: 14px; padding: 10px 6px; text-align: center; }
        .qz-info span { display: block; font-size: .72rem; color: #6b7a60; margin-bottom: 4px; }
        .qz-info strong { font-size: .92rem; }

        .qz-materi { background: #fff; border-radius: 14px; padding: 14px 16px; margin-bottom: 16px; }
        .qz-materi h3 { font-size: 1rem; margin-bottom: 6px; }
        .qz-materi p { white-space: pre-line; line-height: 1.6; font-size: .93rem; color: #3d4d35; }
        .qz-catatan { font-size: .85rem; color: #6b5a14; background: #fdf0d0; border-radius: 12px; padding: 10px 14px; margin-bottom: 16px; }

        .qz-btn {
            background: #557a46; color: #fff; border: none; border-radius: 999px; cursor: pointer;
            padding: 13px 28px; font-family: inherit; font-size: 1rem; font-weight: 700;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .25); text-decoration: none; display: inline-block;
        }
        .qz-btn:hover { background: #466a39; }
        .qz-btn:disabled { background: #a9b39f; cursor: not-allowed; }
        .qz-btn.terang { background: #fff; color: #2f4a2f; }
        .qz-btn.terang:hover { background: #ece3c2; }

        /* Soal */
        .qz-bar { height: 10px; background: #e3dcc0; border-radius: 999px; overflow: hidden; margin-bottom: 18px; }
        .qz-bar div { height: 100%; width: 0; background: linear-gradient(90deg, #7fb08a, #557a46); transition: width .3s ease; }
        .qz-nomor { font-size: .85rem; font-weight: 700; color: #6b7a60; margin-bottom: 8px; }
        .qz-tanya { font-size: 1.35rem; font-weight: 700; line-height: 1.45; margin-bottom: 20px; }
        .qz-petunjuk { font-size: .9rem; color: #6b7a60; margin-top: -12px; margin-bottom: 18px; }

        .qz-opsi {
            display: block; width: 100%; text-align: left; margin-bottom: 10px; padding: 14px 18px;
            background: #fff; color: #2f4a2f; border: 3px solid transparent; border-radius: 16px;
            font-family: inherit; font-size: 1.05rem; cursor: pointer;
        }
        .qz-opsi:hover { background: #f3ecd0; }
        .qz-opsi.dipilih { border-color: #557a46; background: #e6f0dc; font-weight: 700; }
        .qz-tf { display: flex; gap: 12px; }
        .qz-tf .qz-opsi { text-align: center; margin-bottom: 0; font-weight: 700; }

        .qz-isian {
            width: 100%; padding: 14px 18px; font-family: inherit; font-size: 1.15rem; color: #2f4a2f;
            background: #fff; border: 3px solid #d9d2b0; border-radius: 16px; outline: none;
        }
        .qz-isian:focus { border-color: #557a46; }

        /* Menjodohkan: tarik soal ke jawaban */
        .qz-jodoh { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; align-items: start; }
        .qz-jodoh .kol { min-width: 0; border-radius: 16px; padding: 4px; }
        .qz-jodoh .kol-soal.sorot { background: #ece3c2; }
        .qz-jodoh.satu { grid-template-columns: 1fr; gap: 10px; }
        .qz-jodoh.satu .kol-soal { width: 100%; max-width: 340px; margin: 0 auto; }
        .qz-jodoh.satu .kol-soal .qz-kartu, .qz-jodoh.satu .kol-soal .qz-kosong { min-height: 64px; font-size: 1.3rem; }
        .qz-jodoh.satu .kol-jawab { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; }
        .qz-jodoh.satu .kol-jawab .qz-judul-kol { grid-column: 1 / -1; margin-bottom: 0; }
        .qz-jodoh.satu .qz-kotak { margin: 0; min-height: 64px; display: flex; flex-direction: column; justify-content: center; }
        .qz-judul-kol { font-size: .8rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; color: #6b7a60; margin: 0 0 8px 2px; }
        .qz-kartu {
            background: #fff; color: #2f4a2f; border: 3px solid transparent; border-radius: 14px;
            padding: 10px 12px; margin-bottom: 8px; min-height: 46px; display: flex; align-items: center; justify-content: center;
            text-align: center; font-weight: 700; font-size: 1.05rem; line-height: 1.3;
            cursor: grab; touch-action: none; user-select: none; -webkit-user-select: none;
            box-shadow: 0 2px 0 #d9d2b0; outline: none;
        }
        .qz-kartu:hover { background: #f3ecd0; }
        .qz-kartu:focus-visible { border-color: #2f4a2f; }
        .qz-kartu.dipilih { border-color: #557a46; background: #e6f0dc; }
        .qz-kartu.ditarik { opacity: .3; }
        .qz-kartu.di-kotak { margin: 6px 0 0; width: 100%; background: #cfe3bf; box-shadow: none; }
        .qz-kartu.melayang {
            position: fixed; z-index: 100; margin: 0; pointer-events: none; cursor: grabbing;
            box-shadow: 0 10px 22px rgba(0, 0, 0, .35); transform: rotate(-3deg); opacity: 1;
        }
        .qz-kosong { height: 46px; margin-bottom: 8px; border: 2px dashed #d9d2b0; border-radius: 14px; }
        .qz-kotak {
            margin-bottom: 8px; min-height: 46px; padding: 8px 10px; text-align: center;
            background: #f3ecd0; border: 3px dashed #cdc59f; border-radius: 14px; cursor: pointer;
            transition: background .12s, border-color .12s;
        }
        .qz-kotak .jwb { font-weight: 800; font-size: 1.05rem; color: #2f4a2f; }
        .qz-kotak.isi { border-style: solid; border-color: #7fb08a; background: #e6f0dc; }
        .qz-kotak.sorot { border-style: solid; border-color: #557a46; background: #d7e8c6; }

        .qz-nav { display: flex; justify-content: space-between; gap: 10px; margin-top: 22px; }

        .qz-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 22px; padding-top: 18px; border-top: 2px dashed #d9d2b0; }
        .qz-grid button {
            min-width: 44px; height: 44px; padding: 0 8px; border-radius: 12px; cursor: pointer;
            font-family: inherit; font-size: .95rem; font-weight: 700; color: #2f4a2f;
            background: #fff; border: 3px solid transparent;
        }
        .qz-grid button.terjawab { background: #cfe3bf; }
        .qz-grid button.sebagian { background: #fff1b5; }
        .qz-grid button.sekarang { border-color: #2f4a2f; }
        .qz-legenda { font-size: .78rem; color: #6b7a60; margin-top: 10px; width: 100%; }

        /* Konfirmasi */
        .qz-overlay { position: fixed; inset: 0; z-index: 50; background: rgba(0, 0, 0, .55); display: flex; align-items: center; justify-content: center; padding: 16px; }
        .qz-dialog { background: #f7f0d8; border-radius: 24px; padding: 26px 24px; max-width: 420px; width: 100%; text-align: center; box-shadow: 0 12px 34px rgba(0, 0, 0, .45); }
        .qz-dialog h3 { font-size: 1.3rem; margin-bottom: 10px; }
        .qz-dialog p { line-height: 1.5; margin-bottom: 8px; }
        .qz-dialog .aksi { display: flex; gap: 10px; justify-content: center; margin-top: 18px; flex-wrap: wrap; }
        .qz-error { color: #b3261e; font-weight: 700; font-size: .9rem; margin-top: 10px; }

        /* Hasil */
        .qz-hasil { text-align: center; }
        .qz-skor { font-size: 4rem; font-weight: 800; line-height: 1; margin: 6px 0; }
        .qz-skor.lulus { color: #3f7a2f; }
        .qz-skor.gagal { color: #b3261e; }
        .qz-bintang { font-size: 2.4rem; letter-spacing: 6px; color: #cfc8ad; margin: 8px 0; }
        .qz-bintang .nyala { color: #f2b01e; }
        .qz-ringkas { background: #fff; border-radius: 14px; padding: 12px 16px; margin: 12px 0; line-height: 1.6; }
        .qz-naik { background: #fff1b5; color: #6b5a14; font-weight: 800; }
        .qz-salah { background: #fde3e1; color: #8c1d18; font-weight: 700; }
        .qz-aksi-hasil { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin: 18px 0 8px; }

        .qz-saring { text-align: left; margin: 20px 0 10px; font-weight: 700; display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .qz-tinjau { text-align: left; }
        .qz-item { display: flex; gap: 12px; background: #fff; border-radius: 16px; padding: 12px 14px; margin-bottom: 10px; border-left: 6px solid #7fb08a; }
        .qz-item.salah { border-left-color: #b3261e; background: #fff6f5; }
        .qz-item .no {
            flex-shrink: 0; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: #fff; background: #7fb08a;
        }
        .qz-item.salah .no { background: #b3261e; }
        .qz-item .isi { min-width: 0; line-height: 1.5; }
        .qz-item .soal { font-weight: 700; word-break: break-word; }
        .qz-item .baris { font-size: .92rem; word-break: break-word; }
        .qz-item .benar-teks { color: #3f7a2f; font-weight: 700; }
        .qz-item .salah-teks { color: #b3261e; font-weight: 700; }
        .qz-item .jelas { font-size: .88rem; color: #5a6a50; margin-top: 4px; }

        @media (max-width: 560px) {
            .qz-top { flex-wrap: wrap; justify-content: center; }
            .qz-tanya { font-size: 1.15rem; }
            .qz-jodoh { gap: 8px; }
            .qz-kartu, .qz-kotak .jwb { font-size: .95rem; }
            .qz-info { flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="qz-page">

    <header class="qz-top">
        <a href="{{ route('levels.index', $level->class_id) }}" class="qz-keluar" id="qzKeluar" data-sfx="tutup">← Keluar</a>
        <div class="qz-judul">
            <strong>Level {{ $level->level_number }} — {{ $level->name }}</strong>
            <span>{{ $level->schoolClass->name ?? '' }}</span>
        </div>
        <div class="qz-progres" id="qzProgres"></div>
    </header>

    {{-- 1. Materi dan pengantar --}}
    <section class="qz-card" id="qzIntro">
        <h2>Level {{ $level->level_number }} — {{ $level->name }}</h2>
        <p class="qz-sub" id="qzIntroSub">Memuat soal...</p>
        <div id="qzIntroIsi" hidden>
            <div class="qz-info">
                <div><span>Bentuk soal</span><strong id="qzFormat"></strong></div>
                <div><span>Jumlah</span><strong id="qzJumlah"></strong></div>
                <div><span>Nilai lulus</span><strong id="qzLulus"></strong></div>
            </div>
            <div class="qz-materi" id="qzMateri" hidden>
                <h3 id="qzMateriJudul"></h3>
                <p id="qzMateriIsi"></p>
            </div>
            <p class="qz-catatan">📝 Jawabanmu baru dinilai setelah kamu menekan <b>Kumpulkan</b>. Selama belum, kamu boleh kembali dan mengubah jawaban.</p>
            <p class="qz-catatan" id="qzPulih" hidden>♻️ Jawaban sebelumnya di perangkat ini sudah dipulihkan.</p>
            <button type="button" class="qz-btn" id="qzMulai">Mulai Ujian →</button>
        </div>
    </section>

    {{-- 2. Mengerjakan soal --}}
    <section class="qz-card" id="qzSoal" hidden>
        <div class="qz-bar"><div id="qzBarIsi"></div></div>
        <div class="qz-nomor" id="qzNomor"></div>
        <div class="qz-tanya" id="qzTanya"></div>
        <p class="qz-petunjuk" id="qzPetunjuk" hidden></p>
        <div id="qzJawab"></div>

        <div class="qz-nav">
            <button type="button" class="qz-btn terang" id="qzPrev">← Sebelumnya</button>
            <button type="button" class="qz-btn" id="qzNext">Selanjutnya →</button>
        </div>

        <div class="qz-grid" id="qzGrid"></div>
    </section>

    {{-- 3. Hasil --}}
    <section class="qz-card qz-hasil" id="qzHasil" hidden>
        <h2 id="qzHasilJudul"></h2>
        <div class="qz-skor" id="qzSkor"></div>
        <div class="qz-bintang" id="qzBintang"></div>
        <div class="qz-ringkas" id="qzRingkas"></div>
        <div class="qz-ringkas qz-naik" id="qzNaik" hidden></div>
        <div class="qz-ringkas qz-salah" id="qzDaftarSalah" hidden></div>

        <div class="qz-aksi-hasil">
            <button type="button" class="qz-btn terang" id="qzUlang">↻ Coba Lagi</button>
            <a href="{{ route('levels.index', $level->class_id) }}" class="qz-btn">Kembali ke Daftar Level</a>
        </div>

        <label class="qz-saring"><input type="checkbox" id="qzHanyaSalah"> Tampilkan hanya soal yang salah</label>
        <div class="qz-tinjau" id="qzTinjau"></div>
    </section>
</div>

{{-- Konfirmasi mengumpulkan --}}
<div class="qz-overlay" id="qzKonfirmasi" hidden>
    <div class="qz-dialog" role="dialog" aria-modal="true">
        <h3>Kumpulkan jawaban?</h3>
        <p id="qzKonfirmasiIsi"></p>
        <p class="qz-error" id="qzKonfirmasiError" hidden></p>
        <div class="aksi">
            <button type="button" class="qz-btn terang" id="qzKembali">Periksa Lagi</button>
            <button type="button" class="qz-btn" id="qzKumpulkan">Kumpulkan ✓</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/sfx.js') }}"></script>
<script>
    (function () {
        const URL_SOAL = "{{ route('quiz.soal', $level) }}";
        const URL_KUMPULKAN = "{{ route('quiz.kumpulkan', $level) }}";
        const TOKEN = "{{ csrf_token() }}";
        const NOMOR_LEVEL = {{ $level->level_number }};
        const KUNCI_SIMPAN = "mv-ujian-{{ auth()->id() }}-{{ $level->id }}";
        const FORMAT_SOAL = ['Pilihan ganda', 'Benar atau salah', 'Isian jawaban', 'Menjodohkan', 'Gabungan berbagai bentuk soal'];

        const $ = (id) => document.getElementById(id);

        let data = null;      // soal dari server (tanpa kunci)
        let layar = [];       // satu layar = satu soal, atau satu kelompok menjodohkan
        let idx = 0;
        let jawab = {};       // {idSoal: jawaban}
        let tahap = 'intro';  // intro | soal | hasil
        let mengirim = false;

        const bunyi = (n) => { if (window.SFX && SFX[n]) SFX[n](); };

        // ---------- penyimpanan sementara di perangkat ----------
        function baca() {
            try { return JSON.parse(localStorage.getItem(KUNCI_SIMPAN)) || null; } catch (e) { return null; }
        }
        function simpan() {
            try { localStorage.setItem(KUNCI_SIMPAN, JSON.stringify(jawab)); } catch (e) {}
        }
        function hapusSimpanan() {
            try { localStorage.removeItem(KUNCI_SIMPAN); } catch (e) {}
        }

        // ---------- bantu ----------
        function el(tag, kelas, teks) {
            const e = document.createElement(tag);
            if (kelas) e.className = kelas;
            if (teks !== undefined && teks !== null) e.textContent = teks;
            return e;
        }
        function label(l) {
            const a = l.items[0].nomor, b = l.items[l.items.length - 1].nomor;
            return a === b ? String(a) : a + '–' + b;
        }
        function terisi(q) { return (jawab[q.id] || '').trim() !== ''; }
        function statusLayar(l) {
            const n = l.items.filter(terisi).length;
            return n === l.items.length ? 'terjawab' : (n > 0 ? 'sebagian' : '');
        }
        function tampilkan(nama) {
            tahap = nama;
            $('qzIntro').hidden = nama !== 'intro';
            $('qzSoal').hidden = nama !== 'soal';
            $('qzHasil').hidden = nama !== 'hasil';
            window.scrollTo({ top: 0 });
        }

        // ---------- memuat soal ----------
        function susunLayar() {
            // Satu layar = satu soal (termasuk soal menjodohkan, satu pasangan per layar)
            layar = data.soal.map(function (q) { return { tipe: q.type, items: [q] }; });
        }

        async function muat() {
            try {
                const res = await fetch(URL_SOAL, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                if (!res.ok) throw new Error(res.status);
                data = await res.json();
            } catch (e) {
                $('qzIntroSub').textContent = 'Soal gagal dimuat. Coba muat ulang halaman ini.';
                return;
            }

            if (!data.total) {
                $('qzIntroSub').textContent = 'Soal untuk level ini belum tersedia.';
                return;
            }

            susunLayar();
            const tersimpan = baca();
            if (tersimpan && Object.keys(tersimpan).length) {
                jawab = tersimpan;
                $('qzPulih').hidden = false;
            }

            const satuan = NOMOR_LEVEL === 4 ? 'pasangan' : 'soal';
            $('qzIntroSub').textContent = data.level.kelas || '';
            $('qzFormat').textContent = FORMAT_SOAL[NOMOR_LEVEL - 1] || 'Soal';
            $('qzJumlah').textContent = data.total + ' ' + satuan;
            $('qzLulus').textContent = data.level.nilai_lulus + '%';
            if (data.materi) {
                $('qzMateriJudul').textContent = data.materi.judul;
                $('qzMateriIsi').textContent = data.materi.isi;
                $('qzMateri').hidden = false;
            }
            $('qzIntroIsi').hidden = false;
        }

        // ---------- soal menjodohkan: tarik soal ke jawaban ----------
        function tampilJodoh(l, wadah) {
            const pilihan = l.items[0].options;
            const papan = el('div', 'qz-jodoh' + (l.items.length === 1 ? ' satu' : ''));
            wadah.append(papan);

            let terpilih = null;      // id soal yang diketuk (cara ketuk, untuk HP / keyboard)
            let seret = null;         // keadaan tarikan yang sedang berjalan
            let abaikanKlik = false;  // supaya lepas tarikan tidak dihitung sebagai ketukan

            function taruh(item, jawaban) {
                jawab[item.id] = jawaban;
                terpilih = null;
                simpan();
                perbaruiGrid();
                gambar();
                bunyi('klik');
            }

            function buatKartu(item, didalamKotak) {
                const k = el('div', 'qz-kartu' + (didalamKotak ? ' di-kotak' : '') + (terpilih === item.id ? ' dipilih' : ''), item.question);
                k.tabIndex = 0;
                k.setAttribute('role', 'button');
                k.addEventListener('pointerdown', function (e) { mulaiSeret(e, item, k); });
                k.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (abaikanKlik) { abaikanKlik = false; return; }
                    if (didalamKotak) {
                        if (terpilih) {
                            const dipilih = l.items.find(function (x) { return x.id === terpilih; });
                            if (dipilih) { taruh(dipilih, jawab[item.id]); return; }
                        }
                        taruh(item, '');          // ketuk kartu di kotak = kembalikan ke daftar soal
                        return;
                    }
                    terpilih = terpilih === item.id ? null : item.id;
                    gambar();
                });
                k.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); k.click(); }
                });
                return k;
            }

            function gambar() {
                papan.replaceChildren();
                const kiri = el('div', 'kol kol-soal');
                const kanan = el('div', 'kol kol-jawab');
                kiri.append(el('div', 'qz-judul-kol', 'Soal'));
                kanan.append(el('div', 'qz-judul-kol', 'Jawaban'));

                l.items.forEach(function (item) {
                    if (jawab[item.id]) { kiri.append(el('div', 'qz-kosong')); return; }
                    kiri.append(buatKartu(item, false));
                });

                pilihan.forEach(function (o) {
                    const kotak = el('div', 'qz-kotak');
                    kotak.dataset.jawaban = o;
                    kotak.append(el('div', 'jwb', o));
                    const isi = l.items.filter(function (i) { return jawab[i.id] === o; });
                    if (isi.length) kotak.classList.add('isi');
                    isi.forEach(function (i) { kotak.append(buatKartu(i, true)); });
                    kotak.addEventListener('click', function () {
                        if (!terpilih) return;
                        const dipilih = l.items.find(function (x) { return x.id === terpilih; });
                        if (dipilih) taruh(dipilih, o);
                    });
                    kanan.append(kotak);
                });

                papan.append(kiri, kanan);
            }

            function sasaran(e) {
                const n = document.elementFromPoint(e.clientX, e.clientY);
                if (!n) return null;
                const kotak = n.closest('.qz-kotak');
                if (kotak && papan.contains(kotak)) return { jawaban: kotak.dataset.jawaban, el: kotak };
                const kolSoal = n.closest('.kol-soal');
                if (kolSoal && papan.contains(kolSoal)) return { jawaban: '', el: kolSoal };
                return null;
            }

            function sorot(s) {
                papan.querySelectorAll('.sorot').forEach(function (n) { n.classList.remove('sorot'); });
                if (s) s.el.classList.add('sorot');
            }

            function mulaiSeret(e, item, kartu) {
                if (seret || (e.pointerType === 'mouse' && e.button !== 0)) return;
                seret = { item: item, kartu: kartu, id: e.pointerId, x0: e.clientX, y0: e.clientY, aktif: false, klon: null, dx: 0, dy: 0 };
                document.addEventListener('pointermove', gerak, { passive: false });
                document.addEventListener('pointerup', lepas);
                document.addEventListener('pointercancel', batal);
            }

            function gerak(e) {
                if (!seret || e.pointerId !== seret.id) return;
                if (!seret.aktif) {
                    if (Math.hypot(e.clientX - seret.x0, e.clientY - seret.y0) < 6) return;
                    const r = seret.kartu.getBoundingClientRect();
                    seret.aktif = true;
                    seret.dx = seret.x0 - r.left;
                    seret.dy = seret.y0 - r.top;
                    seret.klon = seret.kartu.cloneNode(true);
                    seret.klon.classList.add('melayang');
                    seret.klon.style.width = r.width + 'px';
                    document.body.append(seret.klon);
                    seret.kartu.classList.add('ditarik');
                }
                e.preventDefault();
                seret.klon.style.left = (e.clientX - seret.dx) + 'px';
                seret.klon.style.top = (e.clientY - seret.dy) + 'px';
                if (e.clientY < 70) window.scrollBy(0, -14);
                else if (e.clientY > window.innerHeight - 70) window.scrollBy(0, 14);
                sorot(sasaran(e));
            }

            function bersihkan() {
                document.removeEventListener('pointermove', gerak);
                document.removeEventListener('pointerup', lepas);
                document.removeEventListener('pointercancel', batal);
                if (seret && seret.klon) seret.klon.remove();
                sorot(null);
            }

            function lepas(e) {
                if (!seret || e.pointerId !== seret.id) return;
                const s = seret;
                const tujuan = s.aktif ? sasaran(e) : null;
                bersihkan();
                seret = null;
                if (!s.aktif) return;                 // hanya ketukan biasa, biarkan event click yang bekerja
                abaikanKlik = true;
                setTimeout(function () { abaikanKlik = false; }, 0);
                if (tujuan) taruh(s.item, tujuan.jawaban);
                else gambar();
            }

            function batal(e) {
                if (!seret || e.pointerId !== seret.id) return;
                bersihkan();
                seret = null;
                gambar();
            }

            gambar();
        }

        // ---------- menampilkan satu layar soal ----------
        function tampil() {
            const l = layar[idx];
            const q = l.items[0];

            $('qzProgres').textContent = (idx + 1) + ' / ' + layar.length;
            $('qzBarIsi').style.width = ((idx + 1) / layar.length * 100) + '%';
            $('qzNomor').textContent = 'Soal ' + label(l) + ' dari ' + data.total;

            const tanya = $('qzTanya');
            const petunjuk = $('qzPetunjuk');
            const wadah = $('qzJawab');
            wadah.replaceChildren();
            petunjuk.hidden = true;

            if (l.tipe === 'matching') {
                tanya.textContent = 'Jodohkan bentuk di bawah dengan pasangan yang tepat.';
                petunjuk.textContent = 'Tarik kartu soal ke kotak jawaban yang benar.';
                petunjuk.hidden = false;
                tampilJodoh(l, wadah);
            } else if (l.tipe === 'short_answer') {
                tanya.textContent = q.question;
                petunjuk.textContent = 'Ketik jawabanmu di bawah ini.';
                petunjuk.hidden = false;
                const input = el('input', 'qz-isian');
                input.type = 'text';
                input.maxLength = 100;
                input.autocomplete = 'off';
                input.placeholder = 'Jawabanmu...';
                input.value = jawab[q.id] || '';
                input.addEventListener('input', function () {
                    jawab[q.id] = input.value;
                    simpan();
                    perbaruiGrid();
                });
                wadah.append(input);
            } else {
                tanya.textContent = q.question;
                if (l.tipe === 'true_false') {
                    petunjuk.textContent = 'Pernyataan di atas benar atau salah?';
                    petunjuk.hidden = false;
                }
                const kotak = el('div', l.tipe === 'true_false' ? 'qz-tf' : '');
                q.options.forEach(function (o) {
                    const b = el('button', 'qz-opsi' + (jawab[q.id] === o ? ' dipilih' : ''), o);
                    b.type = 'button';
                    b.addEventListener('click', function () {
                        jawab[q.id] = o;
                        simpan();
                        tampil();
                    });
                    kotak.append(b);
                });
                wadah.append(kotak);
            }

            $('qzPrev').disabled = idx === 0;
            $('qzNext').textContent = idx === layar.length - 1 ? 'Periksa & Kumpulkan ✓' : 'Selanjutnya →';
            buatGrid();
        }

        function buatGrid() {
            const g = $('qzGrid');
            g.replaceChildren();
            layar.forEach(function (l, i) {
                const b = el('button', '', label(l));
                b.type = 'button';
                b.dataset.sfx = 'none';
                b.addEventListener('click', function () { idx = i; tampil(); });
                g.append(b);
            });
            g.append(el('div', 'qz-legenda', 'Hijau = sudah dijawab · Kuning = sebagian · Putih = belum dijawab'));
            perbaruiGrid();
        }

        function perbaruiGrid() {
            const tombol = $('qzGrid').querySelectorAll('button');
            layar.forEach(function (l, i) {
                tombol[i].className = [statusLayar(l), i === idx ? 'sekarang' : ''].join(' ').trim();
            });
        }

        // ---------- mengumpulkan ----------
        function bukaKonfirmasi() {
            const sudah = data.soal.filter(terisi).length;
            const kosong = data.soal.filter(function (q) { return !terisi(q); }).map(function (q) { return q.nomor; });

            let teks = 'Kamu sudah menjawab ' + sudah + ' dari ' + data.total + ' soal.';
            if (kosong.length) {
                teks += ' Yang belum dijawab: nomor ' + kosong.slice(0, 15).join(', ') + (kosong.length > 15 ? ', ...' : '') + '.';
            } else {
                teks += ' Semua sudah terjawab. 👍';
            }
            teks += ' Setelah dikumpulkan, jawaban tidak bisa diubah.';

            $('qzKonfirmasiIsi').textContent = teks;
            $('qzKonfirmasiError').hidden = true;
            $('qzKonfirmasi').hidden = false;
        }

        async function kumpulkan() {
            if (mengirim) return;
            mengirim = true;
            const tombol = $('qzKumpulkan');
            tombol.disabled = true;
            tombol.textContent = 'Mengirim...';
            $('qzKonfirmasiError').hidden = true;

            try {
                const res = await fetch(URL_KUMPULKAN, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ jawaban: jawab })
                });
                const json = await res.json().catch(function () { return null; });
                if (!res.ok) throw new Error((json && json.message) || ('Terjadi kesalahan (' + res.status + ').'));

                hapusSimpanan();
                $('qzKonfirmasi').hidden = true;
                tampilHasil(json);
            } catch (e) {
                $('qzKonfirmasiError').textContent = e.message + ' Jawabanmu masih aman, coba kumpulkan lagi.';
                $('qzKonfirmasiError').hidden = false;
            } finally {
                mengirim = false;
                tombol.disabled = false;
                tombol.textContent = 'Kumpulkan ✓';
            }
        }

        // ---------- hasil ----------
        function tampilHasil(h) {
            $('qzProgres').textContent = '';
            $('qzHasilJudul').textContent = h.lulus ? 'Selamat, kamu lulus! 🎉' : 'Belum lulus, ayo coba lagi 💪';

            const skor = $('qzSkor');
            skor.textContent = h.skor + '%';
            skor.className = 'qz-skor ' + (h.lulus ? 'lulus' : 'gagal');

            const bin = $('qzBintang');
            bin.replaceChildren();
            for (let i = 1; i <= 3; i++) {
                const s = el('span', i <= h.bintang ? 'nyala' : '', '★');
                bin.append(s);
            }

            let ringkas = h.benar + ' dari ' + h.total + ' benar · nilai lulus ' + h.nilai_lulus + '%';
            if (h.xp_didapat > 0) {
                ringkas += '\n⭐ +' + h.xp_didapat + ' XP';
            } else {
                ringkas += '\nTidak ada XP baru (soal yang sudah pernah benar tidak memberi XP lagi).';
            }
            if (h.lulus && h.level_berikutnya_terbuka) ringkas += '\n🔓 Level berikutnya sudah terbuka!';
            $('qzRingkas').textContent = ringkas;
            $('qzRingkas').style.whiteSpace = 'pre-line';

            const naik = $('qzNaik');
            if (h.naik_level) {
                naik.textContent = '🎉 Kamu naik ke Level ' + h.level_pemain + '! Cek avatar barumu di Edit Profil.';
                naik.hidden = false;
            } else {
                naik.hidden = true;
            }

            const salah = h.tinjauan.filter(function (t) { return !t.benar; }).map(function (t) { return t.nomor; });
            const boxSalah = $('qzDaftarSalah');
            if (salah.length) {
                boxSalah.textContent = '❌ Soal yang salah atau belum dijawab: nomor ' + salah.join(', ');
                boxSalah.hidden = false;
            } else {
                boxSalah.hidden = true;
            }

            renderTinjauan(h.tinjauan);
            $('qzHanyaSalah').checked = false;
            $('qzHanyaSalah').onchange = function () { renderTinjauan(h.tinjauan); };

            tampilkan('hasil');
            if (h.naik_level) bunyi('naikLevel');
            else if (h.lulus) bunyi('benar');
            else bunyi('salah');
        }

        function renderTinjauan(daftar) {
            const hanyaSalah = $('qzHanyaSalah').checked;
            const wadah = $('qzTinjau');
            wadah.replaceChildren();

            daftar.forEach(function (t) {
                if (hanyaSalah && t.benar) return;

                const item = el('div', 'qz-item' + (t.benar ? '' : ' salah'));
                item.append(el('div', 'no', t.nomor));

                const isi = el('div', 'isi');
                const jawabanmu = t.jawabanmu !== '' ? t.jawabanmu : '(tidak dijawab)';

                if (t.type === 'matching') {
                    isi.append(el('div', 'soal', t.soal + ' → ' + (t.benar ? t.kunci : jawabanmu)));
                    if (!t.benar) {
                        isi.append(el('div', 'baris salah-teks', 'Jawabanmu: ' + jawabanmu));
                        isi.append(el('div', 'baris benar-teks', 'Pasangan yang benar: ' + t.kunci));
                    }
                } else {
                    isi.append(el('div', 'soal', t.soal));
                    isi.append(el('div', 'baris ' + (t.benar ? 'benar-teks' : 'salah-teks'), 'Jawabanmu: ' + jawabanmu));
                    if (!t.benar) isi.append(el('div', 'baris benar-teks', 'Jawaban yang benar: ' + t.kunci));
                }
                if (t.penjelasan) isi.append(el('div', 'jelas', '💡 ' + t.penjelasan));

                item.append(isi);
                wadah.append(item);
            });

            if (!wadah.children.length) {
                wadah.append(el('p', 'qz-sub', 'Tidak ada soal yang salah. Hebat! 🌟'));
            }
        }

        // ---------- tombol-tombol ----------
        $('qzMulai').addEventListener('click', function () {
            idx = 0;
            tampilkan('soal');
            tampil();
        });
        $('qzPrev').addEventListener('click', function () { if (idx > 0) { idx--; tampil(); } });
        $('qzNext').addEventListener('click', function () {
            if (idx < layar.length - 1) { idx++; tampil(); } else { bukaKonfirmasi(); }
        });
        $('qzKembali').addEventListener('click', function () { $('qzKonfirmasi').hidden = true; });
        $('qzKumpulkan').addEventListener('click', kumpulkan);
        $('qzUlang').addEventListener('click', function () { window.location.reload(); });

        $('qzKeluar').addEventListener('click', function (e) {
            if (tahap === 'soal' && Object.keys(jawab).length) {
                if (!confirm('Keluar sekarang? Jawabanmu belum dinilai, tapi tetap tersimpan di perangkat ini sehingga bisa dilanjutkan.')) {
                    e.preventDefault();
                }
            }
        });

        muat();
    })();
</script>
</body>
</html>