@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-6">
    <h2 class="text-2xl font-bold text-green-900 mb-4">Edit Form Mikrobiologi</h2>
    <form action="{{ route('mikrobiologi-forms.update', $form) }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-green-900 font-semibold">Judul</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required value="{{ old('title', $form->title) }}">
            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">No Form</label>
            <input type="text" name="no" class="w-full border rounded px-3 py-2" required value="{{ old('no', $form->no) }}">
            @error('no')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">Tanggal Inokulasi</label>
            <input type="date" name="tgl_inokulasi" class="w-full border rounded px-3 py-2" required value="{{ old('tgl_inokulasi', $form->tgl_inokulasi) }}">
            @error('tgl_inokulasi')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">Tanggal Pengamatan</label>
            <input type="date" name="tgl_pengamatan" class="w-full border rounded px-3 py-2" required value="{{ old('tgl_pengamatan', $form->tgl_pengamatan) }}">
            @error('tgl_pengamatan')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('mikrobiologi-forms.index') }}" class="bg-gray-300 text-green-900 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-yellow-400 text-green-900 px-4 py-2 rounded hover:bg-yellow-500">Update</button>
        </div>
    </form>
</div>
@endsection 