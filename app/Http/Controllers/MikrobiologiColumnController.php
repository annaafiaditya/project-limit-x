<?php

namespace App\Http\Controllers;

use App\Models\MikrobiologiColumn;
use Illuminate\Http\Request;

class MikrobiologiColumnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:mikrobiologi_forms,id',
            'nama_kolom' => 'required|string',
            'tipe_kolom' => 'required|string',
            'urutan' => 'nullable|integer',
        ]);
        MikrobiologiColumn::create($validated);
        return redirect()->route('mikrobiologi-forms.show', $request->form_id)->with('success', 'Kolom berhasil ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MikrobiologiColumn $mikrobiologiColumn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MikrobiologiColumn $mikrobiologiColumn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MikrobiologiColumn $mikrobiologiColumn)
    {
        $validated = $request->validate([
            'nama_kolom' => 'required|string',
            'tipe_kolom' => 'required|string',
            'urutan' => 'nullable|integer',
        ]);
        $mikrobiologiColumn->update($validated);
        return redirect()->route('mikrobiologi-forms.show', $mikrobiologiColumn->form_id)->with('success', 'Kolom berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MikrobiologiColumn $mikrobiologiColumn)
    {
        $formId = $mikrobiologiColumn->form_id;
        $mikrobiologiColumn->delete();
        return redirect()->route('mikrobiologi-forms.show', $formId)->with('success', 'Kolom berhasil dihapus!');
    }
}
