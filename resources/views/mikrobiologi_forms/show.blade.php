@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-green-900">Detail Form Mikrobiologi</h2>
        <div class="flex gap-2">
            <a href="{{ route('mikrobiologi-forms.edit', ['mikrobiologi_form' => $form->id]) }}" class="bg-yellow-400 text-green-900 px-4 py-2 rounded hover:bg-yellow-500">Edit</a>
            <a href="{{ route('mikrobiologi-forms.index') }}" class="bg-gray-300 text-green-900 px-4 py-2 rounded hover:bg-gray-400">Kembali</a>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><strong>Judul:</strong> {{ $form->title }}</div>
            <div><strong>No Form:</strong> {{ $form->no }}</div>
            <div><strong>Tanggal Inokulasi:</strong> {{ $form->tgl_inokulasi }}</div>
            <div><strong>Tanggal Pengamatan:</strong> {{ $form->tgl_pengamatan }}</div>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg p-4 mb-6 mt-8">
        <h3 class="text-lg font-bold text-success mb-4">Approval / Signature</h3>
        <div class="alert alert-warning mb-4 text-center fw-semibold" style="font-size:1.08em;">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <span class="text-danger">Hati-hati saat mengisi <b>Accept</b>, sama dengan tanda tangan dan <u>tidak bisa diulang!!.</u></span>
            <span>  Jabatan samakan dengan yang di judul nya, agar lebih teliti!!</span>
        </div>
        <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
            @foreach(['technician' => 'QA Lab. Technician', 'staff' => 'QA Staff', 'supervisor' => 'QA Supervisor'] as $role => $jabatan)
                @php $sig = $signatures[$role] ?? null; @endphp
                <div class="col d-flex align-items-stretch">
                    <div class="card border-0 shadow-sm w-100 h-100">
                        <div class="card-body d-flex flex-column align-items-center justify-content-between p-4">
                            <div class="mb-2 w-100 text-center">
                                <div class="d-flex justify-content-center align-items-center mb-2" style="height:60px;">
                                    @if($role=='technician')
                                        <i class="bi bi-journal-bookmark fs-1 text-primary"></i>
                                    @elseif($role=='staff')
                                        <i class="bi bi-globe2 fs-1 text-primary"></i>
                                    @else
                                        <i class="bi bi-capsule fs-1 text-primary"></i>
                                    @endif
                                </div>
                                <div class="fw-bold text-success mb-2" style="font-size:1.1em;">{{ $jabatan }}</div>
                            </div>
                            @if($sig)
                                <div class="mb-3 text-center">
                                    <div class="fw-semibold mb-1">Nama: <span class="text-dark">{{ $sig->name }}</span></div>
                                    <div class="mb-1">Status: <span class="fw-semibold {{ $sig->status == 'accept' ? 'text-success' : 'text-danger' }}">{{ ucfirst($sig->status) }}</span></div>
                                    <div class="mb-1">Tanggal: <span class="text-dark">{{ $sig->tanggal }}</span></div>
                                </div>
                                <button class="btn btn-outline-primary w-100" disabled>Sudah Ditandatangani</button>
                            @else
                                <form action="{{ route('mikrobiologi-forms.signatures.store', ['mikrobiologi_form' => $form->id]) }}" method="POST" class="w-100">
                                    @csrf
                                    <input type="hidden" name="form_id" value="{{ $form->id }}">
                                    <input type="hidden" name="role" value="{{ $role }}">
                                    <div class="form-floating mb-2">
                                        <input type="text" name="name" class="form-control" id="name-{{ $role }}" placeholder="Nama" required>
                                        <label for="name-{{ $role }}">Nama</label>
                                    </div>
                                    <div class="form-floating mb-2">
                                        <select name="jabatan" class="form-select" id="jabatan-{{ $role }}" required>
                                            <option value="QA Lab. Technician" {{ $jabatan == 'QA Lab. Technician' ? 'selected' : '' }}>QA Lab. Technician</option>
                                            <option value="QA Staff" {{ $jabatan == 'QA Staff' ? 'selected' : '' }}>QA Staff</option>
                                            <option value="QA Supervisor" {{ $jabatan == 'QA Supervisor' ? 'selected' : '' }}>QA Supervisor</option>
                                        </select>
                                        <label for="jabatan-{{ $role }}">Jabatan</label>
                                    </div>
                                    <div class="form-floating mb-2">
                                        <select name="status" class="form-select" id="status-{{ $role }}" required onchange="if(this.value==='accept'){document.getElementById('alert-{{ $role }}').style.display='block';}else{document.getElementById('alert-{{ $role }}').style.display='none';}">
                                            <option value="accept">Accept</option>
                                            <option value="reject">Reject</option>
                                        </select>
                                        <label for="status-{{ $role }}">Status</label>
                                    </div>
                                    <div id="alert-{{ $role }}" class="alert alert-warning py-2 px-3 mb-2" style="display:none; font-size:0.95em;">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Pastikan data sudah benar sebelum <b>Accept</b> atau tanda tangan!
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="date" name="tanggal" class="form-control" id="tanggal-{{ $role }}" value="{{ date('Y-m-d') }}" required>
                                        <label for="tanggal-{{ $role }}">Tanggal</label>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100 fw-bold">Simpan</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection 