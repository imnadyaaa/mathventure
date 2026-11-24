<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use App\Support\ChatFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /** XP minimal untuk boleh mengirim pesan. 0 = langsung bebas. Naikkan (misalnya 30) kalau chat mulai disalahgunakan. */
    private const XP_MINIMAL = 0;

    /** Pesan otomatis disembunyikan kalau dilaporkan oleh sekian siswa berbeda. */
    private const BATAS_LAPOR = 3;

    /** Lama pembisuan oleh moderator (menit). */
    private const MENIT_BISU = 10;

    /** Jeda minimal antar pesan satu siswa (detik). */
    private const JEDA_DETIK = 2;

    /** Daftar pesan terbaru di satu ruang. */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $room = $this->namaRuang($user, (string) $request->input('room', 'semua'));

        if (!$room) {
            return response()->json(['butuh_kelas' => true], 409);
        }

        $pesan = ChatMessage::with('user:id,username,avatar,level')
            ->where('room', $room)
            ->where('hidden', false)
            ->latest('id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (ChatMessage $m) => $this->format($m, $user));

        return response()->json(['room' => $room, 'messages' => $pesan]);
    }

    /** Kirim pesan. */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->muted_until && $user->muted_until->isFuture()) {
            return response()->json([
                'message' => 'Kamu sedang dibisukan sampai pukul ' . $user->muted_until->format('H:i') . '.',
            ], 403);
        }

        if ($user->xp < self::XP_MINIMAL) {
            return response()->json([
                'message' => 'Kumpulkan ' . self::XP_MINIMAL . ' XP dulu untuk bisa mengirim pesan.',
            ], 403);
        }

        $room = $this->namaRuang($user, (string) $request->input('room', 'semua'));
        if (!$room) {
            return response()->json(['butuh_kelas' => true], 409);
        }

        $data = $request->validate([
            'body' => 'required|string|max:200',
        ], [
            'body.required' => 'Tulis pesannya dulu ya.',
            'body.max'      => 'Pesan maksimal 200 karakter.',
        ]);

        $teks = trim(preg_replace('/\s+/u', ' ', $data['body']));

        if ($pelanggaran = ChatFilter::pelanggaranKontak($teks)) {
            return response()->json(['message' => $pelanggaran], 422);
        }

        // Jeda antar pesan
        if (!Cache::add('chat:jeda:' . $user->id, 1, self::JEDA_DETIK)) {
            return response()->json(['message' => 'Pelan-pelan ya, tunggu sebentar ⏳'], 429);
        }

        $pesan = ChatMessage::create([
            'user_id' => $user->id,
            'room'    => $room,
            'body'    => ChatFilter::sensor($teks),
        ]);
        $pesan->setRelation('user', $user);

        return response()->json($this->format($pesan, $user), 201);
    }

    /** Siswa memilih kelasnya (hanya sekali). */
    public function setKelas(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->kelas) {
            return response()->json(['message' => 'Kelas sudah dipilih dan tidak bisa diubah.'], 409);
        }

        $data = $request->validate(['kelas' => 'required|in:X,XI,XII']);

        $user->kelas = $data['kelas'];
        $user->save();

        return response()->json(['kelas' => $user->kelas]);
    }

    /** Laporkan pesan. */
    public function report(Request $request, ChatMessage $message): JsonResponse
    {
        $user = $request->user();

        if (!$this->bolehAkses($user, $message->room)) {
            abort(403);
        }
        if ($message->user_id === $user->id) {
            return response()->json(['message' => 'Kamu tidak bisa melaporkan pesanmu sendiri.'], 422);
        }

        DB::table('chat_reports')->insertOrIgnore([
            'message_id' => $message->id,
            'user_id'    => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jumlah = DB::table('chat_reports')->where('message_id', $message->id)->count();
        if ($jumlah >= self::BATAS_LAPOR) {
            $message->update(['hidden' => true]);
        }

        return response()->json(['ok' => true]);
    }

    /** Moderator: sembunyikan pesan. */
    public function destroy(Request $request, ChatMessage $message): JsonResponse
    {
        abort_unless($request->user()->is_moderator, 403);

        $message->update(['hidden' => true]);

        return response()->json(['ok' => true]);
    }

    /** Moderator: bisukan pengirim pesan dan sembunyikan pesannya. */
    public function mute(Request $request, ChatMessage $message): JsonResponse
    {
        abort_unless($request->user()->is_moderator, 403);

        $pengirim = $message->user;
        if (!$pengirim->is_moderator) {
            $pengirim->forceFill(['muted_until' => now()->addMinutes(self::MENIT_BISU)])->save();
        }
        $message->update(['hidden' => true]);

        return response()->json(['ok' => true]);
    }

    /** Ruang yang boleh dibuka siswa: "semua" atau kelasnya sendiri. */
    private function namaRuang(User $user, string $room): ?string
    {
        if ($room === 'kelas') {
            return $user->kelas ? strtolower($user->kelas) : null;
        }

        return 'semua';
    }

    private function bolehAkses(User $user, string $room): bool
    {
        return $room === 'semua' || ($user->kelas && $room === strtolower($user->kelas));
    }

    private function format(ChatMessage $m, User $penonton): array
    {
        return [
            'id'       => $m->id,
            'body'     => $m->body,
            'time'     => $m->created_at->toIso8601String(),
            'username' => $m->user->username,
            'avatar'   => $m->user->avatarUrl(),
            'level'    => $m->user->level,
            'mine'     => $m->user_id === $penonton->id,
        ];
    }
}