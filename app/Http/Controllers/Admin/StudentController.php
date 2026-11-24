<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('q', ''));

        $siswa = User::query()
            ->where('is_admin', false)
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($w) use ($cari) {
                    $w->where('name', 'like', "%{$cari}%")->orWhere('username', 'like', "%{$cari}%");
                });
            })
            ->orderByDesc('xp')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        // Jumlah level yang sudah lulus, per siswa
        $lulus = DB::table('scores')
            ->whereIn('user_id', $siswa->getCollection()->pluck('id'))
            ->where('passed', true)
            ->select('user_id', DB::raw('COUNT(DISTINCT level_id) as jumlah'))
            ->groupBy('user_id')
            ->pluck('jumlah', 'user_id');

        return view('admin.students', ['siswa' => $siswa, 'lulus' => $lulus, 'cari' => $cari]);
    }

    public function show(User $user): View
    {
        $nilai = DB::table('scores')
            ->join('levels', 'levels.id', '=', 'scores.level_id')
            ->join('classes', 'classes.id', '=', 'levels.class_id')
            ->where('scores.user_id', $user->id)
            ->orderBy('classes.id')
            ->orderBy('levels.level_number')
            ->orderByDesc('scores.id')
            ->select('scores.*', 'levels.name as nama_level', 'levels.level_number', 'classes.name as nama_kelas')
            ->get();

        return view('admin.student', ['siswa' => $user, 'nilai' => $nilai]);
    }

    public function toggleModerator(User $user): RedirectResponse
    {
        $user->forceFill(['is_moderator' => !$user->is_moderator])->save();

        return back()->with('ok', $user->is_moderator
            ? "{$user->name} sekarang moderator chat."
            : "{$user->name} bukan lagi moderator chat.");
    }

    public function unmute(User $user): RedirectResponse
    {
        $user->forceFill(['muted_until' => null])->save();

        return back()->with('ok', "{$user->name} sudah boleh chat lagi.");
    }
}
