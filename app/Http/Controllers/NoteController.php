<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        return view('notes.index', [
            'notes'  => $request->user()->notes()->latest('updated_at')->get(),
            'colors' => Note::COLORS,
        ]);
    }

    public function store(Request $request)
    {
        $request->user()->notes()->create($this->validated($request));

        return redirect()->route('notes.index')->with('status', 'Catatan disimpan ✅');
    }

    public function update(Request $request, Note $note)
    {
        $this->pastikanMilikSendiri($request, $note);
        $note->update($this->validated($request));

        return redirect()->route('notes.index')->with('status', 'Catatan diperbarui ✅');
    }

    public function destroy(Request $request, Note $note)
    {
        $this->pastikanMilikSendiri($request, $note);
        $note->delete();

        return redirect()->route('notes.index')->with('status', 'Catatan dihapus 🗑️');
    }

    /** Siswa hanya boleh mengubah atau menghapus catatan miliknya sendiri. */
    private function pastikanMilikSendiri(Request $request, Note $note): void
    {
        abort_unless($note->user_id === $request->user()->id, 403);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:100',
            'body'  => 'required|string|max:2000',
            'color' => 'required|in:' . implode(',', array_keys(Note::COLORS)),
        ], [
            'title.required' => 'Judul catatan wajib diisi.',
            'title.max'      => 'Judul maksimal 100 karakter.',
            'body.required'  => 'Isi catatan wajib diisi.',
            'body.max'       => 'Isi catatan maksimal 2000 karakter.',
            'color.required' => 'Pilih warna catatan.',
            'color.in'       => 'Warna yang dipilih tidak tersedia.',
        ]);
    }
}