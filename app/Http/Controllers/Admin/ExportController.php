<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Ekspor nilai semua siswa ke CSV (bisa dibuka di Excel). */
class ExportController extends Controller
{
    public function nilai(Request $request): StreamedResponse
    {
        // Excel berbahasa Indonesia memakai titik koma; ?pemisah=koma untuk Excel/Sheets berbahasa Inggris
        $pemisah = $request->query('pemisah') === 'koma' ? ',' : ';';

        $data = DB::table('users')
            ->leftJoin('scores', 'scores.user_id', '=', 'users.id')
            ->leftJoin('levels', 'levels.id', '=', 'scores.level_id')
            ->leftJoin('classes', 'classes.id', '=', 'levels.class_id')
            ->where('users.is_admin', false)
            ->orderBy('users.name')
            ->orderBy('classes.id')
            ->orderBy('levels.level_number')
            ->select(
                'users.name as nama',
                'users.username',
                'users.kelas',
                'users.level as level_pemain',
                'users.xp',
                'classes.name as nama_kelas',
                'levels.level_number',
                'levels.name as nama_level',
                'scores.score',
                'scores.stars',
                'scores.correct_count',
                'scores.passed',
                'scores.updated_at as terakhir'
            )
            ->get();

        $judul = ['Nama', 'Username', 'Kelas (chat)', 'Level pemain', 'XP', 'Kelas', 'Level', 'Skor', 'Bintang', 'Jawaban benar', 'Status', 'Terakhir dikerjakan'];

        return response()->streamDownload(function () use ($data, $judul, $pemisah) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $judul, $pemisah, '"', '');

            foreach ($data as $b) {
                $sudahMain = $b->nama_kelas !== null;

                fputcsv($out, [
                    $this->aman($b->nama),
                    $this->aman($b->username),
                    $this->aman($b->kelas),
                    $b->level_pemain,
                    $b->xp,
                    $this->aman($b->nama_kelas),
                    $sudahMain ? $b->level_number . '. ' . $b->nama_level : '',
                    $b->score,
                    $b->stars,
                    $b->correct_count,
                    $sudahMain ? ($b->passed ? 'Lulus' : 'Belum lulus') : '',
                    $b->terakhir ? Carbon::parse($b->terakhir)->format('Y-m-d H:i') : '',
                ], $pemisah, '"', '');
            }

            fclose($out);
        }, 'nilai-siswa-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Mencegah isi sel yang diawali = + - @ dibaca Excel sebagai rumus. */
    private function aman($nilai): string
    {
        $teks = (string) $nilai;

        return $teks !== '' && in_array($teks[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'" . $teks : $teks;
    }
}
