@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 animate-fade-in-up">
    <div class="flex justify-between items-center mb-4 animate-fade-in-up">
        <h2 class="text-2xl font-bold text-green-900">Data Form Mikrobiologi</h2>
        <div class="flex gap-2 align-items-center">
            <a href="{{ route('mikrobiologi-forms.create') }}" class="btn btn-success px-4 py-2">+ Tambah Form</a>
            @if($template_titles->count())
            <form method="GET" action="{{ route('mikrobiologi-forms.create') }}" class="d-flex align-items-center gap-2 bg-white border border-success rounded px-2 py-1 shadow-sm" style="max-width: 340px;">
                <select name="template_title" id="template_title" class="form-select form-select-sm" style="min-width:220px; max-width:320px;" required>
                    <option value="" disabled selected>Pilih template...</option>
                    @foreach($template_titles as $title)
                        <option value="{{ $title }}">{{ $title }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-files"></i> Duplikat
                </button>
            </form>
            @endif
        </div>
    </div>
    <!-- Filter Approval -->
    <div class="mb-3 d-flex align-items-center gap-2 animate-fade-in-up">
        <form method="GET" action="{{ route('mikrobiologi-forms.index') }}" class="d-inline">
            <input type="hidden" name="approval" value="pending">
            <button type="submit" class="btn btn-outline-warning {{ request('approval') == 'pending' ? 'active fw-bold' : '' }}">
                <i class="bi bi-person-check"></i> Filter Approval (Belum Lengkap)
            </button>
        </form>
        @if(request('approval') == 'pending')
            <a href="{{ route('mikrobiologi-forms.index') }}" class="btn btn-outline-secondary">Tampilkan Semua</a>
        @endif
    </div>
    <!-- HAPUS seluruh form/dropdown/template terkait template form di sini -->
    <form method="GET" action="" class="row g-2 align-items-end mb-4 animate-fade-in-up">
        <div class="col-md-3">
            <label class="form-label mb-1">Cari Judul/No/Tanggal</label>
            <input type="text" name="search" placeholder="Cari..." value="{{ $search }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Cari Tanggal</label>
            <input type="date" name="search_tgl" value="{{ $search_tgl }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Group by Judul</label>
            <select name="group_title" class="form-select">
                <option value="">-- Semua Judul --</option>
                @foreach($titles as $title)
                    <option value="{{ $title }}" {{ $group_title == $title ? 'selected' : '' }}>{{ $title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filter</button>
        </div>
        <div class="col-md-1">
            <a href="{{ route('mikrobiologi-forms.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
    @if($group_title)
        <div class="alert alert-info py-2 mb-3 animate-fade-in-up">Menampilkan data untuk judul: <b>{{ $group_title }}</b></div>
    @endif
    @if(session('export_error'))
<div id="export-alert" class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    {!! session('export_error') !!}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
    <div class="bg-white shadow rounded-lg overflow-x-auto animate-fade-in-up" style="animation-delay:.15s; animation-duration:.8s;">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-200">
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Judul</th>
                    <th class="px-4 py-2">No Form</th>
                    <th class="px-4 py-2">Tgl Inokulasi</th>
                    <th class="px-4 py-2">Tgl Pengamatan</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $form)
                <tr class="hover:bg-yellow-50 cursor-pointer" onclick="window.location='{{ route('mikrobiologi-forms.show', ['mikrobiologi_form' => $form->id]) }}'" data-entry-count="{{ $form->entries->count() }}" data-approval-count="{{ $form->signatures->where('status', 'accept')->count() }}">
                    <td class="px-4 py-2">{{ $loop->iteration + ($forms->currentPage()-1)*$forms->perPage() }}</td>
                    <td class="px-4 py-2">{{ $form->title }}</td>
                    <td class="px-4 py-2">{{ $form->no }}</td>
                    <td class="px-4 py-2">{{ $form->tgl_inokulasi }}</td>
                    <td class="px-4 py-2">{{ $form->tgl_pengamatan }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('mikrobiologi-forms.show', ['mikrobiologi_form' => $form->id]) }}" class="text-blue-600 hover:underline">Lihat</a>
                        <a href="{{ route('mikrobiologi-forms.edit', ['mikrobiologi_form' => $form->id]) }}" class="text-yellow-600 hover:underline">Edit</a>
                        <a href="{{ route('mikrobiologi-forms.export', ['mikrobiologi_form' => $form->id]) }}" class="text-green-600 hover:underline">Export Excel</a>
                        <form action="{{ route('mikrobiologi-forms.destroy', ['mikrobiologi_form' => $form->id]) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-4 animate-fade-in-up">
        <div></div>
        <div class="d-flex align-items-center gap-2 p-2 bg-white rounded shadow-sm border" style="min-width:260px;">
            <form method="GET" action="" id="perPageForm" class="d-flex align-items-center me-2">
                @foreach(request()->except('perPage', 'page') as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <label for="perPage" class="me-2 mb-0 fw-semibold text-success" style="font-size:0.95em;">Tampil:</label>
                <select name="perPage" id="perPage" class="form-select form-select-sm w-auto border-success text-success fw-bold" style="background-color:#e9fbe9; font-size:0.95em;" onchange="document.getElementById('perPageForm').submit()">
                    <option value="10" {{ $perPage==10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ $perPage==20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ $perPage==50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage==100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    @if ($forms->onFirstPage())
                        <li class="page-item disabled"><span class="page-link bg-light border-0">&laquo;</span></li>
                    @else
                        <li class="page-item"><a class="page-link bg-warning text-success border-0 fw-bold" href="{{ $forms->previousPageUrl() }}">&laquo;</a></li>
                    @endif
                    @foreach ($forms->getUrlRange(1, $forms->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $forms->currentPage() ? 'active' : '' }}">
                            <a class="page-link {{ $page == $forms->currentPage() ? 'bg-success text-white border-success' : 'bg-light text-success border-0' }} fw-bold" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                    @if ($forms->hasMorePages())
                        <li class="page-item"><a class="page-link bg-warning text-success border-0 fw-bold" href="{{ $forms->nextPageUrl() }}">&raquo;</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link bg-light border-0">&raquo;</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Intercept klik Export Excel
  document.querySelectorAll('a[href*="/export"]').forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation(); // Supaya tidak trigger onclick tr
      const tr = link.closest('tr');
      const formId = link.href.match(/mikrobiologi-forms\/(\d+)\/export/)[1];
      const entryCount = tr.getAttribute('data-entry-count');
      const approvalCount = tr.getAttribute('data-approval-count');
      const judul = tr.querySelector('td:nth-child(2)')?.innerText || '-';
      const noForm = tr.querySelector('td:nth-child(3)')?.innerText || '-';
      let errorMsg = '';
      if (entryCount === null || approvalCount === null) {
        errorMsg = `Tidak bisa export: Data form tidak lengkap.<br><b>Judul:</b> ${judul} <b>No Form:</b> ${noForm}`;
      } else if (parseInt(entryCount) === 0) {
        errorMsg = `Tidak bisa export: Data entry kosong!<br><b>Judul:</b> ${judul} <b>No Form:</b> ${noForm}`;
      }
      if (errorMsg) {
        // Tampilkan alert di web
        let alertDiv = document.getElementById('export-alert');
        if (!alertDiv) {
          alertDiv = document.createElement('div');
          alertDiv.id = 'export-alert';
          alertDiv.className = 'alert alert-danger alert-dismissible fade show mb-3';
          alertDiv.role = 'alert';
          document.querySelector('.bg-white.shadow.rounded-lg').before(alertDiv);
        }
        alertDiv.innerHTML = errorMsg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        setTimeout(function() {
          if(alertDiv) alertDiv.style.display = 'none';
        }, 10000);
        return;
      }
      window.location.href = link.href;
    });
  });
});
</script>
@endpush

<style>
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: none; }
}
.animate-fade-in-up {
  animation: fadeInUp 0.7s cubic-bezier(.39,.575,.565,1) both;u
}
</style> 