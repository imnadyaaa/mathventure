<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Impor banyak soal sekaligus dari file CSV (bisa dibuat di Excel). */
class ImportController extends Controller
{
    private const MAKS_BARIS = 500;

    private const MAKS_PESAN_GALAT = 20;

    /** Nama jenis soal yang diterima di file CSV. */
    private const TIPE = [
        'multiple_choice' => 'multiple_choice',
        'pilihan_ganda'   => 'multiple_choice',
        'pilihan ganda'   => 'multiple_choice',
        'pg'              => 'multiple_choice',
        'true_false'      => 'true_false',
        'benar_salah'     => 'true_false',
        'benar salah'     => 'true_false',
        'benar/salah'     => 'true_false',
        'short_answer'    => 'short_answer',
        'isian'           => 'short_answer',
        'isian_singkat'   => 'short_answer',
        'isian singkat'   => 'short_answer',
        'matching'        => 'matching',
        'menjodohkan'     => 'matching',
    ];

    public function form(Level $level): View
    {
        $level->load('schoolClass');

        return view('admin.import', [
            'level' => $level,
            'jumlahSoal' => Question::where('level_id', $level->id)->count(),
        ]);
    }

    public function proses(Request $request, Level $level): RedirectResponse
    {
        $request->validate([
            'berkas' => ['required', 'file', 'max:2048'],
        ], [
            'berkas.required' => 'Pilih file CSV dulu.',
            'berkas.max'      => 'Ukuran file maksimal 2 MB.',
            'berkas.file'     => 'File tidak bisa dibaca. Coba pilih ulang.',
        ]);

        $berkas = $request->file('berkas');
        if (!in_array(strtolower($berkas->getClientOriginalExtension()), ['csv', 'txt'], true)) {
            return back()->withErrors(['berkas' => 'File harus berformat .csv']);
        }

        // 1. Baca isi file, buang BOM, pastikan UTF-8
        $isi = (string) file_get_contents($berkas->getRealPath());
        if (str_starts_with($isi, "\xEF\xBB\xBF")) {
            $isi = substr($isi, 3);
        }
        if (!mb_check_encoding($isi, 'UTF-8')) {
            $isi = mb_convert_encoding($isi, 'UTF-8', 'Windows-1252');
        }
        if (trim($isi) === '') {
            return back()->withErrors(['berkas' => 'File kosong.']);
        }

        // 2. Tebak pemisah kolom dari baris pertama (Excel Indonesia memakai titik koma)
        $barisPertama = (string) strtok($isi, "\r\n");
        $hitung = [
            ';'  => substr_count($barisPertama, ';'),
            ','  => substr_count($barisPertama, ','),
            "\t" => substr_count($barisPertama, "\t"),
        ];
        arsort($hitung);
        $pemisah = array_key_first($hitung);
        if ($hitung[$pemisah] === 0) {
            return back()->withErrors(['berkas' => 'Baris judul kolom tidak terbaca. Pakai file contoh sebagai acuan.']);
        }

        $h = fopen('php://temp', 'r+');
        fwrite($h, $isi);
        rewind($h);

        // 3. Baca judul kolom
        $judul = fgetcsv($h, 0, $pemisah, '"', '');
        $posisi = [];
        foreach ((array) $judul as $i => $nama) {
            $posisi[mb_strtolower(trim((string) $nama))] = $i;
        }
        $cari = function (array $nama) use ($posisi) {
            foreach ($nama as $n) {
                if (array_key_exists($n, $posisi)) {
                    return $posisi[$n];
                }
            }

            return null;
        };

        $iTipe = $cari(['tipe', 'jenis', 'type']);
        $iTanya = $cari(['pertanyaan', 'soal', 'question']);
        $iJawab = $cari(['jawaban', 'kunci', 'answer']);
        $iPilihan = $cari(['pilihan', 'opsi', 'options']);
        $iJelas = $cari(['penjelasan', 'explanation']);

        if ($iTipe === null || $iTanya === null || $iJawab === null) {
            fclose($h);

            return back()->withErrors(['berkas' => 'Judul kolom harus berisi: tipe, pertanyaan, jawaban (pilihan dan penjelasan opsional).']);
        }

        // 4. Periksa setiap baris
        $soal = [];
        $galat = [];
        $nomor = 1;
        $terbaca = 0;

        while (($r = fgetcsv($h, 0, $pemisah, '"', '')) !== false) {
            $nomor++;

            $kosong = true;
            foreach ($r as $sel) {
                if (trim((string) $sel) !== '') {
                    $kosong = false;
                    break;
                }
            }
            if ($kosong) {
                continue;
            }

            if (++$terbaca > self::MAKS_BARIS) {
                $galat[] = 'File terlalu panjang. Maksimal ' . self::MAKS_BARIS . ' soal per impor.';
                break;
            }

            try {
                $soal[] = $this->periksaBaris($r, $iTipe, $iTanya, $iJawab, $iPilihan, $iJelas);
            } catch (InvalidArgumentException $e) {
                $galat[] = "Baris {$nomor}: " . $e->getMessage();
            }
        }
        fclose($h);

        if ($galat) {
            $tampil = array_slice($galat, 0, self::MAKS_PESAN_GALAT);
            if (count($galat) > self::MAKS_PESAN_GALAT) {
                $tampil[] = 'Dan ' . (count($galat) - self::MAKS_PESAN_GALAT) . ' kesalahan lain.';
            }
            array_unshift($tampil, 'Tidak ada soal yang diimpor. Perbaiki baris berikut, lalu coba lagi:');

            return back()->withErrors(['berkas' => $tampil]);
        }

        if (!$soal) {
            return back()->withErrors(['berkas' => 'File tidak berisi soal.']);
        }

        // 5. Simpan semuanya (semua berhasil atau tidak sama sekali)
        $ganti = $request->boolean('ganti');
        DB::transaction(function () use ($level, $soal, $ganti) {
            if ($ganti) {
                Question::where('level_id', $level->id)->delete();
            }
            foreach ($soal as $s) {
                Question::create($s + ['level_id' => $level->id, 'points' => 50]);
            }
        });

        return redirect()->route('admin.questions.index', $level)
            ->with('ok', count($soal) . ' soal berhasil diimpor' . ($ganti ? ' (soal lama diganti).' : '.'));
    }

