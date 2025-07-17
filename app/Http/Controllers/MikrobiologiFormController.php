<?php

namespace App\Http\Controllers;

use App\Models\MikrobiologiForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $forms = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        $titles = MikrobiologiForm::select('title')->distinct()->orderBy('title')->pluck('title');
        return view('mikrobiologi_forms.index', compact('forms', 'search', 'search_tgl', 'group_title', 'titles', 'perPage'));
    }

    public function create()
    {
        return view('mikrobiologi_forms.create');
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
        return redirect()->route('mikrobiologi-forms.show', $form)->with('success', 'Form berhasil dibuat!');
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
        return redirect()->route('mikrobiologi-forms.show', $mikrobiologi_form)->with('success', 'Form berhasil diupdate!');
    }

    public function destroy(MikrobiologiForm $mikrobiologi_form)
    {
        $mikrobiologi_form->delete();
        return redirect()->route('mikrobiologi-forms.index')->with('success', 'Form berhasil dihapus!');
    }
}
