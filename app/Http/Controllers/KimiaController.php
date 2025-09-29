<?php

namespace App\Http\Controllers;

use App\Models\KimiaForm;
use App\Models\KimiaColumn;
use App\Models\KimiaEntry;
use App\Models\KimiaSignature;
use App\Models\KimiaTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class KimiaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $search_tgl = $request->input('search_tgl');
        $group_title = $request->input('group_title');
        $perPage = $request->input('perPage', 10);
        
        $query = KimiaForm::query();
        
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
        
        // Filter approval: show yang accept < 3
        if ($request->input('approval') === 'pending') {
            $query->whereHas('signatures', function($q){ 
                $q->where('status', 'accept'); 
            }, '<', 3);
        }
        
        $forms = $query->with(['entries', 'signatures'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));
        
        $titles = Cache::remember('kimia_distinct_titles', 120, function(){
            return KimiaForm::select('title')->distinct()->orderBy('title')->pluck('title');
        });
        $template_titles = $titles;
        
        return view('kimia_forms.index', compact('forms', 'search', 'search_tgl', 'group_title', 'titles', 'perPage', 'template_titles'));
    }

    public function create(Request $request)
    {
        $template = null;
        $tables = collect();
        
        if ($request->has('template_title')) {
            $template = KimiaForm::where('title', $request->template_title)
                ->with(['tables.columns', 'tables.entries'])
                ->latest()->first();
            if ($template) {
                $tables = $template->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }])->get();
            }
        }
        
        return view('kimia_forms.create', compact('template', 'tables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'no' => 'required',
            'tanggal' => 'required|date',
        ]);
        
        $validated['created_by'] = Auth::id();
        $form = KimiaForm::create($validated);
        
        // Duplikat semua tabel jika dari template
        if ($request->has('template_title')) {
            $template = KimiaForm::where('title', $request->template_title)
                ->with(['tables.columns', 'tables.entries'])
                ->latest()->first();
            if ($template) {
                foreach ($template->tables as $templateTable) {
                    // Buat tabel baru
                    $newTable = KimiaTable::create([
                        'form_id' => $form->id,
                        'name' => $templateTable->name,
                    ]);
                    
                    // Duplikat kolom untuk tabel ini
                    foreach ($templateTable->columns as $col) {
                        KimiaColumn::create([
                            'form_id' => $form->id,
                            'table_id' => $newTable->id,
                            'nama_kolom' => $col->nama_kolom,
                            'tipe_kolom' => $col->tipe_kolom,
                            'urutan' => $col->urutan,
                        ]);
                    }
                    
                    // Duplikat entries untuk tabel ini
                    foreach ($templateTable->entries as $entry) {
                        KimiaEntry::create([
                            'form_id' => $form->id,
                            'table_id' => $newTable->id,
                            'data' => $entry->data,
                        ]);
                    }
                }
            }
        } else {
            // Buat tabel default pertama jika bukan dari template
            $table = KimiaTable::create([
                'form_id' => $form->id,
                'name' => 'Tabel 1',
            ]);
            
            // Duplikat kolom jika dari template (cara lama)
            if ($request->has('columns.nama_kolom')) {
                $nama_kolom = $request->input('columns.nama_kolom');
                $tipe_kolom = $request->input('columns.tipe_kolom');
                $urutan = $request->input('columns.urutan');
                
                for ($i = 0; $i < count($nama_kolom); $i++) {
                    KimiaColumn::create([
                        'form_id' => $form->id,
                        'table_id' => $table->id,
                        'nama_kolom' => $nama_kolom[$i],
                        'tipe_kolom' => $tipe_kolom[$i],
                        'urutan' => $urutan[$i] ?? 0,
                    ]);
                }
            }
        }
        
        return redirect()->route('kimia.show', ['kimia_form' => $form->id])->with('success', 'Form berhasil dibuat!');
    }

    public function show(KimiaForm $kimia_form)
    {
        $tables = $kimia_form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
        $signatures = $kimia_form->signatures()->get()->keyBy('role');
        
        return view('kimia_forms.show', [
            'form' => $kimia_form,
            'tables' => $tables,
            'signatures' => $signatures,
        ]);
    }

    public function addTable(Request $request, KimiaForm $kimia_form)
    {
        $validated = $request->validate([
            'name' => 'required|string'
        ]);
        KimiaTable::create([
            'form_id' => $kimia_form->id,
            'name' => $validated['name']
        ]);
        return redirect()->route('kimia.show', $kimia_form)->with('success', 'Tabel berhasil ditambahkan');
    }

    public function updateTable(Request $request, KimiaTable $kimiaTable)
    {
        $validated = $request->validate([
            'name' => 'required|string'
        ]);
        
        $kimiaTable->update($validated);
        
        if ($request->wantsJson()) {
            return response()->json($kimiaTable);
        }
        return redirect()->route('kimia.show', ['kimia_form' => $kimiaTable->form_id])->with('success', 'Tabel berhasil diupdate!');
    }

    public function destroyTable(KimiaTable $kimiaTable)
    {
        $form_id = $kimiaTable->form_id;
        $kimiaTable->delete();
        
        return redirect()->route('kimia.show', ['kimia_form' => $form_id])->with('success', 'Tabel berhasil dihapus!');
    }

    public function storeColumn(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:kimia_forms,id',
            'table_id' => 'required|exists:kimia_tables,id',
            'nama_kolom' => 'required|string',
            'tipe_kolom' => 'required|in:string,integer,date,time,decimal',
            'urutan' => 'nullable|integer',
        ]);
        
        $column = KimiaColumn::create($validated);
        
        if ($request->wantsJson()) {
            return response()->json($column);
        }
        return back()->with('success', 'Kolom berhasil ditambahkan!');
    }

    public function updateColumn(Request $request, KimiaColumn $kimiaColumn)
    {
        $validated = $request->validate([
            'nama_kolom' => 'required|string',
            'tipe_kolom' => 'required|in:string,integer,date,time,decimal',
            'urutan' => 'nullable|integer',
        ]);
        
        $kimiaColumn->update($validated);
        
        if ($request->wantsJson()) {
            return response()->json($kimiaColumn);
        }
        return back()->with('success', 'Kolom berhasil diupdate!');
    }

    public function destroyColumn(KimiaColumn $kimiaColumn)
    {
        $kimiaColumn->delete();
        
        return back()->with('success', 'Kolom berhasil dihapus!');
    }

    public function storeEntry(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:kimia_forms,id',
            'table_id' => 'required|exists:kimia_tables,id',
            'data' => 'required|array',
        ]);
        
        $entry = KimiaEntry::create($validated);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($entry);
        }
        
        return back()->with('success', 'Data entry berhasil ditambah!');
    }

    public function updateEntry(Request $request, KimiaEntry $kimiaEntry)
    {
        try {
            $validated = $request->validate([
                'data' => 'required|array',
            ]);
            $kimiaEntry->update($validated);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'updated' => true]);
            }
            return redirect()->route('kimia.show', ['kimia_form' => $kimiaEntry->form_id])->with('success', 'Data entry berhasil diupdate!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal update entry: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal update entry!');
        }
    }

    public function destroyEntry(KimiaEntry $kimiaEntry)
    {
        $form_id = $kimiaEntry->form_id;
        $kimiaEntry->delete();
        
        return redirect()->route('kimia.show', ['kimia_form' => $form_id])->with('success', 'Data entry berhasil dihapus!');
    }

    public function storeSignature(Request $request, KimiaForm $kimia_form)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:kimia_forms,id',
            'role' => 'required|in:technician,staff,supervisor',
            'name' => 'required|string',
            'jabatan' => 'required|string',
            'status' => 'required|in:accept,reject',
            'tanggal' => 'required|date',
        ]);
        
        KimiaSignature::create($validated);
        
        return redirect()->route('kimia.show', ['kimia_form' => $kimia_form->id])->with('success', 'Signature berhasil disimpan!');
    }

    public function edit(KimiaForm $kimia_form)
    {
        return view('kimia_forms.edit', ['form' => $kimia_form]);
    }

    public function update(Request $request, KimiaForm $kimia_form)
    {
        $validated = $request->validate([
            'title' => 'required',
            'no' => 'required',
            'tanggal' => 'required|date',
        ]);
        
        $kimia_form->update($validated);
        
        if ($request->query('from') === 'show') {
            return redirect()->route('kimia.show', ['kimia_form' => $kimia_form->id])->with('success', 'Form berhasil diupdate!');
        }
        
        return redirect()->route('kimia.index')->with('success', 'Form berhasil diupdate!');
    }

    public function destroy(KimiaForm $kimia_form)
    {
        $kimia_form->delete();
        return redirect()->route('kimia.index')->with('success', 'Form berhasil dihapus!');
    }

    public function export(KimiaForm $kimia_form)
    {
        $judul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $kimia_form->title);
        $no = preg_replace('/[^A-Za-z0-9_\-]/', '_', $kimia_form->no);
        $filename = $judul.'_'.$no.'.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KimiaFormExport($kimia_form), $filename);
    }

    public function exportAll(Request $request)
    {
        $query = KimiaForm::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('no', 'like', "%$search%")
                  ->orWhere('tanggal', 'like', "%$search%");
            });
        }

        if ($request->filled('search_tgl')) {
            $query->whereDate('tanggal', $request->input('search_tgl'));
        }

        if ($request->filled('group_title')) {
            $query->where('title', $request->input('group_title'));
        }

        if ($request->input('approval') === 'pending') {
            $query->whereHas('signatures', function($q){ $q->where('status', 'accept'); }, '<', 3);
        }

        $ids = $query->pluck('id')->toArray();

        if (empty($ids)) {
            return back()->with('export_error', 'Tidak ada data sesuai filter untuk diexport.');
        }

        $filename = 'Kimia_All_'.now()->format('Ymd_His').'.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KimiaCombinedExport($ids), $filename);
    }

    public function exportPdf(KimiaForm $kimia_form)
    {
        $tables = $kimia_form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
        $signatures = $kimia_form->signatures()->get();
        
        $judul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $kimia_form->title);
        $no = preg_replace('/[^A-Za-z0-9_\-]/', '_', $kimia_form->no);
        $filename = $judul.'_'.$no.'.pdf';
        
        // Generate HTML content for PDF
        $html = view('kimia_forms.pdf', compact('kimia_form', 'tables', 'signatures'))->render();
        
        // Return HTML for browser to handle PDF generation
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}
