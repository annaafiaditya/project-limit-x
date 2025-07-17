<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrobiologiFormController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MikrobiologiEntryController;
use App\Http\Controllers\MikrobiologiSignatureController;
use App\Http\Controllers\MikrobiologiColumnController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('home');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::resource('mikrobiologi-forms', MikrobiologiFormController::class);
    Route::resource('mikrobiologi-forms.entries', MikrobiologiEntryController::class)->shallow();
    Route::resource('mikrobiologi-forms.signatures', MikrobiologiSignatureController::class)->shallow();
    Route::resource('mikrobiologi-forms.columns', MikrobiologiColumnController::class)->shallow();
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
