<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Question;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public const TIPE = [
        'multiple_choice' => 'Pilihan ganda',
        'true_false'      => 'Benar / Salah',
        'short_answer'    => 'Isian singkat',
        'matching'        => 'Menjodohkan',
    ];

    /** Daftar semua kelas dan levelnya. */
    public function levels(): View
    {
        $kelas = SchoolClass::with(['levels' => function ($q) {
            $q->withCount(['questions', 'materials'])->orderBy('level_number');
        }])->orderBy('id')->get();

        return view('admin.levels', ['kelas' => $kelas]);
    }

    /** Daftar soal dalam satu level. */
    public function index(Level $level): View
    {
        $level->load('schoolClass');

        return view('admin.questions', [
            'level'    => $level,
            'soal'     => Question::where('level_id', $level->id)->orderBy('id')->get(),
            'tipeNama' => self::TIPE,
        ]);
    }

    public function create(Level $level): View
    {
        $level->load('schoolClass');

        return view('admin.question-form', [
            'level'    => $level,
            'question' => new Question(['type' => 'multiple_choice']),
            'tipeNama' => self::TIPE,
        ]);
    }

    public function store(Request $request, Level $level): RedirectResponse
    {
        $data = $this->validasi($request);
        Question::create($data + ['level_id' => $level->id, 'points' => 50]);

        return redirect()->route('admin.questions.index', $level)->with('ok', 'Soal berhasil ditambahkan.');
    }

    public function edit(Question $question): View
    {
        $level = $question->level()->with('schoolClass')->first();

        return view('admin.question-form', [
            'level'    => $level,
            'question' => $question,
            'tipeNama' => self::TIPE,
        ]);
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $question->update($this->validasi($request));

        return redirect()->route('admin.questions.index', $question->level_id)->with('ok', 'Soal berhasil diperbarui.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $levelId = $question->level_id;
        $question->delete();

        return redirect()->route('admin.questions.index', $levelId)->with('ok', 'Soal dihapus.');
    }

    /** Memeriksa isian form sesuai jenis soal, lalu mengubahnya ke bentuk yang dipakai aplikasi. */
    private function validasi(Request $request): array
    {
        $v = $request->validate([
            'type'         => ['required', Rule::in(array_keys(self::TIPE))],
            'question'     => ['required', 'string', 'max:1000'],
            'answer'       => ['required', 'string', 'max:255'],
            'options_text' => ['nullable', 'string', 'max:2000'],
            'explanation'  => ['nullable', 'string', 'max:2000'],
        ], [
            'question.required' => 'Pertanyaan wajib diisi.',
            'answer.required'   => 'Jawaban benar wajib diisi.',
        ]);

        $jawaban = trim($v['answer']);
        $opsi = null;

        if ($v['type'] === 'true_false') {
            $opsi = ['Benar', 'Salah'];
            if (!in_array($jawaban, $opsi, true)) {
                throw ValidationException::withMessages(['answer' => 'Jawaban harus Benar atau Salah.']);
            }
        } elseif (in_array($v['type'], ['multiple_choice', 'matching'], true)) {
            $opsi = collect(preg_split('/\R/u', (string) ($v['options_text'] ?? '')))
                ->map(fn ($o) => trim($o))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (count($opsi) < 2) {
                throw ValidationException::withMessages(['options_text' => 'Isi minimal 2 pilihan, satu pilihan per baris.']);
            }
            if (!in_array($jawaban, $opsi, true)) {
                throw ValidationException::withMessages(['answer' => 'Jawaban benar harus sama persis dengan salah satu pilihan.']);
            }
        }

        return [
            'type'        => $v['type'],
            'question'    => $v['question'],
            'answer'      => $jawaban,
            'options'     => $opsi,
            'explanation' => $v['explanation'] ?? null,
        ];
    }
}
