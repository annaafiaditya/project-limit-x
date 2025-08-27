@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success mb-4 mx-auto" style="max-width: 900px;">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger mb-4 mx-auto" style="max-width: 900px;">{{ $errors->first() }}</div>
@endif
<style>
@keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: none; } }
.fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
.fade-slide-up-delay-1 { animation-delay: .15s; }
.fade-slide-up-delay-2 { animation-delay: .3s; }
</style>
<div class="max-w-5xl mx-auto py-6 fade-slide-up">
    <div class="bg-white shadow rounded-lg p-6 mb-6 fade-slide-up fade-slide-up-delay-1">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-2xl font-bold text-blue-900 mb-2">Detail Form Kimia</h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('kimia.edit', ['kimia_form' => $form->id]) }}" class="btn btn-warning btn-sm text-white fw-bold px-3 py-1">Edit</a>
                <a href="{{ route('kimia.export', ['kimia_form' => $form->id]) }}" class="btn btn-success btn-sm fw-bold px-3 py-1">Export Excel</a>
                <a href="{{ route('kimia.export-pdf', ['kimia_form' => $form->id]) }}" class="btn btn-danger btn-sm fw-bold px-3 py-1" target="_blank">Export PDF</a>
                <a href="{{ route('kimia.index') }}" class="btn btn-secondary btn-sm fw-bold px-3 py-1">Kembali</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><strong>Judul:</strong> {{ $form->title }}</div>
            <div><strong>No Form:</strong> {{ $form->no }}</div>
            <div><strong>Tanggal:</strong> {{ $form->tanggal }}</div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <h3 class="text-lg font-bold mb-1" style="color:#222;">Tabel pada Form Ini</h3>
        <div class="alert alert-info py-2 mb-3" style="font-size:0.95rem;">
            Petunjuk: Anda bisa membuat lebih dari satu tabel. 1) Klik "Nama Tabel Baru" lalu "Tambah Tabel". 2) Di setiap tabel, tambahkan kolom sesuai kebutuhan. 3) Isi data pada bagian "Input Data Entry" di bawah tabel.
        </div>
        <ul class="mb-3">
            @foreach($tables as $t)
                <li class="mb-1">- {{ $t->name }} ({{ $t->columns->count() }} kolom, {{ $t->entries->count() }} baris)</li>
            @endforeach
        </ul>
        <form action="{{ route('kimia.tables.add', $form) }}" method="POST" class="d-flex gap-2 align-items-end">
            @csrf
            <div>
                <label class="form-label">Nama Tabel Baru</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Tabel 2" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary mt-4">Tambah Tabel</button>
                    </div>
        </form>
                </div>

    @php $__colors = ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ef4444','#0ea5e9','#14b8a6']; @endphp
    @foreach($tables as $table)
    @php $accent = $__colors[$loop->index % count($__colors)]; @endphp
    <div class="mb-8 fade-slide-up fade-slide-up-delay-2" id="table-{{ $table->id }}">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 mb-2" style="border-radius:0.8rem; background: {{ $accent }}15; border: 1px solid {{ $accent }};">
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background: {{ $accent }}; color:#fff;">Tabel {{ $loop->iteration }}</span>
                <strong>{{ $table->name }}</strong>
                <small class="text-muted">({{ $table->columns->count() }} kolom • {{ $table->entries->count() }} baris)</small>
            </div>
            <button type="button" class="btn btn-sm" onclick="toggleKimiaTable({{ $table->id }})" style="border:1px solid {{ $accent }}; color: {{ $accent }}; border-radius:0.6rem;">Tutup/Buka</button>
        </div>
        <div id="table-content-{{ $table->id }}">
        <style>
            .dynamic-card { background: #f7f7fa; border-radius: 1.2rem; box-shadow: 0 4px 24px #0002; padding: 2rem 1.5rem; margin-bottom: 2.5rem; }
            .dynamic-table th, .dynamic-table td { background: transparent !important; color: #222; vertical-align: middle; padding: 0.7rem 1rem; }
            .dynamic-table th { font-weight: 700; font-size: 1.08rem; border-bottom: 2px solid #e0e0e0; background: #f1f1f7 !important; }
            .dynamic-table td { border-bottom: 1px solid #e0e0e0; }
            .dynamic-table tbody tr:hover { background: #dbeafe !important; }
            .dynamic-btn { background: #93c5fd; color: #222; border: none; border-radius: 1.2rem; font-weight: 600; font-size: 1rem; letter-spacing: 1px; box-shadow: 0 2px 8px #0002; padding: 0.5rem 1.2rem; margin: 0 0.2rem; transition: all .2s; }
            .dynamic-btn:hover, .dynamic-btn:focus { background: #60a5fa; color: #222; }
            .dynamic-input, .dynamic-select { background: #fff; color: #222; border: 1px solid #bbb; border-radius: 0.7rem; padding: 0.4rem 0.8rem; margin-bottom: 0.2rem; }
            .action-btn { border-radius: 0.7rem; padding: 0.3rem 0.7rem; font-size: 0.98em; margin-right: 0.2rem; display: inline-flex; align-items: center; gap: 0.3em; }
            .action-btn-edit { background: #facc15; color: #222; } .action-btn-edit:hover { background: #eab308; color: #222; }
            .action-btn-delete { background: #ef4444; color: #fff; } .action-btn-delete:hover { background: #b91c1c; color: #fff; }
        </style>
        <div class="dynamic-card" style="border-left:6px solid {{ $accent }};">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="fw-bold mb-0" style="color:#222;">{{ $table->name }}</h3>
                <span class="badge text-bg-light" style="border:1px solid #e5e7eb;">{{ $table->columns->count() }} kolom • {{ $table->entries->count() }} baris</span>
            </div>
            <div class="mb-3">
                <form id="form-tambah-kolom-{{ $table->id }}" action="{{ route('kimia-columns.store') }}" method="POST" class="d-flex gap-2 align-items-end mb-3">
                    @csrf
                    <input type="hidden" name="form_id" value="{{ $form->id }}">
                    <input type="hidden" name="table_id" value="{{ $table->id }}">
                    <input type="text" name="nama_kolom" class="dynamic-input" required placeholder="Nama Kolom (contoh: Parameter)">
                    <select name="tipe_kolom" class="dynamic-select" required>
                        <option value="string">Teks</option>
                        <option value="integer">Angka</option>
                        <option value="decimal">Desimal</option>
                        <option value="date">Tanggal</option>
                        <option value="time">Jam</option>
                        </select>
                    <button type="submit" class="dynamic-btn">Tambah Kolom</button>
                </form>
                <div class="text-muted mb-2" style="font-size:0.92rem;">Tips: Untuk angka desimal, gunakan tipe "Desimal". Untuk tanggal dan jam, gunakan tipe yang sesuai agar format otomatis.</div>
                <table class="table dynamic-table mb-0">
                    <thead>
                        <tr>
                            <th>Nama Kolom</th>
                            <th>Tipe</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($table->columns as $col)
                        <tr>
                            <td>{{ $col->nama_kolom }}</td>
                            <td>{{ ucfirst($col->tipe_kolom) }}</td>
                            <td>
                                <form action="{{ route('kimia-columns.destroy', $col) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kolom ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dynamic-card mb-6" style="border-left:6px solid {{ $accent }};">
            <h4 class="fw-bold mb-3" style="color:#222;">Input Data Entry</h4>
            <form action="{{ route('kimia-entries.store') }}" method="POST" class="d-flex flex-wrap gap-2 align-items-end mb-3">
                @csrf
                <input type="hidden" name="form_id" value="{{ $form->id }}">
                <input type="hidden" name="table_id" value="{{ $table->id }}">
                @foreach($table->columns as $col)
                    @php $name = 'data['.$col->id.']'; @endphp
                    <div class="mb-2 me-2">
                        <label class="block text-blue-900 font-semibold mb-1" style="font-size:0.97em;">{{ $col->nama_kolom }}</label>
                        @if($col->tipe_kolom === 'string')
                            <input type="text" name="{{ $name }}" class="dynamic-input w-44" required placeholder="{{ $col->nama_kolom }}">
                        @elseif($col->tipe_kolom === 'integer')
                            <input type="number" name="{{ $name }}" class="dynamic-input w-32" required placeholder="{{ $col->nama_kolom }}">
                        @elseif($col->tipe_kolom === 'decimal')
                            <input type="number" step="0.01" name="{{ $name }}" class="dynamic-input w-32" required placeholder="{{ $col->nama_kolom }}">
                        @elseif($col->tipe_kolom === 'date')
                            <input type="date" name="{{ $name }}" class="dynamic-input w-36" required>
                        @elseif($col->tipe_kolom === 'time')
                            <input type="time" name="{{ $name }}" class="dynamic-input w-32" required>
                    @else
                            <input type="text" name="{{ $name }}" class="dynamic-input w-44" required placeholder="{{ $col->nama_kolom }}">
                    @endif
                </div>
                @endforeach
                <button type="submit" class="dynamic-btn mt-4">Simpan Entry</button>
            </form>
        </div>

        @if($table->entries->count())
        <div class="dynamic-card mb-6" style="border-left:6px solid {{ $accent }};">
            <h4 class="fw-bold mb-3" style="color:#222;">Daftar Entry</h4>
            <table class="table dynamic-table mb-0">
                <thead>
                    <tr>
                        @foreach($table->columns as $col)
                            <th>{{ $col->nama_kolom }}</th>
                        @endforeach
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($table->entries as $entry)
                    <tr>
                        @foreach($table->columns as $col)
                            <td>
                                @if(isset($entry->data[$col->id]))
                                    @if($col->tipe_kolom === 'date')
                                        {{ \Carbon\Carbon::parse($entry->data[$col->id])->format('d/m/Y') }}
                                    @elseif($col->tipe_kolom === 'time')
                                        {{ $entry->data[$col->id] }}
                                    @elseif($col->tipe_kolom === 'decimal')
                                        {{ number_format($entry->data[$col->id], 2) }}
                                    @else
                                        {{ $entry->data[$col->id] }}
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <form action="{{ route('kimia-entries.destroy', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        </div>
    </div>
    @endforeach

    <div class="bg-white shadow rounded-lg p-4 mb-6 mt-8">
        <h3 class="text-lg font-bold text-primary mb-4">Approval / Signature</h3>
        <div class="alert alert-warning mb-4 text-center fw-semibold" style="font-size:1.08em;">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <span class="text-danger">Hati-hati saat mengisi <b>Accept</b>, sama dengan tanda tangan dan <u>tidak bisa diulang!!.</u></span>
            <span>  Jabatan samakan dengan yang di judul nya, agar lebih teliti!!</span>
        </div>
        <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
            @php $signatures = $signatures ?? collect(); @endphp
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
                                <div class="fw-bold text-primary mb-2" style="font-size:1.1em;">{{ $jabatan }}</div>
                            </div>
                            @if($sig)
                                <div class="mb-3 text-center">
                                    <div class="fw-semibold mb-1">Nama: <span class="text-dark">{{ $sig->name }}</span></div>
                                    <div class="mb-1">Status: <span class="fw-semibold {{ $sig->status == 'accept' ? 'text-success' : 'text-danger' }}">{{ ucfirst($sig->status) }}</span></div>
                                    <div class="mb-1">Tanggal: <span class="text-dark">{{ $sig->tanggal }}</span></div>
</div>
                                <button class="btn btn-outline-primary w-100" disabled>Sudah Ditandatangani</button>
                            @else
                                <form action="{{ route('kimia.signatures.store', ['kimia_form' => $form->id]) }}" method="POST" class="w-100">
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
                                        <select name="status" class="form-select" id="status-{{ $role }}" required>
                                            <option value="accept">Accept</option>
                                            <option value="reject">Reject</option>
                        </select>
                                        <label for="status-{{ $role }}">Status</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="date" name="tanggal" class="form-control" id="tanggal-{{ $role }}" value="{{ date('Y-m-d') }}" required>
                                        <label for="tanggal-{{ $role }}">Tanggal</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan</button>
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

@push('scripts')
<script>
    function toggleKimiaTable(id){
        const el = document.getElementById('table-content-' + id);
        if(!el) return;
        el.style.display = (el.style.display === 'none') ? '' : 'none';
}
</script>
@endpush
