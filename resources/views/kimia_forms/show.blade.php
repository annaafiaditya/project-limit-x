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
            .action-btn { border-radius: 0.7rem; padding: 0.4rem 0.8rem; font-size: 0.9em; margin-right: 0.3rem; display: inline-flex; align-items: center; gap: 0.3em; font-weight: 500; transition: all 0.2s ease; border: none; cursor: pointer; }
            .action-btn-edit { background: #3b82f6; color: #fff; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3); } 
            .action-btn-edit:hover { background: #2563eb; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4); }
            .action-btn-save { background: #10b981; color: #fff; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3); }
            .action-btn-save:hover { background: #059669; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4); }
            .action-btn-cancel { background: #6b7280; color: #fff; box-shadow: 0 2px 4px rgba(107, 114, 128, 0.3); }
            .action-btn-cancel:hover { background: #4b5563; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(107, 114, 128, 0.4); }
            .action-btn-delete { background: #ef4444; color: #fff; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3); } 
            .action-btn-delete:hover { background: #dc2626; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4); }
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
                        <tr id="kimia-kolom-row-{{ $col->id }}">
                            <td class="kimia-kolom-nama">{{ $col->nama_kolom }}</td>
                            <td class="kimia-kolom-tipe">{{ ucfirst($col->tipe_kolom) }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="action-btn action-btn-edit kimia-edit-col" data-id="{{ $col->id }}">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </button>
                                    <form action="{{ route('kimia-columns.destroy', $col) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kolom ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-delete">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                </div>
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
                    <tr id="kimia-entry-row-{{ $entry->id }}">
                        @foreach($table->columns as $col)
                            <td class="kimia-entry-col" data-col-id="{{ $col->id }}">
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
                            <div class="d-flex gap-1">
                                <button type='button' class='kimia-entry-edit-btn action-btn action-btn-edit' data-id='{{ $entry->id }}'>
                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                </button>
                                <form action="{{ route('kimia-entries.destroy', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-delete">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
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

    function showNotif(msg, type) {
        let notif = document.getElementById('ajax-notif');
        if (!notif) {
            notif = document.createElement('div');
            notif.id = 'ajax-notif';
            notif.className = 'alert alert-' + type;
            notif.style.position = 'fixed';
            notif.style.top = '20px';
            notif.style.right = '20px';
            notif.style.zIndex = 9999;
            document.body.appendChild(notif);
        }
        notif.className = 'alert alert-' + type;
        notif.innerText = msg;
        notif.style.display = 'block';
        setTimeout(() => notif.style.display = 'none', 2000);
    }

    // Kimia Column Edit Functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Kimia Columns
        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.kimia-edit-col');
            if (editBtn) {
                const id = editBtn.dataset.id;
                const tr = document.getElementById('kimia-kolom-row-' + id);
                const nama = tr.querySelector('.kimia-kolom-nama').innerText;
                const tipe = tr.querySelector('.kimia-kolom-tipe').innerText.toLowerCase();
                tr.innerHTML = `<td><input type='text' class='dynamic-input' value='${nama}' id='edit-kimia-nama-${id}'></td>` +
                    `<td><select class='dynamic-select' id='edit-kimia-tipe-${id}'>` +
                    `<option value='string' ${tipe==='string'?'selected':''}>Teks</option>` +
                    `<option value='integer' ${tipe==='integer'?'selected':''}>Angka</option>` +
                    `<option value='decimal' ${tipe==='decimal'?'selected':''}>Desimal</option>` +
                    `<option value='date' ${tipe==='date'?'selected':''}>Tanggal</option>` +
                    `<option value='time' ${tipe==='time'?'selected':''}>Jam</option>` +
                    `</select></td>` +
                    `<td><div class="d-flex gap-1"><button type='button' class='action-btn action-btn-save kimia-save-col' data-id='${id}'><i class="bi bi-check-lg me-1"></i>Simpan</button>` +
                    `<button type='button' class='action-btn action-btn-cancel kimia-cancel-col' data-id='${id}'><i class="bi bi-x-lg me-1"></i>Batal</button></div></td>`;
            }

            // Save Kimia Column
            const saveBtn = e.target.closest('.kimia-save-col');
            if (saveBtn) {
                const id = saveBtn.dataset.id;
                const tr = document.getElementById('kimia-kolom-row-' + id);
                const nama = tr.querySelector(`#edit-kimia-nama-${id}`).value;
                const tipe = tr.querySelector(`#edit-kimia-tipe-${id}`).value;
                fetch(`/kimia-columns/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ nama_kolom: nama, tipe_kolom: tipe }),
                    credentials: 'same-origin',
                })
                .then(res => res.json().then(json => ({ok: res.ok, json})))
                .then(({ok, json}) => {
                    if (ok) {
                        tr.innerHTML = `<td class='kimia-kolom-nama'>${json.nama_kolom}</td><td class='kimia-kolom-tipe'>${json.tipe_kolom.charAt(0).toUpperCase()+json.tipe_kolom.slice(1)}</td><td><div class="d-flex gap-1"><button type='button' class='action-btn action-btn-edit kimia-edit-col' data-id='${id}'><i class="bi bi-pencil-square me-1"></i>Edit</button><form action="/kimia-columns/${id}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kolom ini?')"><input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="action-btn action-btn-delete"><i class="bi bi-trash me-1"></i>Hapus</button></form></div></td>`;
                        showNotif('Kolom berhasil diupdate', 'success');
                    } else {
                        showNotif(json.message || 'Gagal update kolom', 'danger');
                    }
                })
                .catch(err => showNotif(err.message, 'danger'));
            }

            // Cancel Kimia Column
            const cancelBtn = e.target.closest('.kimia-cancel-col');
            if (cancelBtn) {
                location.reload();
            }

            // Edit Kimia Entry
            const editEntryBtn = e.target.closest('.kimia-entry-edit-btn');
            if (editEntryBtn) {
                const id = editEntryBtn.dataset.id;
                const row = document.getElementById('kimia-entry-row-' + id);
                if (!row) return;
                // Simpan data lama
                const oldData = [];
                row.querySelectorAll('.kimia-entry-col').forEach(td => oldData.push(td.innerText));
                // Ganti ke input
                @foreach($tables as $table)
                    @foreach($table->columns as $col)
                row.querySelector('.kimia-entry-col[data-col-id="{{ $col->id }}"]').innerHTML = `<input type='{{ $col->tipe_kolom === 'integer' ? 'number' : ($col->tipe_kolom === 'decimal' ? 'number' : ($col->tipe_kolom === 'date' ? 'date' : ($col->tipe_kolom === 'time' ? 'time' : 'text'))) }}' class='dynamic-input kimia-entry-edit-input' value='${row.querySelector('.kimia-entry-col[data-col-id="{{ $col->id }}"]').innerText}' data-col-id='{{ $col->id }}' {{ $col->tipe_kolom === 'decimal' ? 'step="0.01"' : '' }}>`;
                    @endforeach
                @endforeach
                // Ganti tombol aksi
                row.querySelector('td:last-child').innerHTML = `<div class="d-flex gap-1"><button type='button' class='kimia-entry-save-btn action-btn action-btn-save' data-id='${id}'><i class="bi bi-check-lg me-1"></i>Simpan</button><button type='button' class='kimia-entry-cancel-btn action-btn action-btn-cancel' data-id='${id}'><i class="bi bi-x-lg me-1"></i>Batal</button></div>`;
            }

            // Save Kimia Entry
            const saveEntryBtn = e.target.closest('.kimia-entry-save-btn');
            if (saveEntryBtn) {
                const id = saveEntryBtn.dataset.id;
                const row = document.getElementById('kimia-entry-row-' + id);
                const data = {};
                row.querySelectorAll('.kimia-entry-edit-input').forEach(input => {
                    data[input.dataset.colId] = input.value;
                });
                fetch(`/kimia-entries/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ data })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Gagal update entry');
                    return res.json();
                })
                .then(json => {
                    if (json.success || json.updated) {
                        // Update tampilan baris
                        @foreach($tables as $table)
                            @foreach($table->columns as $col)
                        row.querySelector('.kimia-entry-col[data-col-id="{{ $col->id }}"]').innerText = data['{{ $col->id }}'];
                            @endforeach
                        @endforeach
                        row.querySelector('td:last-child').innerHTML = `<div class="d-flex gap-1"><button type='button' class='kimia-entry-edit-btn action-btn action-btn-edit' data-id='${id}'><i class="bi bi-pencil-square me-1"></i>Edit</button><form action="/kimia-entries/${id}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')"><input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="action-btn action-btn-delete"><i class="bi bi-trash me-1"></i>Hapus</button></form></div>`;
                        showNotif('Entry berhasil diupdate', 'success');
                    } else {
                        showNotif(json.message || 'Gagal update entry', 'danger');
                    }
                })
                .catch(err => {
                    showNotif('Terjadi error saat update entry: ' + err.message, 'danger');
                    console.error(err);
                });
            }

            // Cancel Kimia Entry
            const cancelEntryBtn = e.target.closest('.kimia-entry-cancel-btn');
            if (cancelEntryBtn) {
                location.reload();
            }
        });
    });
</script>
@endpush
