<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\SchoolClass;
use App\Models\Score;

class LevelController extends Controller
{
    public function index(SchoolClass $schoolClass)
    {
        $levels = Level::where('class_id', $schoolClass->id)
            ->orderBy('level_number')
            ->get();

        // Nilai terbaik siswa yang sedang login untuk level-level ini
        $scores = Score::where('user_id', auth()->id())
            ->whereIn('level_id', $levels->pluck('id'))
            ->get()
            ->keyBy('level_id');

        // Level 1 selalu terbuka. Level berikutnya terbuka kalau level sebelumnya sudah lulus.
        $sebelumnyaLulus = true;

        foreach ($levels as $level) {
            $skor = $scores->get($level->id);

            $level->terbuka = $sebelumnyaLulus;
            $level->bintang = $skor ? $skor->stars : 0;

            $sebelumnyaLulus = $skor ? (bool) $skor->passed : false;
        }

        return view('levels.index', compact('schoolClass', 'levels'));
    }
}