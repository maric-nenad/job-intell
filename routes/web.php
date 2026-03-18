<?php

use App\Http\Controllers\CvController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

    Route::get('/jobs', fn() => view('jobs.index'))->name('jobs.index');
    Route::get('/applications', fn() => view('applications.index'))->name('applications.index');
    Route::get('/analytics', fn() => view('analytics.index'))->name('analytics.index');
    Route::get('/cvs', fn() => view('cvs.index'))->name('cvs.index');
    Route::get('/cvs/{cv}/download', [CvController::class, 'download'])->name('cvs.download');
});

require __DIR__.'/auth.php';
