@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-6">
    <h2 class="text-2xl font-bold text-green-900 mb-4">Buat Form Mikrobiologi</h2>
    @if(isset($template) && $template)
        <div class="alert alert-info mb-4">Membuat form dari template: <b>{{ $template->title }}</b></div>
    @endif
    <form action="{{ route('mikrobiologi-forms.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-green-900 font-semibold">Judul</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required value="{{ old('title', isset($template) && $template ? $template->title : '') }}">
            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">No Form</label>
            <input type="text" name="no" class="w-full border rounded px-3 py-2" required value="{{ old('no') }}">
            @error('no')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">Tanggal Inokulasi</label>
            <input type="date" name="tgl_inokulasi" class="w-full border rounded px-3 py-2" required value="{{ old('tgl_inokulasi') }}">
            @error('tgl_inokulasi')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">Tanggal Pengamatan</label>
            <input type="date" name="tgl_pengamatan" class="w-full border rounded px-3 py-2" required value="{{ old('tgl_pengamatan') }}">
            @error('tgl_pengamatan')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        @if(isset($columns) && $columns->count())
        <div class="mb-4">
            <label class="block text-green-900 font-semibold mb-2">Kolom Data Entry (dari template)</label>
            <ul class="list-disc pl-6 mb-2">
                @foreach($columns as $col)
                    <li>{{ $col->nama_kolom }} ({{ $col->tipe_kolom }})</li>
                    <input type="hidden" name="columns[nama_kolom][]" value="{{ $col->nama_kolom }}">
                    <input type="hidden" name="columns[tipe_kolom][]" value="{{ $col->tipe_kolom }}">
                    <input type="hidden" name="columns[urutan][]" value="{{ $col->urutan }}">
                @endforeach
            </ul>
            <div class="alert alert-warning">Kolom di atas otomatis diambil dari template, akan langsung tersedia setelah form disimpan.</div>
        </div>
        @endif
        <div class="flex justify-end gap-2">
            <a href="{{ route('mikrobiologi-forms.index') }}" class="bg-gray-300 text-green-900 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
        </div>
    </form>
</div>
@endsection 