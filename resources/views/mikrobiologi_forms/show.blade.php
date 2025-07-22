@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-green-900">Detail Form Mikrobiologi</h2>
        <div class="flex gap-2">
            <a href="{{ route('mikrobiologi-forms.edit', ['mikrobiologi_form' => $form->id]) }}" class="bg-yellow-400 text-green-900 px-4 py-2 rounded hover:bg-yellow-500">Edit</a>
            <a href="{{ route('mikrobiologi-forms.index') }}" class="bg-gray-300 text-green-900 px-4 py-2 rounded hover:bg-gray-400">Kembali</a>
            <!-- FORM HAPUS FORM, hanya untuk hapus form utama -->
            <form action="{{ route('mikrobiologi-forms.destroy', ['mikrobiologi_form' => $form->id]) }}" method="POST" onsubmit="return confirm('Yakin hapus form ini?')" style="display:inline-block; margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 btn-delete-form">Hapus Form</button>
            </form>
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
    <!-- TABEL DINAMIS START (pastikan di luar form hapus form) -->
    <div class="mb-8">
        <style>
            .dynamic-card {
                background: #f7f7fa;
                border-radius: 1.2rem;
                box-shadow: 0 4px 24px #0002;
                padding: 2rem 1.5rem;
                margin-bottom: 2.5rem;
            }
            .dynamic-table th, .dynamic-table td {
                background: transparent !important;
                color: #222;
                vertical-align: middle;
                padding: 0.7rem 1rem;
            }
            .dynamic-table th {
                font-weight: 700;
                font-size: 1.08rem;
                border-bottom: 2px solid #e0e0e0;
                background: #f1f1f7 !important;
            }
            .dynamic-table td {
                border-bottom: 1px solid #e0e0e0;
            }
            .dynamic-table tbody tr:hover {
                background: #d1fae5 !important; /* emerald-100 */
            }
            .dynamic-btn, .dynamic-btn-green {
                background: #6ee7b7; /* emerald-300 */
                color: #222;
                border: none;
                border-radius: 1.2rem;
                font-weight: 600;
                font-size: 1rem;
                letter-spacing: 1px;
                box-shadow: 0 2px 8px #0002;
                padding: 0.5rem 1.2rem;
                margin: 0 0.2rem;
                transition: all .2s;
            }
            .dynamic-btn:hover, .dynamic-btn:focus, .dynamic-btn-green:hover, .dynamic-btn-green:focus {
                background: #34d399; /* emerald-400 */
                color: #222;
            }
            .dynamic-input, .dynamic-select {
                background: #fff;
                color: #222;
                border: 1px solid #bbb;
                border-radius: 0.7rem;
                padding: 0.4rem 0.8rem;
                margin-bottom: 0.2rem;
            }
            .action-btn {
                border-radius: 0.7rem;
                padding: 0.3rem 0.7rem;
                font-size: 0.98em;
                margin-right: 0.2rem;
                display: inline-flex;
                align-items: center;
                gap: 0.3em;
            }
            .action-btn-edit { background: #facc15; color: #222; }
            .action-btn-edit:hover { background: #eab308; color: #222; }
            .action-btn-save { background: #6ee7b7; color: #222; }
            .action-btn-save:hover { background: #34d399; color: #222; }
            .action-btn-cancel { background: #e5e7eb; color: #222; }
            .action-btn-cancel:hover { background: #d1d5db; color: #222; }
            .action-btn-delete { background: #ef4444; color: #fff; }
            .action-btn-delete:hover { background: #b91c1c; color: #fff; }
        </style>
        <div class="dynamic-card">
            <h3 class="fw-bold mb-3" style="color:#222;">Tabel Dinamis</h3>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mb-2">{{ $errors->first() }}</div>
            @endif
            <!-- Builder Kolom (MINIMAL, TAMBAH & HAPUS) -->
            <div class="mb-3">
                <!-- FORM TAMBAH KOLOM, hanya untuk tambah kolom -->
                <form id="form-tambah-kolom" action="{{ route('columns.store') }}" method="POST" class="d-flex gap-2 align-items-end mb-3">
                    @csrf
                    <input type="hidden" name="form_id" value="{{ $form->id }}">
                    <input type="text" name="nama_kolom" class="dynamic-input" required placeholder="Nama Kolom">
                    <select name="tipe_kolom" class="dynamic-select" required>
                        <option value="string">Teks</option>
                        <option value="integer">Angka</option>
                        <option value="date">Tanggal</option>
                        <option value="time">Jam</option>
                    </select>
                    <button type="submit" class="dynamic-btn-green">Tambah Kolom</button>
                </form>
                <!-- TABEL KOLOM DINAMIS DI LUAR FORM TAMBAH KOLOM -->
                <table class="table dynamic-table mb-0" id="kolom-table">
                    <thead>
                        <tr>
                            <th>Nama Kolom</th>
                            <th>Tipe</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kolom-tbody">
                        @foreach($columns as $col)
                        <tr id="kolom-row-{{ $col->id }}">
                            <td class="kolom-nama">{{ $col->nama_kolom }}</td>
                            <td class="kolom-tipe">{{ ucfirst($col->tipe_kolom) }}</td>
                            <td>
                                <button type="button" class="action-btn action-btn-edit" data-id="{{ $col->id }}">Edit</button>
                                <button type="button" class="action-btn action-btn-delete" data-id="{{ $col->id }}" onclick="return false;">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- TABEL DINAMIS END -->
    <!-- FORM INPUT DATA ENTRY (CARD TERPISAH) -->
    <div class="dynamic-card mb-6">
        <h4 class="fw-bold mb-3" style="color:#222;">Input Data Entry</h4>
        <form id="form-entry" action="{{ route('mikrobiologi-forms.entries.store', ['form' => $form->id]) }}" method="POST" class="d-flex flex-wrap gap-2 align-items-end mb-3">
            @csrf
            <input type="hidden" name="form_id" value="{{ $form->id }}">
            @foreach($columns as $col)
                @php $name = 'data['.$col->id.']'; @endphp
                <div class="mb-2 me-2">
                    <label class="block text-green-900 font-semibold mb-1" style="font-size:0.97em;">{{ $col->nama_kolom }}</label>
                    @if($col->tipe_kolom === 'string')
                        <input type="text" name="{{ $name }}" class="dynamic-input w-44" required placeholder="{{ $col->nama_kolom }}">
                    @elseif($col->tipe_kolom === 'integer')
                        <input type="number" name="{{ $name }}" class="dynamic-input w-32" required placeholder="{{ $col->nama_kolom }}">
                    @elseif($col->tipe_kolom === 'date')
                        <input type="date" name="{{ $name }}" class="dynamic-input w-36" required>
                    @elseif($col->tipe_kolom === 'time')
                        <input type="time" name="{{ $name }}" class="dynamic-input w-32" required>
                    @else
                        <input type="text" name="{{ $name }}" class="dynamic-input w-44" required placeholder="{{ $col->nama_kolom }}">
                    @endif
                </div>
            @endforeach
            <button type="submit" class="dynamic-btn-green mt-4">Simpan Entry</button>
        </form>
    </div>
    <!-- DAFTAR ENTRY (CARD TERPISAH) -->
    @if(count($entries))
    <div class="dynamic-card mb-6">
        <h4 class="fw-bold mb-3" style="color:#222;">Daftar Entry</h4>
        <table class="table dynamic-table mb-0">
            <thead>
                <tr>
                    @foreach($columns as $col)
                        <th>{{ $col->nama_kolom }}</th>
                    @endforeach
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="entry-tbody">
                @foreach($entries as $entry)
                <tr id="entry-row-{{ $entry->id }}">
                    @foreach($columns as $col)
                        <td class="entry-col" data-col-id="{{ $col->id }}">{{ $entry->data[$col->id] ?? '' }}</td>
                    @endforeach
                    <td>
                        <button type='button' class='entry-edit-btn dynamic-btn action-btn-edit' data-id='{{ $entry->id }}'>Edit</button>
                        <button type='button' class='entry-delete-btn dynamic-btn action-btn-delete' data-id='{{ $entry->id }}'>Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
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

@push('scripts')
<script>
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
const formKolom = document.getElementById('form-tambah-kolom');
if (formKolom) {
    formKolom.onsubmit = function(e) {
        e.preventDefault();
        const data = new FormData(formKolom);
        fetch(formKolom.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: data,
            credentials: 'same-origin',
        })
        .then(res => res.json().then(json => ({ok: res.ok, json})))
        .then(({ok, json}) => {
            if (ok) {
                addKolomRow(json);
                showNotif('Kolom berhasil ditambah', 'success');
                formKolom.reset();
            } else {
                showNotif(json.message || 'Gagal tambah kolom', 'danger');
            }
        })
        .catch(err => showNotif(err.message, 'danger'));
        return false;
    }
}
function addKolomRow(col) {
    const tbody = document.getElementById('kolom-tbody');
    const tr = document.createElement('tr');
    tr.id = 'kolom-row-' + col.id;
    tr.innerHTML = `<td class='kolom-nama'>${col.nama_kolom}</td><td class='kolom-tipe'>${col.tipe_kolom.charAt(0).toUpperCase()+col.tipe_kolom.slice(1)}</td><td><button type='button' class='action-btn action-btn-edit' data-id='${col.id}'>Edit</button><button type='button' class='action-btn action-btn-delete' data-id='${col.id}' onclick='return false;'>Hapus</button></td>`;
    tbody.appendChild(tr);
    // Tambahkan input baru ke form entry
    const formEntry = document.getElementById('form-entry');
    if (formEntry) {
        // Cari sebelum tombol submit
        const submitBtn = formEntry.querySelector('button[type="submit"]');
        const div = document.createElement('div');
        div.className = 'mb-2 me-2';
        div.innerHTML = `<label class="block text-green-900 font-semibold mb-1" style="font-size:0.97em;">${col.nama_kolom}</label>` +
            (col.tipe_kolom === 'string' ? `<input type="text" name="data[${col.id}]" class="dynamic-input w-44" required placeholder="${col.nama_kolom}">`
            : col.tipe_kolom === 'integer' ? `<input type="number" name="data[${col.id}]" class="dynamic-input w-32" required placeholder="${col.nama_kolom}">`
            : col.tipe_kolom === 'date' ? `<input type="date" name="data[${col.id}]" class="dynamic-input w-36" required>`
            : col.tipe_kolom === 'time' ? `<input type="time" name="data[${col.id}]" class="dynamic-input w-32" required>`
            : `<input type="text" name="data[${col.id}]" class="dynamic-input w-44" required placeholder="${col.nama_kolom}">`);
        formEntry.insertBefore(div, submitBtn);
    }
}
// Event delegation untuk hapus kolom
const kolomTbody = document.getElementById('kolom-tbody');
if (kolomTbody) {
    kolomTbody.addEventListener('click', function(e) {
        const btn = e.target.closest('button.action-btn-delete');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        const id = btn.dataset.id;
        const tr = document.getElementById('kolom-row-' + id);
        if (!confirm('Yakin hapus kolom ini?')) return false;
        fetch(`/columns/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(res => res.json().then(json => ({ok: res.ok, json})))
        .then(({ok, json}) => {
            if (ok) {
                tr.remove();
                // Hapus input di form entry (cari div input berdasarkan input name)
                const formEntry = document.getElementById('form-entry');
                if (formEntry) {
                    const input = formEntry.querySelector(`[name="data[${id}]"]`);
                    if (input) {
                        const inputDiv = input.closest('div.mb-2, div.mb-2.me-2, div');
                        if (inputDiv) inputDiv.remove();
                    }
                }
                showNotif('Kolom berhasil dihapus', 'success');
            } else {
                showNotif(json.message || 'Gagal hapus kolom', 'danger');
            }
        })
        .catch(err => showNotif(err.message, 'danger'));
        return false;
    });
}
// Inline edit kolom
document.addEventListener('DOMContentLoaded', function() {
    const kolomTbody = document.getElementById('kolom-tbody');
    if (!kolomTbody) return;
    kolomTbody.addEventListener('click', function(e) {
        // Edit
        const editBtn = e.target.closest('button.action-btn-edit');
        if (editBtn) {
            const id = editBtn.dataset.id;
            const tr = document.getElementById('kolom-row-' + id);
            const nama = tr.querySelector('.kolom-nama').innerText;
            const tipe = tr.querySelector('.kolom-tipe').innerText.toLowerCase();
            tr.innerHTML = `<td><input type='text' class='dynamic-input' value='${nama}' id='edit-nama-${id}'></td>` +
                `<td><select class='dynamic-select' id='edit-tipe-${id}'>` +
                `<option value='string' ${tipe==='string'?'selected':''}>Teks</option>` +
                `<option value='integer' ${tipe==='integer'?'selected':''}>Angka</option>` +
                `<option value='date' ${tipe==='date'?'selected':''}>Tanggal</option>` +
                `<option value='time' ${tipe==='time'?'selected':''}>Jam</option>` +
                `</select></td>` +
                `<td><button type='button' class='action-btn action-btn-save' data-id='${id}'>Simpan</button>` +
                `<button type='button' class='action-btn action-btn-cancel' data-id='${id}'>Batal</button></td>`;
        }
        // Simpan
        const saveBtn = e.target.closest('button.action-btn-save');
        if (saveBtn) {
            const id = saveBtn.dataset.id;
            const tr = document.getElementById('kolom-row-' + id);
            const nama = tr.querySelector(`#edit-nama-${id}`).value;
            const tipe = tr.querySelector(`#edit-tipe-${id}`).value;
            fetch(`/columns/${id}`, {
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
                    tr.innerHTML = `<td class='kolom-nama'>${json.nama_kolom}</td><td class='kolom-tipe'>${json.tipe_kolom.charAt(0).toUpperCase()+json.tipe_kolom.slice(1)}</td><td><button type='button' class='action-btn action-btn-edit' data-id='${id}'>Edit</button><button type='button' class='action-btn action-btn-delete' data-id='${id}' onclick='return false;'>Hapus</button></td>`;
                    // Update input di form entry
                    const formEntry = document.getElementById('form-entry');
                    if (formEntry) {
                        const input = formEntry.querySelector(`[name="data[${id}]"]`);
                        if (input) {
                            // Update label
                            const label = input.parentNode.querySelector('label');
                            if (label) label.innerText = json.nama_kolom;
                            // Update tipe input
                            if (json.tipe_kolom === 'string') input.type = 'text';
                            else if (json.tipe_kolom === 'integer') input.type = 'number';
                            else if (json.tipe_kolom === 'date') input.type = 'date';
                            else if (json.tipe_kolom === 'time') input.type = 'time';
                            else input.type = 'text';
                            input.placeholder = json.nama_kolom;
                        }
                    }
                    showNotif('Kolom berhasil diupdate', 'success');
                } else {
                    showNotif(json.message || 'Gagal update kolom', 'danger');
                }
            })
            .catch(err => showNotif(err.message, 'danger'));
        }
        // Batal
        const cancelBtn = e.target.closest('button.action-btn-cancel');
        if (cancelBtn) {
            const id = cancelBtn.dataset.id;
            const tr = document.getElementById('kolom-row-' + id);
            // Reload ulang halaman (atau bisa fetch ulang data kolom jika ingin lebih dinamis)
            location.reload();
        }
    });
});
// Handler hapus entry via AJAX (final fix, return false, log bubbling)
document.addEventListener('DOMContentLoaded', function() {
    const entryTbody = document.getElementById('entry-tbody');
    if (!entryTbody) return;
    entryTbody.addEventListener('click', function(e) {
        // Hapus Entry
        const delBtn = e.target.closest('.entry-delete-btn');
        if (delBtn) {
            const id = delBtn.dataset.id;
            e.preventDefault();
            e.stopPropagation();
            if (!confirm('Yakin hapus entry ini?')) return false;
            fetch(`/entries/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            })
            .then(res => {
                if (!res.ok) throw new Error('Gagal hapus entry');
                return res.json();
            })
            .then(json => {
                if (json.success) {
                    const row = document.getElementById('entry-row-' + id);
                    if (row) row.remove();
                    showNotif('Entry berhasil dihapus', 'success');
                } else {
                    showNotif(json.message || 'Gagal hapus entry', 'danger');
                }
            })
            .catch(err => {
                showNotif('Terjadi error saat hapus entry: ' + err.message, 'danger');
                console.error(err);
            });
            return false;
        }
        // Edit Entry
        const editBtn = e.target.closest('.entry-edit-btn');
        if (editBtn) {
            const id = editBtn.dataset.id;
            const row = document.getElementById('entry-row-' + id);
            if (!row) return;
            // Simpan data lama
            const oldData = [];
            row.querySelectorAll('.entry-col').forEach(td => oldData.push(td.innerText));
            // Ganti ke input
            @foreach($columns as $col)
            row.querySelector('.entry-col[data-col-id="{{ $col->id }}"]').innerHTML = `<input type='{{ $col->tipe_kolom === 'integer' ? 'number' : ($col->tipe_kolom === 'date' ? 'date' : ($col->tipe_kolom === 'time' ? 'time' : 'text')) }}' class='dynamic-input entry-edit-input' value='${row.querySelector('.entry-col[data-col-id="{{ $col->id }}"]').innerText}' data-col-id='{{ $col->id }}'>`;
            @endforeach
            // Ganti tombol aksi
            row.querySelector('td:last-child').innerHTML = `<button type='button' class='entry-save-btn dynamic-btn action-btn-save' data-id='${id}'>Simpan</button><button type='button' class='entry-cancel-btn dynamic-btn action-btn-cancel' data-id='${id}'>Batal</button>`;
            // Handler Simpan
            row.querySelector('.entry-save-btn').onclick = function() {
                const data = {};
                row.querySelectorAll('.entry-edit-input').forEach(input => {
                    data[input.dataset.colId] = input.value;
                });
                fetch(`/entries/${id}`, {
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
                        @foreach($columns as $col)
                        row.querySelector('.entry-col[data-col-id="{{ $col->id }}"]').innerText = data['{{ $col->id }}'];
                        @endforeach
                        row.querySelector('td:last-child').innerHTML = `<button type='button' class='entry-edit-btn dynamic-btn action-btn-edit' data-id='${id}'>Edit</button><button type='button' class='entry-delete-btn dynamic-btn action-btn-delete' data-id='${id}'>Hapus</button>`;
                        showNotif('Entry berhasil diupdate', 'success');
                    } else {
                        showNotif(json.message || 'Gagal update entry', 'danger');
                    }
                })
                .catch(err => {
                    showNotif('Terjadi error saat update entry: ' + err.message, 'danger');
                    console.error(err);
                });
            };
            // Handler Batal
            row.querySelector('.entry-cancel-btn').onclick = function() {
                let i = 0;
                row.querySelectorAll('.entry-col').forEach(td => { td.innerText = oldData[i++]; });
                row.querySelector('td:last-child').innerHTML = `<button type='button' class='entry-edit-btn dynamic-btn action-btn-edit' data-id='${id}'>Edit</button><button type='button' class='entry-delete-btn dynamic-btn action-btn-delete' data-id='${id}'>Hapus</button>`;
            };
        }
    });
});
</script>
@endpush 