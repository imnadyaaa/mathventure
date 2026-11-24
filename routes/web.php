
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\QuizController;

Route::get('/', fn () => auth()->check()
    ? redirect('/classes')
    : redirect('/login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');

    Route::get('/classes/{schoolClass}/levels', [LevelController::class, 'index'])
        ->name('levels.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::view('/splash', 'splash')->name('splash');

     Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');

    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');

    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    Route::get('/levels/{level}/kerjakan', [QuizController::class, 'show'])->name('quiz.show');

    Route::get('/levels/{level}/soal', [QuizController::class, 'soal'])->name('quiz.soal');
    
    Route::post('/levels/{level}/kumpulkan', [QuizController::class, 'kumpulkan'])->name('quiz.kumpulkan');
});

    Route::post('/levels/{level}/kumpulkan', [QuizController::class, 'kumpulkan'])->name('quiz.kumpulkan');   

Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::post('/', [ChatController::class, 'store'])->name('store')->middleware('throttle:20,1');
    Route::post('/kelas', [ChatController::class, 'setKelas'])->name('kelas');
    Route::post('/{message}/laporkan', [ChatController::class, 'report'])->name('report');
    Route::post('/{message}/bisukan', [ChatController::class, 'mute'])->name('mute');
    Route::delete('/{message}', [ChatController::class, 'destroy'])->name('destroy');
});

require __DIR__.'/admin.php';