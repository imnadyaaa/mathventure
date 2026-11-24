<?php

use App\Http\Controllers\Admin\ChatReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Soal dan materi
    Route::get('/soal', [QuestionController::class, 'levels'])->name('levels');
    Route::get('/levels/{level}/soal', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('/levels/{level}/soal/baru', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/levels/{level}/soal', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/soal/{question}/ubah', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/soal/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/soal/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    Route::get('/levels/{level}/materi', [MaterialController::class, 'edit'])->name('materials.edit');
    Route::put('/levels/{level}/materi', [MaterialController::class, 'update'])->name('materials.update');

    // Impor soal dari CSV
    Route::get('/impor/contoh', [ImportController::class, 'contoh'])->name('import.contoh');
    Route::get('/levels/{level}/impor', [ImportController::class, 'form'])->name('import.form');
    Route::post('/levels/{level}/impor', [ImportController::class, 'proses'])->name('import.proses');

    // Siswa
    Route::get('/ekspor/nilai', [ExportController::class, 'nilai'])->name('students.export');
    Route::get('/siswa', [StudentController::class, 'index'])->name('students.index');
    Route::get('/siswa/{user}', [StudentController::class, 'show'])->name('students.show');
    Route::post('/siswa/{user}/moderator', [StudentController::class, 'toggleModerator'])->name('students.moderator');
    Route::post('/siswa/{user}/buka-bisu', [StudentController::class, 'unmute'])->name('students.unmute');

    // Laporan chat
    Route::get('/chat', [ChatReportController::class, 'index'])->name('chat.index');
    Route::post('/chat/{message}/sembunyi', [ChatReportController::class, 'toggle'])->name('chat.toggle');
    Route::post('/chat/{message}/bisukan', [ChatReportController::class, 'mute'])->name('chat.mute');
    Route::post('/chat/{message}/abaikan', [ChatReportController::class, 'dismiss'])->name('chat.dismiss');
});
