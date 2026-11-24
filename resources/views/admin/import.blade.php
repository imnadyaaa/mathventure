@extends('admin.layout')
@section('title', 'Impor soal')

@section('content')
    <div class="space">
        <div>
            <h1>Impor soal dari CSV</h1>
            <p class="sub" style="margin:0">{{ $level->schoolClass->name }}, Level {{ $level->level_number }} ({{ $level->name }}) · saat ini {{ $jumlahSoal }} soal</p>
        </div>
        <a class="btn abu" href="{{ route('admin.questions.index', $level) }}">← Kembali</a>
    </div>

    <div class="card">
        <strong>Cara memakai</strong>
        <ol style="line-height:1.7; padding-left:20px; margin:8px 0 0">
            <li>Unduh <a href="{{ route('admin.import.contoh') }}">file contoh</a>, lalu buka di Excel.</li>
            <li>Ganti isinya dengan soalmu, satu soal per baris. Jangan ubah baris judul (baris pertama).</li>
            <li>Simpan sebagai <b>CSV UTF-8</b> (di Excel: Save As, lalu pilih "CSV UTF-8 (Comma delimited)"). UTF-8 penting supaya simbol seperti ², ³, dan ⁵log tidak rusak.</li>
            <li>Unggah filenya di bawah ini.</li>
        </ol>
    </div>

    <div class="card scroll">
        <strong>Isi tiap kolom</strong>
        <table style="margin-top:8px">
            <thead><tr><th>Kolom</th><th>Isi</th></tr></thead>
            <tbody>
                <tr><td><b>tipe</b></td><td><code>pilihan_ganda</code>, <code>benar_salah</code>, <code>isian</code>, atau <code>menjodohkan</code></td></tr>
                <tr><td><b>pertanyaan</b></td><td>Teks soal. Untuk menjodohkan: sisi kiri.</td></tr>
                <tr><td><b>jawaban</b></td><td>Jawaban benar. Untuk benar_salah: <code>Benar</code> atau <code>Salah</code>. Untuk isian boleh beberapa jawaban, dipisah <code>|</code>.</td></tr>
                <tr><td><b>pilihan</b></td><td>Khusus pilihan_ganda dan menjodohkan: semua pilihan dipisah <code>|</code>, contoh <code>6|8|9|16</code>. Kosongkan untuk jenis lain.</td></tr>
                <tr><td><b>penjelasan</b></td><td>Opsional. Tampil setelah siswa selesai.</td></tr>
            </tbody>
        </table>
        <p class="hint">Untuk menjodohkan, semua soal dalam satu kelompok harus memakai isi kolom pilihan yang sama persis. Jawaban harus sama persis dengan salah satu pilihan.</p>
    </div>

    <form class="card" method="POST" action="{{ route('admin.import.proses', $level) }}" enctype="multipart/form-data">
        @csrf

        <label for="berkas">File CSV</label>
        <input type="file" id="berkas" name="berkas" accept=".csv,.txt" required>

        <label style="font-weight:400; display:flex; gap:8px; align-items:flex-start; margin-top:16px">
            <input type="checkbox" name="ganti" value="1" style="margin-top:4px">
            <span>Hapus dulu semua soal yang sudah ada di level ini, lalu ganti dengan isi file.<br>
                <span class="hint">Kalau tidak dicentang, soal dari file ditambahkan setelah soal yang sudah ada.</span></span>
        </label>

        <div class="row" style="margin-top:18px">
            <button type="submit" class="btn">Impor soal</button>
            <a class="btn abu" href="{{ route('admin.questions.index', $level) }}">Batal</a>
        </div>
        <p class="hint">Kalau ada satu baris yang salah, tidak ada soal yang diimpor dan kamu akan diberi tahu baris mana yang perlu diperbaiki.</p>
    </form>
@endsection
