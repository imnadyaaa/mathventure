<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialController extends Controller
{
    /** Satu level punya satu materi (yang ditampilkan di awal kuis). */
    public function edit(Level $level): View
    {
        $level->load('schoolClass');

        return view('admin.material', [
            'level'    => $level,
            'material' => Material::where('level_id', $level->id)->orderBy('id')->first() ?? new Material(),
        ]);
    }

    public function update(Request $request, Level $level): RedirectResponse
    {
        $data = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:20000'],
        ], [
            'title.required'   => 'Judul materi wajib diisi.',
            'content.required' => 'Isi materi wajib diisi.',
        ]);

        $materi = Material::where('level_id', $level->id)->orderBy('id')->first();
        if ($materi) {
            $materi->update($data);
        } else {
            Material::create($data + ['level_id' => $level->id]);
        }

        return redirect()->route('admin.levels')->with('ok', 'Materi berhasil disimpan.');
    }
}
