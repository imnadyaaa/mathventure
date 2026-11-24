<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'jumlahSiswa'  => User::where('is_admin', false)->count(),
            'jumlahSoal'   => Question::count(),
            'jumlahMateri' => Material::count(),
            'jumlahLapor'  => DB::table('chat_reports')->distinct()->count('message_id'),
            'jumlahBisu'   => User::whereNotNull('muted_until')->where('muted_until', '>', now())->count(),
        ]);
    }
}
