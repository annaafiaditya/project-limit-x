<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrobiologiFormController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MikrobiologiSignatureController;
use App\Http\Controllers\MikrobiologiColumnController;
use App\Http\Controllers\MikrobiologiEntryController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('home');
});

// Pastikan hanya ada satu route manual untuk hapus kolom
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::resource('mikrobiologi-forms', MikrobiologiFormController::class);
    Route::resource('mikrobiologi-forms.signatures', MikrobiologiSignatureController::class)->shallow();
    // Route kolom dinamis hanya untuk store dan destroy
    Route::post('/columns', [MikrobiologiColumnController::class, 'store'])->name('columns.store');
    Route::put('/columns/{id}', [MikrobiologiColumnController::class, 'update'])->name('columns.update');
    Route::delete('/columns/{id}', [MikrobiologiColumnController::class, 'destroy'])->name('columns.destroy');
    Route::post('/mikrobiologi-forms/{form}/entries', [MikrobiologiEntryController::class, 'store'])->name('mikrobiologi-forms.entries.store');
    Route::delete('/entries/{mikrobiologiEntry}', [MikrobiologiEntryController::class, 'destroy'])->name('entries.destroy');
    Route::put('/entries/{mikrobiologiEntry}', [MikrobiologiEntryController::class, 'update'])->name('entries.update');
    Route::get('/template-forms/unique-titles', [App\Http\Controllers\MikrobiologiFormController::class, 'uniqueTitles'])->name('template-forms.unique-titles');
    Route::get('/mikrobiologi-forms/{mikrobiologi_form}/export', [App\Http\Controllers\MikrobiologiFormController::class, 'export'])->name('mikrobiologi-forms.export');
});
// Route kolom di luar auth untuk testing AJAX tanpa auth

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
