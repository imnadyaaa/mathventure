<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Question;
use App\Models\Score;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /** Bintang: 1 kalau lulus, 2 mulai skor ini, 3 kalau semua benar. */
    private const SKOR_DUA_BINTANG = 85;

    /** Halaman mengerjakan soal. */
    public function show(Level $level)
    {
        $this->pastikanBolehMain($level);

        return view('quiz.show', ['level' => $level->load('schoolClass')]);
    }

    /** Daftar soal untuk dikerjakan. TIDAK berisi kunci jawaban maupun penjelasan. */
    public function soal(Level $level): JsonResponse
    {
        $this->pastikanBolehMain($level);

        $kolam = [];   // pilihan menjodohkan diacak sekali per kelompok
        $nomor = 0;

        $daftar = Question::where('level_id', $level->id)
            ->orderBy('id')
            ->get()
            ->map(function (Question $q) use (&$kolam, &$nomor) {
                $opsi = $q->options;

                if ($opsi && $q->type === 'matching') {
                    $kunci = md5(json_encode($opsi));
                    $kolam[$kunci] ??= collect($opsi)->shuffle()->values()->all();
                    $opsi = $kolam[$kunci];
                } elseif ($opsi && $q->type === 'multiple_choice') {
                    $opsi = collect($opsi)->shuffle()->values()->all();
                }

                return [
                    'id'       => $q->id,
                    'nomor'    => ++$nomor,
                    'type'     => $q->type,
                    'question' => $q->question,
                    'options'  => $opsi,
                ];
            })
            ->values();

        $materi = $level->materials()->first();

        return response()->json([
            'level' => [
                'id'          => $level->id,
                'nomor'       => $level->level_number,
                'nama'        => $level->name,
                'kelas'       => $level->schoolClass?->name,
                'nilai_lulus' => $level->minimum_score,
            ],
            'materi' => $materi ? ['judul' => $materi->title, 'isi' => $materi->content] : null,
            'total'  => $daftar->count(),
            'soal'   => $daftar,
        ]);
    }

    /** Siswa mengumpulkan jawaban. Dinilai di sini. */
    public function kumpulkan(Request $request, Level $level): JsonResponse
    {
        $this->pastikanBolehMain($level);

        $request->validate([
            'jawaban'   => 'nullable|array',
            'jawaban.*' => 'nullable|string|max:200',
        ]);
        $jawaban = $request->input('jawaban', []);

        $user = $request->user();
        $soal = Question::where('level_id', $level->id)->orderBy('id')->get();

        if ($soal->isEmpty()) {
            return response()->json(['message' => 'Soal untuk level ini belum tersedia.'], 422);
        }

        // 1. Periksa setiap jawaban
        $tinjauan = [];
        $benarIds = [];
        foreach ($soal as $i => $q) {
            $kunci = (string) $q->answer;
            $jawab = trim((string) ($jawaban[$q->id] ?? ''));
            $benar = $jawab !== '' && $this->sama($jawab, $kunci, $q->type);

            if ($benar) {
                $benarIds[] = $q->id;
            }

            $tinjauan[] = [
                'nomor'     => $i + 1,
                'type'      => $q->type,
                'soal'      => $q->question,
                'jawabanmu' => $jawab,
                'kunci'     => explode('|', $kunci)[0],
                'benar'     => $benar,
                'penjelasan' => $q->explanation,
            ];
        }

        // 2. Hitung nilai dan bintang
        $total = $soal->count();
        $jumlahBenar = count($benarIds);
        $skor = (int) round($jumlahBenar / $total * 100);
        $lulus = $skor >= $level->minimum_score;
        $bintang = ! $lulus ? 0 : ($skor >= 100 ? 3 : ($skor >= self::SKOR_DUA_BINTANG ? 2 : 1));

        // 3. Simpan nilai, XP, dan progres
        $levelAwal = $user->level;
        $xpDidapat = 0;
        $levelBerikutnyaTerbuka = false;

        DB::transaction(function () use ($user, $level, $soal, $benarIds, $skor, $lulus, $bintang, $jumlahBenar, &$xpDidapat, &$levelBerikutnyaTerbuka) {
            // XP hanya untuk soal yang BARU PERTAMA KALI dijawab benar
            $sudah = DB::table('question_user')
                ->where('user_id', $user->id)
                ->whereIn('question_id', $benarIds)
                ->pluck('question_id')
                ->all();
            $baru = array_values(array_diff($benarIds, $sudah));

            if ($baru) {
                $xpDidapat = (int) $soal->whereIn('id', $baru)->sum('points');

                DB::table('question_user')->insert(array_map(fn ($id) => [
                    'user_id'     => $user->id,
                    'question_id' => $id,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ], $baru));

                $user->addXp($xpDidapat);
            }

            // Nilai terbaik disimpan
            $lama = Score::where('user_id', $user->id)->where('level_id', $level->id)->first();
            if (! $lama) {
                Score::create([
                    'user_id'       => $user->id,
                    'level_id'      => $level->id,
                    'score'         => $skor,
                    'passed'        => $lulus,
                    'stars'         => $bintang,
                    'correct_count' => $jumlahBenar,
                ]);
            } else {
                $data = ['passed' => $lama->passed || $lulus];
                if ($skor > $lama->score) {
                    $data += ['score' => $skor, 'stars' => $bintang, 'correct_count' => $jumlahBenar];
                }
                $lama->update($data);
            }

            // Progres: level ini selesai, level berikutnya terbuka
            if ($lulus) {
                $this->tandaiProgres($user->id, $level->id, ['is_unlocked' => true, 'is_completed' => true]);

                $berikutnya = Level::where('class_id', $level->class_id)
                    ->where('level_number', $level->level_number + 1)
                    ->first();
                if ($berikutnya) {
                    $this->tandaiProgres($user->id, $berikutnya->id, ['is_unlocked' => true]);
                    $levelBerikutnyaTerbuka = true;
                }
            }
        });

        return response()->json([
            'skor'         => $skor,
            'benar'        => $jumlahBenar,
            'total'        => $total,
            'lulus'        => $lulus,
            'nilai_lulus'  => $level->minimum_score,
            'bintang'      => $bintang,
            'xp_didapat'   => $xpDidapat,
            'xp_total'     => $user->xp,
            'level_pemain' => $user->level,
            'naik_level'   => $user->level > $levelAwal,
            'level_berikutnya_terbuka' => $levelBerikutnyaTerbuka,
            'tinjauan'     => $tinjauan,
        ]);
    }

    /** Level 1 selalu terbuka. Level lain terbuka kalau level sebelumnya sudah lulus. */
    private function pastikanBolehMain(Level $level): void
    {
        if ($level->level_number <= 1) {
            return;
        }

        $sebelumnya = Level::where('class_id', $level->class_id)
            ->where('level_number', $level->level_number - 1)
            ->first();

        if (! $sebelumnya) {
            return;
        }

        $sudahLulus = Score::where('user_id', auth()->id())
            ->where('level_id', $sebelumnya->id)
            ->where('passed', true)
            ->exists();

        abort_unless($sudahLulus, 403, 'Level ini masih terkunci.');
    }

    private function tandaiProgres(int $userId, int $levelId, array $nilai): void
    {
        $baris = DB::table('progress')->where('user_id', $userId)->where('level_id', $levelId);

        if ($baris->exists()) {
            $baris->update($nilai + ['updated_at' => now()]);
        } else {
            DB::table('progress')->insert($nilai + [
                'user_id'      => $userId,
                'level_id'     => $levelId,
                'is_unlocked'  => false,
                'is_completed' => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    /** Membandingkan jawaban siswa dengan kunci. */
    private function sama(string $jawab, string $kunci, string $tipe): bool
    {
        if ($tipe !== 'short_answer') {
            return trim($jawab) === trim($kunci);
        }

        // Isian: spasi diabaikan, koma dianggap titik, beberapa jawaban diterima dipisah tanda |
        $j = $this->rapikan($jawab);
        foreach (explode('|', $kunci) as $k) {
            $k = $this->rapikan($k);
            if ($j === $k) {
                return true;
            }
            if (is_numeric($j) && is_numeric($k) && abs((float) $j - (float) $k) < 1e-9) {
                return true;
            }
        }

        return false;
    }

    private function rapikan(string $teks): string
    {
        $teks = mb_strtolower(trim($teks));

        return str_replace([' ', ',', '−'], ['', '.', '-'], $teks);
    }
}