<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lecturer\SchoolClassController;
use App\Http\Controllers\Lecturer\SubjectController;
use App\Http\Controllers\Lecturer\QuestionController;

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

    });
});

require __DIR__.'/auth.php';
