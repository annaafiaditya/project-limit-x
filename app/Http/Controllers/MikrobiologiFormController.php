<?php

namespace App\Http\Controllers;

use App\Models\MikrobiologiForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\FormExport;
use App\Exports\MikrobiologiCombinedExport;
use Maatwebsite\Excel\Facades\Excel;

class MikrobiologiFormController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $search_tgl = $request->input('search_tgl');
        $group_title = $request->input('group_title');
        $perPage = $request->input('perPage', 10);
        $query = MikrobiologiForm::query();
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
        
        // Filter approval: show yang accept < 3
        if ($request->input('approval') === 'pending') {
            $query->whereHas('signatures', function($q){ 
                $q->where('status', 'accept'); 
            }, '<', 3);
        }
        
        $forms = $query->with(['entries', 'signatures'])->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        $titles = MikrobiologiForm::select('title')->distinct()->orderBy('title')->pluck('title');
        $template_titles = MikrobiologiForm::select('title')->distinct()->orderBy('title')->pluck('title');
        return view('mikrobiologi_forms.index', compact('forms', 'search', 'search_tgl', 'group_title', 'titles', 'perPage', 'template_titles'));
    }

    public function create(Request $request)
    {
        $template = null;
        $columns = collect();
        if ($request->has('template_title')) {
            $template = \App\Models\MikrobiologiForm::where('title', $request->template_title)->latest()->first();
            if ($template) {
                $columns = $template->columns()->orderBy('urutan')->get();
            }
        }
        return view('mikrobiologi_forms.create', compact('template', 'columns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'no' => 'required',
            'judul_tabel' => 'nullable|string',
            'tgl_inokulasi' => 'required|date',
            'tgl_pengamatan' => 'required|date',
        ]);
        $validated['created_by'] = Auth::id();
        $form = MikrobiologiForm::create($validated);
        // Logic duplikat form jika dari template
        if ($request->has('template_title')) {
            \Log::info('DEBUG STORE: template_title', [$request->template_title]);
            $template = \App\Models\MikrobiologiForm::where('title', $request->template_title)->latest()->first();
            if ($template) {
                \Log::info('DEBUG STORE: template_id', [$template->id]);
                \Log::info('DEBUG STORE: template columns', $template->columns()->get()->toArray());
                foreach ($template->columns()->get() as $col) {
                    try {
                        \Log::info('Akan create kolom duplikat', ['form_id' => $form->id, 'col' => $col->toArray()]);
                        $newCol = \App\Models\MikrobiologiColumn::create([
                            'form_id' => $form->id,
                            'nama_kolom' => $col->nama_kolom,
                            'tipe_kolom' => $col->tipe_kolom,
                            'urutan' => $col->urutan,
                        ]);
                        \Log::info('Berhasil create kolom duplikat', ['newCol' => $newCol->toArray()]);
                    } catch (\Exception $e) {
                        \Log::error('Gagal create kolom duplikat: ' . $e->getMessage(), ['col' => $col->toArray(), 'form_id' => $form->id]);
                        abort(500, 'Gagal create kolom duplikat: ' . $e->getMessage());
                    }
                }
                foreach ($template->entries as $entry) {
                    $form->entries()->create([
                        'data' => $entry->data,
                    ]);
                }
            }
        }
        // Logic duplikat form jika dari template
        if ($request->has('columns.nama_kolom')) {
            $nama_kolom = $request->input('columns.nama_kolom');
            $tipe_kolom = $request->input('columns.tipe_kolom');
            $urutan = $request->input('columns.urutan');
            for ($i = 0; $i < count($nama_kolom); $i++) {
                \App\Models\MikrobiologiColumn::create([
                    'form_id' => $form->id,
                    'nama_kolom' => $nama_kolom[$i],
                    'tipe_kolom' => $tipe_kolom[$i],
                    'urutan' => $urutan[$i] ?? 0,
                ]);
            }
        }
        return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $form->id])->with('success', 'Form berhasil dibuat!');
    }

    public function show(MikrobiologiForm $mikrobiologi_form)
    {
        $columns = $mikrobiologi_form->columns()->orderBy('urutan')->get();
        $entries = $mikrobiologi_form->entries()->orderBy('id')->get();
        $signatures = $mikrobiologi_form->signatures()->get()->keyBy('role');
        return view('mikrobiologi_forms.show', [
            'form' => $mikrobiologi_form,
            'columns' => $columns,
            'entries' => $entries,
            'signatures' => $signatures,
        ]);
    }

    public function edit(MikrobiologiForm $mikrobiologi_form)
    {
        return view('mikrobiologi_forms.edit', ['form' => $mikrobiologi_form]);
    }

    public function update(Request $request, MikrobiologiForm $mikrobiologi_form)
    {
        $validated = $request->validate([
            'title' => 'required',
            'no' => 'required',
            'judul_tabel' => 'nullable|string',
            'tgl_inokulasi' => 'required|date',
            'tgl_pengamatan' => 'required|date',
        ]);
        $mikrobiologi_form->update($validated);
        // Jika request mengandung ?from=show, redirect ke detail form
        if ($request->query('from') === 'show') {
            return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $mikrobiologi_form->id])->with('success', 'Form berhasil diupdate!');
        }
        // Default: redirect ke index
        return redirect()->route('mikrobiologi-forms.index')->with('success', 'Form berhasil diupdate!');
    }

    public function destroy(MikrobiologiForm $mikrobiologi_form)
    {
        $mikrobiologi_form->delete();
        return redirect()->route('mikrobiologi-forms.index')->with('success', 'Form berhasil dihapus!');
    }

    public function uniqueTitles()
    {
        $titles = \App\Models\MikrobiologiForm::select('title')->distinct()->orderBy('title')->get();
        return response()->json($titles);
    }

    public function export(MikrobiologiForm $mikrobiologi_form)
    {
        $judul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $mikrobiologi_form->title);
        $no = preg_replace('/[^A-Za-z0-9_\-]/', '_', $mikrobiologi_form->no);
        $filename = $judul.'_'.$no.'.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\FormExport($mikrobiologi_form), $filename);
    }

    public function exportAll(Request $request)
    {
        $query = MikrobiologiForm::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('no', 'like', "%$search%")
                  ->orWhere('tgl_inokulasi', 'like', "%$search%")
                  ->orWhere('tgl_pengamatan', 'like', "%$search%");
            });
        }

        if ($request->filled('search_tgl')) {
            $search_tgl = $request->input('search_tgl');
            $query->where(function($q) use ($search_tgl) {
                $q->whereDate('tgl_inokulasi', $search_tgl)
                  ->orWhereDate('tgl_pengamatan', $search_tgl);
            });
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

        $filename = 'Mikrobiologi_All_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download(new MikrobiologiCombinedExport($ids), $filename);
    }

    public function exportPdf(MikrobiologiForm $mikrobiologi_form)
    {
        $columns = $mikrobiologi_form->columns()->orderBy('urutan')->get();
        $entries = $mikrobiologi_form->entries()->orderBy('id')->get();
        $signatures = $mikrobiologi_form->signatures()->get();
        
        $judul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $mikrobiologi_form->title);
        $no = preg_replace('/[^A-Za-z0-9_\-]/', '_', $mikrobiologi_form->no);
        $filename = $judul.'_'.$no.'.pdf';
        
        // Generate HTML content for PDF
        $html = view('mikrobiologi_forms.pdf', compact('mikrobiologi_form', 'columns', 'entries', 'signatures'))->render();
        
        // Return HTML for browser to handle PDF generation
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}
