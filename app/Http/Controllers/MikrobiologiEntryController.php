<?php

namespace App\Http\Controllers;

use App\Models\MikrobiologiEntry;
use Illuminate\Http\Request;

class MikrobiologiEntryController extends Controller
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
            'data' => 'required|array',
        ]);
        MikrobiologiEntry::create($validated);
        return redirect()->route('mikrobiologi-forms.show', $request->form_id)->with('success', 'Data entry berhasil ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MikrobiologiEntry $mikrobiologiEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MikrobiologiEntry $mikrobiologiEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MikrobiologiEntry $mikrobiologiEntry)
    {
        $validated = $request->validate([
            'data' => 'required|array',
        ]);
        $mikrobiologiEntry->update($validated);
        return redirect()->route('mikrobiologi-forms.show', $mikrobiologiEntry->form_id)->with('success', 'Data entry berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MikrobiologiEntry $mikrobiologiEntry)
    {
        $formId = $mikrobiologiEntry->form_id;
        $mikrobiologiEntry->delete();
        return redirect()->route('mikrobiologi-forms.show', $formId)->with('success', 'Data entry berhasil dihapus!');
    }
}
