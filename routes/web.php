<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrobiologiFormController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\MikrobiologiSignatureController;
use App\Http\Controllers\MikrobiologiColumnController;
use App\Http\Controllers\MikrobiologiEntryController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('home');
});

// CSRF token refresh route
Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Pastikan hanya ada satu route manual untuk hapus kolom
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/dashboard/data', function () {
        // Cache lightweight dashboard aggregations for 60 seconds
        $judulCounts = Cache::remember('dash_mikro_judul_counts', 60, function () {
            return \App\Models\MikrobiologiForm::select('title', DB::raw('COUNT(*) as total'))
                ->groupBy('title')
                ->orderBy('title')
                ->pluck('total', 'title');
        });
        $entryCount = Cache::remember('dash_mikro_entry_count', 60, function () {
            return \App\Models\MikrobiologiEntry::count();
        });
        $approvalPending = Cache::remember('dash_mikro_approval_pending', 60, function () {
            return \App\Models\MikrobiologiForm::whereDoesntHave('signatures', function($q){
                $q->where('status', 'accept');
            })->orWhereHas('signatures', function($q){
                $q->where('status', 'accept');
            }, '<', 3)->count();
        });

        // Kimia stats
        $kimiaJudulCounts = Cache::remember('dash_kimia_judul_counts', 60, function () {
            return \App\Models\KimiaForm::select('title', DB::raw('COUNT(*) as total'))
                ->groupBy('title')
                ->orderBy('title')
                ->pluck('total', 'title');
        });
        $kimiaEntryCount = Cache::remember('dash_kimia_entry_count', 60, function () {
            return \App\Models\KimiaEntry::count();
        });
        $kimiaApprovalPending = Cache::remember('dash_kimia_approval_pending', 60, function () {
            return \App\Models\KimiaForm::whereDoesntHave('signatures', function($q){
                $q->where('status', 'accept');
            })->orWhereHas('signatures', function($q){
                $q->where('status', 'accept');
            }, '<', 3)->count();
        });

        return response()->json([
            // Mikrobiologi
            'judul_labels' => collect($judulCounts)->keys()->values(),
            'judul_data' => collect($judulCounts)->values(),
            'entry_count' => $entryCount,
            'approval_pending' => $approvalPending,
            // Kimia
            'kimia_judul_labels' => collect($kimiaJudulCounts)->keys()->values(),
            'kimia_judul_data' => collect($kimiaJudulCounts)->values(),
            'kimia_entry_count' => $kimiaEntryCount,
            'kimia_approval_pending' => $kimiaApprovalPending,
        ]);
    })->name('dashboard.data');
    Route::post('/dashboard/note', function (Illuminate\Http\Request $request) {
        $request->validate([
            'note' => 'nullable|string|max:2000',
        ]);
        $user = Auth::user();
        $user->note = $request->note;
        $user->save();
        return redirect()->route('dashboard')->with('note_saved', true);
    })->name('dashboard.note');
    // Static/export routes must come BEFORE parameterized resource routes to avoid collisions
    Route::get('/mikrobiologi-forms/export-all', [App\Http\Controllers\MikrobiologiFormController::class, 'exportAll'])->name('mikrobiologi-forms.export-all');
    Route::get('/mikrobiologi-forms/{mikrobiologi_form}/export', [App\Http\Controllers\MikrobiologiFormController::class, 'export'])->whereNumber('mikrobiologi_form')->name('mikrobiologi-forms.export');
    Route::get('/mikrobiologi-forms/{mikrobiologi_form}/export-pdf', [App\Http\Controllers\MikrobiologiFormController::class, 'exportPdf'])->whereNumber('mikrobiologi_form')->name('mikrobiologi-forms.export-pdf');
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
    
    // PATCH: Filter approval di index mikrobiologi-forms
    Route::get('/mikrobiologi-forms', function (Illuminate\Http\Request $request) {
        $search = $request->input('search');
        $search_tgl = $request->input('search_tgl');
        $group_title = $request->input('group_title');
        $perPage = $request->input('perPage', 10);
        $query = \App\Models\MikrobiologiForm::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('no', 'like', "%$search%")
                  ->orWhere('tgl_inokulasi', 'like', "%$search%")
                  ->orWhere('tgl_pengamatan', 'like', "%$search%")
                ;
            });
        }
        if ($search_tgl) {
            $query->where(function($q) use ($search_tgl) {
                $q->whereDate('tgl_inokulasi', $search_tgl)
                  ->orWhereDate('tgl_pengamatan', $search_tgl);
            });
        }
        if ($group_title) {
            $query->where('title', $group_title);
        }
        // Tambahkan filter approval
        if ($request->input('approval') === 'pending') {
            $query->whereHas('signatures', function($q){ $q->where('status', 'accept'); }, '<', 3);
        }
        $forms = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        $titles = \App\Models\MikrobiologiForm::select('title')->distinct()->orderBy('title')->pluck('title');
        $template_titles = $titles;
        return view('mikrobiologi_forms.index', compact('forms', 'search', 'search_tgl', 'group_title', 'titles', 'perPage', 'template_titles'));
    })->name('mikrobiologi-forms.index');
});
// Route kolom di luar auth untuk testing AJAX tanpa auth

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Kimia Routes - with approval filter
    Route::get('/kimia', function (Illuminate\Http\Request $request) {
        $search = $request->input('search');
        $search_tgl = $request->input('search_tgl');
        $group_title = $request->input('group_title');
        $perPage = $request->input('perPage', 10);
        $query = \App\Models\KimiaForm::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('no', 'like', "%$search%")
                  ->orWhere('tanggal', 'like', "%$search%");
            });
        }
        if ($search_tgl) {
            $query->whereDate('tanggal', $search_tgl);
        }
        if ($group_title) {
            $query->where('title', $group_title);
        }
        // Tambahkan filter approval
        if ($request->input('approval') === 'pending') {
            $query->whereDoesntHave('signatures', function($q){
                $q->where('status', 'accept');
            })->orWhereHas('signatures', function($q){
                $q->where('status', 'accept');
            }, '<', 3);
        }
        $forms = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        $titles = \App\Models\KimiaForm::select('title')->distinct()->orderBy('title')->pluck('title');
        $template_titles = $titles;
        return view('kimia_forms.index', compact('forms', 'search', 'search_tgl', 'group_title', 'titles', 'perPage', 'template_titles'));
    })->name('kimia.index');
    Route::get('/kimia/create', [App\Http\Controllers\KimiaController::class, 'create'])->name('kimia.create');
    Route::post('/kimia', [App\Http\Controllers\KimiaController::class, 'store'])->name('kimia.store');
    Route::get('/kimia/{kimia_form}', [App\Http\Controllers\KimiaController::class, 'show'])->whereNumber('kimia_form')->name('kimia.show');
    Route::get('/kimia/{kimia_form}/edit', [App\Http\Controllers\KimiaController::class, 'edit'])->whereNumber('kimia_form')->name('kimia.edit');
    Route::put('/kimia/{kimia_form}', [App\Http\Controllers\KimiaController::class, 'update'])->whereNumber('kimia_form')->name('kimia.update');
    Route::delete('/kimia/{kimia_form}', [App\Http\Controllers\KimiaController::class, 'destroy'])->whereNumber('kimia_form')->name('kimia.destroy');
    Route::post('/kimia/{kimia_form}/tables', [App\Http\Controllers\KimiaController::class, 'addTable'])->whereNumber('kimia_form')->name('kimia.tables.add');
    Route::put('/kimia-tables/{kimiaTable}', [App\Http\Controllers\KimiaController::class, 'updateTable'])->name('kimia.tables.update');
    Route::delete('/kimia-tables/{kimiaTable}', [App\Http\Controllers\KimiaController::class, 'destroyTable'])->name('kimia.tables.destroy');
    
    // Kimia Columns & Entries
    Route::post('/kimia-columns', [App\Http\Controllers\KimiaController::class, 'storeColumn'])->name('kimia-columns.store');
    Route::put('/kimia-columns/{kimiaColumn}', [App\Http\Controllers\KimiaController::class, 'updateColumn'])->name('kimia-columns.update');
    Route::delete('/kimia-columns/{kimiaColumn}', [App\Http\Controllers\KimiaController::class, 'destroyColumn'])->name('kimia-columns.destroy');
    Route::post('/kimia-entries', [App\Http\Controllers\KimiaController::class, 'storeEntry'])->name('kimia-entries.store');
    Route::put('/kimia-entries/{kimiaEntry}', [App\Http\Controllers\KimiaController::class, 'updateEntry'])->name('kimia-entries.update');
    Route::delete('/kimia-entries/{kimiaEntry}', [App\Http\Controllers\KimiaController::class, 'destroyEntry'])->name('kimia-entries.destroy');
    
    // Kimia Signatures
    Route::post('/kimia/{kimia_form}/signatures', [App\Http\Controllers\KimiaController::class, 'storeSignature'])->whereNumber('kimia_form')->name('kimia.signatures.store');
    
    // Kimia Export - static first, then parameterized
    Route::get('/kimia/export-all', [App\Http\Controllers\KimiaController::class, 'exportAll'])->name('kimia.export-all');
    Route::get('/kimia/{kimia_form}/export', [App\Http\Controllers\KimiaController::class, 'export'])->whereNumber('kimia_form')->name('kimia.export');
    Route::get('/kimia/{kimia_form}/export-pdf', [App\Http\Controllers\KimiaController::class, 'exportPdf'])->whereNumber('kimia_form')->name('kimia.export-pdf');
    // Kimia Print (PDF via browser)
    Route::get('/kimia/{kimia_form}/print', function (App\Models\KimiaForm $kimia_form) {
        $tables = $kimia_form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
        $signatures = $kimia_form->signatures()->get();
        return view('kimia_forms.print', compact('kimia_form', 'tables', 'signatures'));
    })->whereNumber('kimia_form')->name('kimia.print');
});

require __DIR__.'/auth.php';
