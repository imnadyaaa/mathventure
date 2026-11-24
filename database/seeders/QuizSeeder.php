<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Mengisi kelas, 5 level per kelas, serta materi dan soal Kelas X.
 * Bentuk soal tiap level berbeda:
 *   Level 1 Dasar          : pilihan ganda (10 soal)
 *   Level 2 Pemahaman      : benar atau salah (10 soal)
 *   Level 3 Perhitungan    : isian jawaban (15 soal)
 *   Level 4 Penerapan      : menjodohkan (10 pasangan)
 *   Level 5 Evaluasi Akhir : gabungan berbagai bentuk (15 soal)
 *
 * PERHATIAN: menjalankan seeder ini akan MENGGANTI materi dan soal Kelas X dengan isi file ini.
 *
 *   php artisan db:seed --class=QuizSeeder
 */
class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1. Kelas: pakai 3 kelas yang sudah ada, atau buat kalau masih kosong
        if (DB::table('classes')->count() === 0) {
            foreach (['Kelas X', 'Kelas XI', 'Kelas XII'] as $nama) {
                DB::table('classes')->insert(['name' => $nama, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
        $kelas = DB::table('classes')->orderBy('id')->get();

        // 2. Lima level per kelas (minimum_score = nilai lulus dalam persen)
        $namaLevel = [1 => 'Dasar', 2 => 'Pemahaman', 3 => 'Perhitungan', 4 => 'Penerapan', 5 => 'Evaluasi Akhir'];
        foreach ($kelas as $k) {
            foreach ($namaLevel as $no => $nama) {
                DB::table('levels')->updateOrInsert(
                    ['class_id' => $k->id, 'level_number' => $no],
                    ['name' => $nama, 'minimum_score' => 70, 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }

        // 3. Materi dan soal Kelas X (kelas pertama): Eksponen dan Logaritma
        //    Format soal: [pertanyaan, tipe, jawaban, pilihan (atau null), penjelasan]
        //    tipe: multiple_choice, true_false, short_answer, matching
        //    matching: pertanyaan = sisi kiri, jawaban = pasangannya di sisi kanan,
        //              pilihan = semua pasangan sisi kanan dalam satu kelompok
        $kelasX = $kelas->first();

        $bank = [
            1 => [
                'materi' => ['Eksponen, Logaritma, serta Barisan dan Deret', 'EKSPONEN
Eksponen adalah perkalian berulang. Contoh: 2³ = 2 × 2 × 2 = 8.
• aᵐ × aⁿ = aᵐ⁺ⁿ
• aᵐ ÷ aⁿ = aᵐ⁻ⁿ
• (aᵐ)ⁿ = aᵐˣⁿ
• a⁰ = 1 dan a⁻ⁿ = 1/aⁿ

LOGARITMA
Logaritma adalah kebalikan eksponen: ᵃlog b = c artinya aᶜ = b. Contoh: ²log 8 = 3 karena 2³ = 8.

BARISAN DAN DERET ARITMETIKA (selisih tetap b)
• Suku ke-n: Uₙ = a + (n − 1)b
• Jumlah n suku: Sₙ = n/2 × (2a + (n − 1)b)

BARISAN DAN DERET GEOMETRI (rasio tetap r)
• Rasio: r = U₂ ÷ U₁
• Suku ke-n: Uₙ = a × rⁿ⁻¹
• Jumlah n suku (r > 1): Sₙ = a(rⁿ − 1)/(r − 1)'],
                'soal' => [
                    ['Bentuk sederhana dari 5³ × 5⁴ adalah ...', 'multiple_choice', '5⁷', ['5⁷', '5¹²', '25⁷', '10⁷'],
                        'Pangkat dijumlahkan: 5³⁺⁴ = 5⁷.'],
                    ['Bentuk sederhana dari 7⁸ ÷ 7⁵ adalah ...', 'multiple_choice', '7³', ['7³', '7¹³', '7⁴⁰', '1³'],
                        'Pangkat dikurangi: 7⁸⁻⁵ = 7³.'],
                    ['Bentuk sederhana dari (2⁴)³ adalah ...', 'multiple_choice', '2¹²', ['2⁷', '2¹²', '6⁴', '2⁸¹'],
                        'Pangkat dikalikan: 2⁴ˣ³ = 2¹².'],
                    ['Jika 2ˣ = 128, maka x = ...', 'multiple_choice', '7', ['5', '6', '7', '8'],
                        'Karena 2⁷ = 128, maka x = 7.'],
                    ['Nilai dari ⁵log 125 adalah ...', 'multiple_choice', '3', ['2', '3', '4', '5'],
                        'Karena 5³ = 125, maka ⁵log 125 = 3.'],
                    ['Suku ke-10 dari barisan 3, 7, 11, 15, ... adalah ...', 'multiple_choice', '39', ['35', '39', '43', '47'],
                        'Barisan aritmetika dengan a = 3 dan b = 4: U₁₀ = 3 + 9 × 4 = 39.'],
                    ['Rasio dari barisan 5, 15, 45, 135, ... adalah ...', 'multiple_choice', '3', ['2', '3', '5', '9'],
                        'r = 15 ÷ 5 = 3.'],
                    ['Suku ke-5 dari barisan geometri dengan a = 2 dan r = 3 adalah ...', 'multiple_choice', '162', ['54', '81', '162', '486'],
                        'U₅ = 2 × 3⁴ = 2 × 81 = 162.'],
                    ['Jumlah 5 suku pertama dari deret 4 + 8 + 16 + 32 + ... adalah ...', 'multiple_choice', '124', ['64', '96', '124', '128'],
                        'Deret geometri a = 4, r = 2: S₅ = 4(2⁵ − 1)/(2 − 1) = 4 × 31 = 124.'],
                    ['Jumlah 6 suku pertama dari deret 2 + 5 + 8 + 11 + ... adalah ...', 'multiple_choice', '57', ['45', '51', '57', '63'],
                        'Deret aritmetika a = 2, b = 3: S₆ = 6/2 × (2 × 2 + 5 × 3) = 3 × 19 = 57.'],
                ],
            ],
            2 => [
                'materi' => ['Sifat-Sifat Eksponen dan Logaritma', 'Sifat eksponen:
• aᵐ × aⁿ = aᵐ⁺ⁿ
• aᵐ ÷ aⁿ = aᵐ⁻ⁿ
• (aᵐ)ⁿ = a pangkat (m × n)

Sifat logaritma:
• ᵃlog (b × c) = ᵃlog b + ᵃlog c
• ᵃlog (b ÷ c) = ᵃlog b − ᵃlog c

Hati-hati: ᵃlog (b + c) tidak sama dengan ᵃlog b + ᵃlog c.'],
                'soal' => [
                    ['2³ × 2² = 2⁵', 'true_false', 'Benar', ['Benar', 'Salah'],
                        'Pada perkalian, pangkat dijumlahkan: 2³⁺² = 2⁵.'],
                    ['3² + 3² = 3⁴', 'true_false', 'Salah', ['Benar', 'Salah'],
                        '3² + 3² = 9 + 9 = 18, sedangkan 3⁴ = 81. Penjumlahan tidak menjumlahkan pangkat.'],
                    ['Bilangan apa pun (selain 0) yang dipangkatkan 0 hasilnya 1.', 'true_false', 'Benar', ['Benar', 'Salah'],
                        'Itu sifat a⁰ = 1 untuk a ≠ 0.'],
                    ['(2²)³ = 2⁵', 'true_false', 'Salah', ['Benar', 'Salah'],
                        'Pangkat dari pangkat dikalikan: (2²)³ = 2⁶, bukan 2⁵.'],
                    ['²log 16 = 4', 'true_false', 'Benar', ['Benar', 'Salah'],
                        'Karena 2⁴ = 16.'],
                    ['ᵃlog 1 = 1 untuk setiap bilangan pokok a', 'true_false', 'Salah', ['Benar', 'Salah'],
                        'ᵃlog 1 = 0, karena a⁰ = 1.'],
                    ['²log 4 + ²log 8 = ²log 32', 'true_false', 'Benar', ['Benar', 'Salah'],
                        'Logaritma dijumlahkan berarti bilangannya dikalikan: 4 × 8 = 32.'],
                    ['5⁻² = −25', 'true_false', 'Salah', ['Benar', 'Salah'],
                        'Pangkat negatif berarti kebalikan: 5⁻² = 1/25.'],
                    ['ᵃlog a = 1', 'true_false', 'Benar', ['Benar', 'Salah'],
                        'Karena a¹ = a.'],
                    ['²log (4 + 4) = ²log 4 + ²log 4', 'true_false', 'Salah', ['Benar', 'Salah'],
                        '²log 8 = 3, sedangkan ²log 4 + ²log 4 = 2 + 2 = 4. Yang boleh dijumlahkan adalah logaritma dari perkalian, bukan dari penjumlahan.'],
                ],
            ],
            3 => [
                'materi' => ['Cara Menghitung', 'Langkahnya: sederhanakan dulu dengan sifat eksponen atau logaritma, lalu hitung satu langkah demi satu langkah.

Contoh: 2⁴ × 2³ ÷ 2⁵ = 2⁴⁺³⁻⁵ = 2² = 4.
Contoh: ²log 4 + ²log 8 = 2 + 3 = 5.
Untuk mencari x pada 2ˣ = 32, tulis 32 sebagai 2⁵, maka x = 5.'],
                'soal' => [
                    ['Hasil dari 3⁴ adalah ...', 'short_answer', '81', null,
                        '3 × 3 × 3 × 3 = 81.'],
                    ['Hasil dari 2⁵ ÷ 2² adalah ...', 'short_answer', '8', null,
                        'Pangkat dikurangi: 2⁵⁻² = 2³ = 8.'],
                    ['Hasil dari 2⁴ × 2³ ÷ 2⁵ adalah ...', 'short_answer', '4', null,
                        '2⁴⁺³⁻⁵ = 2² = 4.'],
                    ['Nilai x yang memenuhi 2ˣ = 32 adalah ...', 'short_answer', '5', null,
                        '32 = 2⁵, jadi x = 5.'],
                    ['Nilai x yang memenuhi 3ˣ = 81 adalah ...', 'short_answer', '4', null,
                        '81 = 3⁴, jadi x = 4.'],
                    ['Hasil dari (2²)³ adalah ...', 'short_answer', '64', null,
                        '(2²)³ = 2⁶ = 64.'],
                    ['Hasil dari 5² + 3² adalah ...', 'short_answer', '34', null,
                        '25 + 9 = 34.'],
                    ['Hasil dari 10³ adalah ...', 'short_answer', '1000', null,
                        '10 × 10 × 10 = 1000.'],
                    ['Nilai dari ²log 32 adalah ...', 'short_answer', '5', null,
                        'Karena 2⁵ = 32.'],
                    ['Nilai dari ⁵log 125 adalah ...', 'short_answer', '3', null,
                        'Karena 5³ = 125.'],
                    ['Nilai dari ²log 4 + ²log 8 adalah ...', 'short_answer', '5', null,
                        '²log 4 = 2 dan ²log 8 = 3, jadi 2 + 3 = 5.'],
                    ['Nilai dari ³log 27 − ³log 3 adalah ...', 'short_answer', '2', null,
                        '³log 27 = 3 dan ³log 3 = 1, jadi 3 − 1 = 2.'],
                    ['Nilai dari ⁴log 64 adalah ...', 'short_answer', '3', null,
                        'Karena 4³ = 64.'],
                    ['Jika ²log x = 4, nilai x adalah ...', 'short_answer', '16', null,
                        'x = 2⁴ = 16.'],
                    ['Jika ³log x = 2, nilai x adalah ...', 'short_answer', '9', null,
                        'x = 3² = 9.'],
                ],
            ],
            4 => [
                'materi' => ['Cara Menjodohkan', 'Hitung dulu nilai setiap bentuk di kolom kiri, lalu cari pasangannya di kolom kanan.
Setiap pasangan hanya dipakai satu kali.'],
                'soal' => [
                    ['2⁵', 'matching', '32', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        '2 × 2 × 2 × 2 × 2 = 32.'],
                    ['3³', 'matching', '27', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        '3 × 3 × 3 = 27.'],
                    ['10²', 'matching', '100', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        '10 × 10 = 100.'],
                    ['5⁰', 'matching', '1', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        'Pangkat 0 hasilnya 1.'],
                    ['2⁻³', 'matching', '1/8', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        '2⁻³ = 1/2³ = 1/8.'],
                    ['²log 16', 'matching', '4', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        'Karena 2⁴ = 16.'],
                    ['³log 9', 'matching', '2', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        'Karena 3² = 9.'],
                    ['¹⁰log 1000', 'matching', '3', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        'Karena 10³ = 1000.'],
                    ['²log 1', 'matching', '0', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        'Karena 2⁰ = 1.'],
                    ['4²', 'matching', '16', ['32', '27', '100', '1', '1/8', '4', '2', '3', '0', '16'],
                        '4 × 4 = 16.'],
                ],
            ],
            5 => [
                'materi' => ['Ringkasan Rumus', '• aᵐ × aⁿ = aᵐ⁺ⁿ
• aᵐ ÷ aⁿ = aᵐ⁻ⁿ
• (aᵐ)ⁿ = a pangkat (m × n)
• a⁰ = 1, a⁻ⁿ = 1/aⁿ
• ᵃlog b = c artinya aᶜ = b
• ᵃlog (b × c) = ᵃlog b + ᵃlog c
• ᵃlog (b ÷ c) = ᵃlog b − ᵃlog c
• ᵃlog a = 1, ᵃlog 1 = 0'],
                'soal' => [
                    ['Bentuk sederhana dari (a³ × a⁴) ÷ a² adalah ...', 'multiple_choice', 'a⁵', ['a⁵', 'a⁹', 'a⁶', 'a¹'],
                        'Pangkat: 3 + 4 − 2 = 5, jadi a⁵.'],
                    ['Nilai dari ²log 8 + ²log 2 adalah ...', 'multiple_choice', '4', ['3', '4', '5', '6'],
                        '²log 8 = 3 dan ²log 2 = 1, jadi 3 + 1 = 4.'],
                    ['Nilai dari 8⁰ + 2⁻¹ adalah ...', 'multiple_choice', '3/2', ['1/2', '1', '3/2', '2'],
                        '8⁰ = 1 dan 2⁻¹ = 1/2, jadi 1 + 1/2 = 3/2.'],
                    ['Bentuk logaritma dari 3⁴ = 81 adalah ...', 'multiple_choice', '³log 81 = 4', ['⁴log 3 = 81', '³log 81 = 4', '³log 4 = 81', '⁸¹log 3 = 4'],
                        'aᶜ = b sama dengan ᵃlog b = c, jadi ³log 81 = 4.'],
                    ['(3²)² = 3⁴', 'true_false', 'Benar', ['Benar', 'Salah'],
                        'Pangkat dari pangkat dikalikan: 3²ˣ² = 3⁴.'],
                    ['ᵃlog (b × c) = ᵃlog b × ᵃlog c', 'true_false', 'Salah', ['Benar', 'Salah'],
                        'Yang benar: ᵃlog (b × c) = ᵃlog b + ᵃlog c (dijumlahkan, bukan dikalikan).'],
                    ['2⁻² = 1/4', 'true_false', 'Benar', ['Benar', 'Salah'],
                        '2⁻² = 1/2² = 1/4.'],
                    ['Hasil dari 2⁶ ÷ 2³ adalah ...', 'short_answer', '8', null,
                        '2⁶⁻³ = 2³ = 8.'],
                    ['Nilai dari ²log 64 adalah ...', 'short_answer', '6', null,
                        'Karena 2⁶ = 64.'],
                    ['Nilai x yang memenuhi 5ˣ = 125 adalah ...', 'short_answer', '3', null,
                        '125 = 5³, jadi x = 3.'],
                    ['Nilai dari ³log 81 − ³log 9 adalah ...', 'short_answer', '2', null,
                        '³log 81 = 4 dan ³log 9 = 2, jadi 4 − 2 = 2.'],
                    ['3²', 'matching', '9', ['9', '3', '1', '1/2'],
                        '3 × 3 = 9.'],
                    ['²log 8', 'matching', '3', ['9', '3', '1', '1/2'],
                        'Karena 2³ = 8.'],
                    ['10⁰', 'matching', '1', ['9', '3', '1', '1/2'],
                        'Pangkat 0 hasilnya 1.'],
                    ['2⁻¹', 'matching', '1/2', ['9', '3', '1', '1/2'],
                        '2⁻¹ = 1/2.'],
                ],
            ],
        ];

        foreach ($bank as $no => $isi) {
            $level = DB::table('levels')
                ->where('class_id', $kelasX->id)
                ->where('level_number', $no)
                ->first();

            // Hapus isi lama level ini supaya tidak bercampur dengan isi baru
            DB::table('questions')->where('level_id', $level->id)->delete();
            DB::table('materials')->where('level_id', $level->id)->delete();

            DB::table('materials')->insert([
                'level_id'   => $level->id,
                'title'      => $isi['materi'][0],
                'content'    => $isi['materi'][1],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($isi['soal'] as [$pertanyaan, $tipe, $jawaban, $pilihan, $penjelasan]) {
                DB::table('questions')->insert([
                    'level_id'    => $level->id,
                    'question'    => $pertanyaan,
                    'type'        => $tipe,
                    'answer'      => $jawaban,
                    'options'     => $pilihan ? json_encode($pilihan, JSON_UNESCAPED_UNICODE) : null,
                    'points'      => 10,
                    'explanation' => $penjelasan,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }
    }
}