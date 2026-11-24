<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ChatReportController extends Controller
{
    /** Pesan yang dilaporkan atau disembunyikan, plus siswa yang sedang dibisukan. */
    public function index(): View
    {
        $laporan = DB::table('chat_reports')
            ->select('message_id', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('message_id')
            ->pluck('jumlah', 'message_id');

        $pesan = ChatMessage::with('user')
            ->where(function ($w) use ($laporan) {
                $w->whereIn('id', $laporan->keys()->all())->orWhere('hidden', true);
            })
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $dibisukan = User::whereNotNull('muted_until')
            ->where('muted_until', '>', now())
            ->orderBy('muted_until')
            ->get();

        return view('admin.chat', ['pesan' => $pesan, 'laporan' => $laporan, 'dibisukan' => $dibisukan]);
    }

    /** Sembunyikan atau tampilkan kembali sebuah pesan. */
    public function toggle(ChatMessage $message): RedirectResponse
    {
        $message->update(['hidden' => !$message->hidden]);

        return back()->with('ok', $message->hidden ? 'Pesan disembunyikan.' : 'Pesan ditampilkan kembali.');
    }

    /** Bisukan pengirim pesan untuk beberapa menit dan sembunyikan pesannya. */
    public function mute(Request $request, ChatMessage $message): RedirectResponse
    {
        $data = $request->validate(['menit' => ['required', 'integer', 'in:10,60,1440,10080']]);

        $pengirim = $message->user;
        if ($pengirim->is_admin || $pengirim->is_moderator) {
            return back()->withErrors(['menit' => 'Admin dan moderator tidak bisa dibisukan.']);
        }

        $pengirim->forceFill(['muted_until' => now()->addMinutes((int) $data['menit'])])->save();
        $message->update(['hidden' => true]);

        return back()->with('ok', "{$pengirim->name} dibisukan dan pesannya disembunyikan.");
    }

    /** Laporan dianggap tidak bermasalah: hapus laporan dan tampilkan lagi pesannya. */
    public function dismiss(ChatMessage $message): RedirectResponse
    {
        DB::table('chat_reports')->where('message_id', $message->id)->delete();
        $message->update(['hidden' => false]);

        return back()->with('ok', 'Laporan diabaikan dan pesan ditampilkan kembali.');
    }
}
