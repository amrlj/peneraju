<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lecturer\SchoolClassController;
use App\Http\Controllers\Lecturer\SubjectController;
use App\Http\Controllers\Lecturer\QuestionController;
use App\Http\Controllers\Lecturer\ResultController;
use App\Http\Controllers\Lecturer\ExamController as LecturerExamController;
use App\Http\Controllers\Student\ExamController  as StudentExamController;
use App\Http\Controllers\Student\AttemptController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Lecturer
    Route::prefix('lecturer')->name('lecturer.')->middleware(['role:lecturer', 'verified'])->group(function () {
        Route::resource('classes', SchoolClassController::class)->except('show');
        Route::resource('subjects', SubjectController::class)->except('show');
        Route::resource('questions', QuestionController::class)->except('show');
        Route::resource('exams', LecturerExamController::class);
        Route::post('exams/{exam}/publish', [LecturerExamController::class, 'publish'])->name('exams.publish');
        Route::get('exams/{exam}/results', [ResultController::class, 'index'])->name('exams.results');
        Route::get('exams/{exam}/results/export', [ResultController::class, 'export'])->name('exams.results.export');
        Route::get('attempts/{attempt}', [ResultController::class, 'show'])->name('attempts.show');
        Route::put('attempts/{attempt}/mark', [ResultController::class, 'update'])->name('attempts.mark');

    });

        Route::prefix('student')->name('student.')->middleware(['role:student', 'verified'])->group(function () {

        Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/start', [AttemptController::class, 'start'])->name('exams.start');
        Route::get('/attempts/{attempt}', [AttemptController::class, 'show'])->name('attempts.show');
        Route::post('/attempts/{attempt}/answers', [AttemptController::class, 'saveAnswer'])->name('attempts.answers.save');
        Route::post('/attempts/{attempt}/submit', [AttemptController::class, 'submit'])->name('attempts.submit');
        Route::get('/attempts/{attempt}/result', [AttemptController::class, 'result'])->name('attempts.result');
    });
});

require __DIR__.'/auth.php';