    /** File contoh yang bisa diunduh admin. */
    public function contoh(): StreamedResponse
    {
        $baris = [
            ['tipe', 'pertanyaan', 'jawaban', 'pilihan', 'penjelasan'],
            ['pilihan_ganda', 'Hasil dari 2³ adalah ...', '8', '6|8|9|16', '2³ = 2 × 2 × 2 = 8.'],
            ['benar_salah', '2³ × 2² = 2⁵', 'Benar', '', 'Pada perkalian, pangkat dijumlahkan.'],
            ['isian', 'Nilai dari ²log 8 adalah ...', '3', '', 'Karena 2³ = 8.'],
            ['menjodohkan', '2³', '8', '8|9|16', 'Pasangan untuk 2³.'],
            ['menjodohkan', '3²', '9', '8|9|16', 'Pasangan untuk 3².'],
            ['menjodohkan', '4²', '16', '8|9|16', 'Pasangan untuk 4².'],
        ];

        return response()->streamDownload(function () use ($baris) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            foreach ($baris as $b) {
                fputcsv($out, $b, ';', '"', '');
            }
            fclose($out);
        }, 'contoh-soal.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Memeriksa satu baris CSV dan mengubahnya ke bentuk yang dipakai aplikasi. */
    private function periksaBaris(array $r, int $iTipe, int $iTanya, int $iJawab, ?int $iPilihan, ?int $iJelas): array
    {
        $ambil = function (?int $i) use ($r): string {
            return $i === null ? '' : trim((string) ($r[$i] ?? ''));
        };

        $tipeAsli = $ambil($iTipe);
        $tipe = self::TIPE[mb_strtolower($tipeAsli)] ?? null;
        if ($tipe === null) {
            throw new InvalidArgumentException("jenis soal \"{$tipeAsli}\" tidak dikenal. Pakai: pilihan_ganda, benar_salah, isian, atau menjodohkan.");
        }

        $tanya = $ambil($iTanya);
        $jawab = $ambil($iJawab);
        $jelas = $ambil($iJelas);

        if ($tanya === '') {
            throw new InvalidArgumentException('pertanyaan masih kosong.');
        }
        if ($jawab === '') {
            throw new InvalidArgumentException('jawaban masih kosong.');
        }
        if (mb_strlen($tanya) > 1000) {
            throw new InvalidArgumentException('pertanyaan terlalu panjang (maksimal 1000 huruf).');
        }
        if (mb_strlen($jawab) > 255) {
            throw new InvalidArgumentException('jawaban terlalu panjang (maksimal 255 huruf).');
        }
        if (mb_strlen($jelas) > 2000) {
            throw new InvalidArgumentException('penjelasan terlalu panjang (maksimal 2000 huruf).');
        }

        $opsi = null;

        if ($tipe === 'true_false') {
            $kecil = mb_strtolower($jawab);
            if (in_array($kecil, ['benar', 'true'], true)) {
                $jawab = 'Benar';
            } elseif (in_array($kecil, ['salah', 'false'], true)) {
                $jawab = 'Salah';
            } else {
                throw new InvalidArgumentException('jawaban benar/salah harus ditulis Benar atau Salah.');
            }
            $opsi = ['Benar', 'Salah'];
        } elseif ($tipe === 'multiple_choice' || $tipe === 'matching') {
            $opsi = [];
            foreach (explode('|', $ambil($iPilihan)) as $o) {
                $o = trim($o);
                if ($o !== '' && !in_array($o, $opsi, true)) {
                    $opsi[] = $o;
                }
            }
            if (count($opsi) < 2) {
                throw new InvalidArgumentException('kolom pilihan harus berisi minimal 2 pilihan, dipisah tanda |');
            }
            if (!in_array($jawab, $opsi, true)) {
                throw new InvalidArgumentException("jawaban \"{$jawab}\" harus sama persis dengan salah satu pilihan.");
            }
        }

        return [
            'type'        => $tipe,
            'question'    => $tanya,
            'answer'      => $jawab,
            'options'     => $opsi,
            'explanation' => $jelas !== '' ? $jelas : null,
        ];
    }
}
