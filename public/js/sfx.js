/*
 * Efek suara Mathventure
 * Bunyi dibuat langsung oleh browser (Web Audio API), jadi tidak butuh file audio.
 * Patuh pada pengaturan "Efek suara" di panel Pengaturan (localStorage: mv_sfx).
 *
 * Pemakaian:
 *   SFX.klik()  SFX.buka()  SFX.tutup()  SFX.benar()
 *   SFX.salah() SFX.xp()    SFX.naikLevel()
 *
 * Di HTML, atribut data-sfx mengatur bunyi saat elemen diklik:
 *   <button data-sfx="buka">   -> bunyi "buka"
 *   <button data-sfx="none">   -> tanpa bunyi
 *   (tanpa atribut)            -> bunyi "klik"
 */
(function () {
    const KEY = 'mv_sfx';
    const VOLUME_UTAMA = 5;
    let ctx = null;

    function aktif() {
        try { return localStorage.getItem(KEY) !== 'off'; } catch (e) { return true; }
    }

    function getCtx() {
        if (!ctx) {
            const AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) return null;
            ctx = new AC();
        }
        if (ctx.state === 'suspended') ctx.resume();
        return ctx;
    }

    // Satu nada: frekuensi, jeda mulai (detik), durasi (detik), opsi bentuk gelombang & volume
    function nada(freq, mulai, durasi, opsi) {
        const c = getCtx();
        if (!c) return;
        const o = Object.assign({ tipe: 'sine', vol: 0.15, slide: null }, opsi);
        const t = c.currentTime + mulai;

        const osc = c.createOscillator();
        const gain = c.createGain();
        osc.type = o.tipe;
        osc.frequency.setValueAtTime(freq, t);
        if (o.slide) osc.frequency.exponentialRampToValueAtTime(o.slide, t + durasi);

        gain.gain.setValueAtTime(0.0001, t);
        gain.gain.exponentialRampToValueAtTime(o.vol, t + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, t + durasi);

        osc.connect(gain);
        gain.connect(c.destination);
        osc.start(t);
        osc.stop(t + durasi + 0.05);
    }

    const bunyi = {
        klik() {
            nada(660, 0, 0.08, { tipe: 'triangle', vol: 0.10 });
        },
        buka() {
            nada(440, 0, 0.10);
            nada(660, 0.07, 0.12);
        },
        tutup() {
            nada(660, 0, 0.10);
            nada(440, 0.07, 0.12);
        },
        benar() { // tiga nada naik, ceria
            [523.25, 659.25, 783.99].forEach((f, i) =>
                nada(f, i * 0.09, 0.22, { tipe: 'triangle', vol: 0.16 }));
        },
        salah() { // dua nada turun, lembut
            nada(220, 0, 0.28, { tipe: 'sawtooth', vol: 0.07, slide: 140 });
            nada(165, 0.12, 0.30, { tipe: 'sawtooth', vol: 0.07, slide: 100 });
        },
        xp() { // "ting" bergaya koin
            nada(988, 0, 0.09, { tipe: 'square', vol: 0.05 });
            nada(1319, 0.08, 0.16, { tipe: 'square', vol: 0.05 });
        },
        naikLevel() { // fanfare singkat
            [523.25, 659.25, 783.99, 1046.5].forEach((f, i) =>
                nada(f, i * 0.11, 0.30, { tipe: 'triangle', vol: 0.16 }));
            nada(1318.5, 0.50, 0.50, { tipe: 'sine', vol: 0.12 });
        }
    };

    const SFX = {};
    Object.keys(bunyi).forEach(function (nama) {
        SFX[nama] = function () { if (aktif()) bunyi[nama](); };
    });
    window.SFX = SFX;

    // Bunyi otomatis saat tombol, link, atau kotak centang diklik
    document.addEventListener('click', function (e) {
        const el = e.target.closest('[data-sfx], button, a, input[type=checkbox]');
        if (!el) return;
        const nama = el.dataset.sfx || 'klik';
        if (nama === 'none') return;
        if (SFX[nama]) SFX[nama]();
    }, true);
})();
