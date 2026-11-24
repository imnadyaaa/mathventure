@extends('admin.layout')
@section('title', $question->exists ? 'Ubah soal' : 'Tambah soal')

@section('content')
@php
    $tipe = old('type', $question->type ?: 'multiple_choice');
    $opsiTeks = old('options_text', is_array($question->options) ? implode("\n", $question->options) : '');
    $jawab = old('answer', $question->answer);
@endphp

    <div class="space">
        <div>
            <h1>{{ $question->exists ? 'Ubah soal' : 'Tambah soal' }}</h1>
            <p class="sub" style="margin:0">{{ $level->schoolClass->name }}, Level {{ $level->level_number }} ({{ $level->name }})</p>
        </div>
        <a class="btn abu" href="{{ route('admin.questions.index', $level) }}">← Kembali</a>
    </div>

    <form class="card" method="POST"
          action="{{ $question->exists ? route('admin.questions.update', $question) : route('admin.questions.store', $level) }}">
        @csrf
        @if ($question->exists)
            @method('PUT')
        @endif

        <label for="type">Jenis soal</label>
        <select id="type" name="type">
            @foreach ($tipeNama as $nilai => $nama)
                <option value="{{ $nilai }}" @selected($tipe === $nilai)>{{ $nama }}</option>
            @endforeach
        </select>

        <label for="question">Pertanyaan</label>
        <textarea id="question" name="question">{{ old('question', $question->question) }}</textarea>
        <p class="hint" id="hint-pertanyaan"></p>

        <div id="box-opsi">
            <label for="opsi">Pilihan jawaban (satu pilihan per baris)</label>
            <textarea id="opsi" name="options_text">{{ $opsiTeks }}</textarea>
            <p class="hint" id="hint-opsi"></p>
        </div>

        <div id="jawab-tf">
            <label for="jawab-tf-input">Jawaban benar</label>
            <select id="jawab-tf-input" name="answer">
                <option value="Benar" @selected($jawab === 'Benar')>Benar</option>
                <option value="Salah" @selected($jawab === 'Salah')>Salah</option>
            </select>
        </div>

        <div id="jawab-teks">
            <label for="jawab-teks-input">Jawaban benar</label>
            <input type="text" id="jawab-teks-input" name="answer" value="{{ $jawab }}">
            <p class="hint" id="hint-jawab"></p>
        </div>

        <label for="explanation">Penjelasan jawaban (opsional, tampil setelah siswa selesai)</label>
        <textarea id="explanation" name="explanation">{{ old('explanation', $question->explanation) }}</textarea>

        <div class="row" style="margin-top:18px">
            <button type="submit" class="btn">Simpan</button>
            <a class="btn abu" href="{{ route('admin.questions.index', $level) }}">Batal</a>
        </div>
    </form>

    <script>
        const petunjuk = {
            multiple_choice: {
                tanya: 'Tulis pertanyaannya saja.',
                opsi: 'Tulis semua pilihan, satu per baris (minimal 2). Urutannya akan diacak saat siswa mengerjakan.',
                jawab: 'Tulis persis sama dengan salah satu pilihan di atas.'
            },
            true_false: {
                tanya: 'Tulis sebuah pernyataan yang bisa dinilai benar atau salah.',
                opsi: '',
                jawab: ''
            },
            short_answer: {
                tanya: 'Tulis pertanyaan yang jawabannya singkat, misalnya angka.',
                opsi: '',
                jawab: 'Boleh lebih dari satu jawaban yang diterima, pisahkan dengan tanda |, contoh: 0,5|1/2'
            },
            matching: {
                tanya: 'Tulis sisi kiri yang mau dijodohkan, misalnya "2³".',
                opsi: 'Tulis semua pasangan sisi kanan dalam satu kelompok, satu per baris. Soal dalam satu kelompok harus memakai daftar yang SAMA PERSIS (isi dan urutan).',
                jawab: 'Tulis pasangan yang benar untuk sisi kiri ini, persis sama dengan salah satu pilihan.'
            }
        };

        const pilihJenis = document.getElementById('type');

        function terapkan() {
            const t = pilihJenis.value;
            const pakaiOpsi = (t === 'multiple_choice' || t === 'matching');
            const tf = (t === 'true_false');

            document.getElementById('box-opsi').style.display = pakaiOpsi ? 'block' : 'none';
            document.getElementById('opsi').disabled = !pakaiOpsi;

            document.getElementById('jawab-tf').style.display = tf ? 'block' : 'none';
            document.getElementById('jawab-tf-input').disabled = !tf;

            document.getElementById('jawab-teks').style.display = tf ? 'none' : 'block';
            document.getElementById('jawab-teks-input').disabled = tf;

            document.getElementById('hint-pertanyaan').textContent = petunjuk[t].tanya;
            document.getElementById('hint-opsi').textContent = petunjuk[t].opsi;
            document.getElementById('hint-jawab').textContent = petunjuk[t].jawab;
        }

        pilihJenis.addEventListener('change', terapkan);
        terapkan();
    </script>
@endsection
