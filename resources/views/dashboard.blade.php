@extends('layouts.app')

@section('content')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch("{{ route('dashboard.data') }}")
        .then(res => res.json())
        .then(data => {
            // Donut chart judul form
            const ctx = document.getElementById('judulDonutChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.judul_labels,
                    datasets: [{
                        data: data.judul_data,
                        backgroundColor: [
                            '#b9e4c9', '#b5d8f8', '#ffe6a7', '#f9c6c9', '#d6c8f5', '#b8e8f4', '#f7c6e0', '#fff3b0', '#c7f9cc', '#ffd6d6'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        legend: { display: true, position: 'bottom' }
                    },
                    cutout: '75%',
                    responsive: true,
                }
            });

            // Entry count
            document.getElementById('entryCount').textContent = data.entry_count;

            // Approval pending
            const approvalBtn = document.getElementById('approvalPendingBtn');
            approvalBtn.textContent = data.approval_pending;
            approvalBtn.onclick = function() {
                window.location.href = "{{ route('mikrobiologi-forms.index') }}?approval=pending";
            };

            // KIMIA widgets
            // Donut Kimia judul
            const ctxKimia = document.getElementById('kimiaJudulDonutChart').getContext('2d');
            new Chart(ctxKimia, {
                type: 'doughnut',
                data: {
                    labels: data.kimia_judul_labels,
                    datasets: [{
                        data: data.kimia_judul_data,
                        backgroundColor: [
                            '#b5d8f8', '#b9e4c9', '#ffe6a7', '#f9c6c9', '#d6c8f5', '#b8e8f4', '#f7c6e0', '#fff3b0', '#c7f9cc', '#ffd6d6'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: { legend: { display: true, position: 'bottom' } },
                    cutout: '75%',
                    responsive: true,
                }
            });

            // Entry count Kimia
            document.getElementById('kimiaEntryCount').textContent = data.kimia_entry_count;

            // Approval pending Kimia
            const approvalKimiaBtn = document.getElementById('kimiaApprovalPendingBtn');
            approvalKimiaBtn.textContent = data.kimia_approval_pending;
            approvalKimiaBtn.onclick = function() {
                window.location.href = "{{ route('kimia.index') }}?approval=pending";
            };
        });
});
</script>
@endpush

<div class="container py-5">
    <div class="row justify-content-center">
        <!-- Hero Section -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between p-4 p-md-5 shadow-lg hero-dashboard-card" 
                style="border-radius: 2rem; background: linear-gradient(120deg, #e0f7fa 0%, #f8fafc 100%); border: 1.5px solid #e0e7ef; box-shadow: 0 8px 32px #0001;">
                
                <!-- Teks Sambutan -->
                <div class="mb-4 mb-md-0 text-center text-md-start flex-grow-1">
                    <h1 class="fw-bold text-success mb-3" style="font-size: 2.6rem; letter-spacing: 0.5px; text-shadow: 0 2px 6px #b6f0e6;">
                        Selamat Datang, <span class="text-dark">"{{ Auth::user()->name }}"</span>!
                    </h1>

                    <div style="height: 4px; width: 90px; background: linear-gradient(90deg, #34d399, #60a5fa, #fbbf24); border-radius: 2px; margin-bottom: 1.2rem;"></div>

                    <p class="lead text-secondary mb-3" style="font-size: 1.15rem; line-height: 1.7;">
                        Saatnya melangkah menuju <strong>Laboratorium Digital yang Cerdas</strong>.<br>
                        Dengan <strong>Futami Limit-X</strong>, Anda tidak hanya mencatat data—<em>Anda membangun fondasi keputusan berbasis informasi real-time</em>.<br><br>
                        Sistem kami mengintegrasikan seluruh proses kerja laboratorium dalam satu platform yang <strong>aman</strong>, <strong>efisien</strong>, dan <strong>mudah digunakan</strong>.<br>
                        Catat data penting, pantau perkembangan, dan hasilkan laporan yang siap audit dengan lebih cepat dan presisi.<br><br>
                        <span class="text-info fw-semibold">Silakan scroll ke bawah untuk melihat petunjuk penggunaan, statistik, aksi cepat, dan catatan pribadi.</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Petunjuk Penggunaan -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up fade-slide-up-delay-1">
    <div class="card shadow border-0" style="border-radius: 1.3rem; background: linear-gradient(120deg, #f1f5f9 0%, #f8fafc 100%);">
        <div class="card-body px-4 py-5">
            <h4 class="fw-bold mb-4 text-primary d-flex align-items-center" style="font-size:1.35rem;">
                <i class="bi bi-info-circle me-2"></i> Cara Menggunakan Web Ini
            </h4>

            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-start p-3 rounded shadow-sm bg-white" style="border-left: 4px solid #34d399;">
                    <i class="bi bi-arrow-right-circle-fill text-success me-3" style="font-size: 1.4rem;"></i>
                    <div style="font-size: 1.08rem;">Gunakan <strong>Aksi Cepat</strong> untuk membuka Data/Form <strong>Mikrobiologi</strong> dan <strong>Kimia</strong>.</div>
                </div>
                <div class="d-flex align-items-start p-3 rounded shadow-sm bg-white" style="border-left: 4px solid #34d399;">
                    <i class="bi bi-arrow-right-circle-fill text-success me-3" style="font-size: 1.4rem;"></i>
                    <div style="font-size: 1.08rem;">Pada <strong>Kimia</strong>, Anda dapat membuat <strong>lebih dari satu tabel</strong> di dalam satu form. Tambah tabel dari bagian “Tabel pada Form Ini”, lalu tambah kolom dan input data per tabel.</div>
                </div>
                <div class="d-flex align-items-start p-3 rounded shadow-sm bg-white" style="border-left: 4px solid #34d399;">
                    <i class="bi bi-arrow-right-circle-fill text-success me-3" style="font-size: 1.4rem;"></i>
                    <div style="font-size: 1.08rem;">Fitur <strong>Template</strong> tersedia untuk <strong>Mikrobiologi</strong> dan <strong>Kimia</strong> (duplikasi form tanpa menyusun tabel ulang).</div>
                </div>
                <div class="d-flex align-items-start p-3 rounded shadow-sm bg-white" style="border-left: 4px solid #34d399;">
                    <i class="bi bi-arrow-right-circle-fill text-success me-3" style="font-size: 1.4rem;"></i>
                    <div style="font-size: 1.08rem;">Lakukan <strong>approval/tanda tangan</strong> pada 3 peran (Technician, Staff, Supervisor). Dashboard menampilkan jumlah yang <em>menunggu approval</em> untuk masing‑masing modul.</div>
                </div>
                <div class="d-flex align-items-start p-3 rounded shadow-sm bg-white" style="border-left: 4px solid #34d399;">
                    <i class="bi bi-arrow-right-circle-fill text-success me-3" style="font-size: 1.4rem;"></i>
                    <div style="font-size: 1.08rem;">Pantau ringkasan di dashboard: <strong>diagram judul</strong>, <strong>total entry</strong>, dan <strong>approval pending</strong> untuk Mikrobiologi dan Kimia.</div>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Statistik -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up fade-slide-up-delay-2">
            <div class="row g-3 align-items-stretch">
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 text-center p-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius:1.2rem; background:linear-gradient(120deg,#fef9c3 0%,#e0f2fe 100%); min-height:220px;">
                        <h6 class="fw-bold mb-2 text-success"><i class="bi bi-pie-chart me-2"></i> Diagram Judul Form</h6>
                        <div style="width:100%; max-width:180px; margin:0 auto;">
                            <canvas id="judulDonutChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 text-center py-4 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius:1.2rem; background:linear-gradient(120deg,#e0f2fe 0%,#f8fafc 100%); min-height:220px;">
                        <div class="mb-2"><i class="bi bi-list-task text-info" style="font-size:2.2rem;"></i></div>
                        <div class="fw-bold" id="entryCount" style="font-size:1.7rem;">...</div>
                        <div class="text-secondary">Entry Data</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 text-center py-4 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius:1.2rem; background:linear-gradient(120deg,#f7fff7 0%,#fef9c3 100%); min-height:220px;">
                        <div class="mb-2"><i class="bi bi-person-check text-primary" style="font-size:2.2rem;"></i></div>
                        <button id="approvalPendingBtn" class="btn btn-warning fw-bold px-4 py-2 mt-2" style="font-size:1.1rem; border-radius:1.2rem;">...</button>
                        <div class="text-secondary mt-2">Menunggu Approval Mikrobiologi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Kimia -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up fade-slide-up-delay-2">
            <div class="row g-3 align-items-stretch">
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 text-center p-3 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius:1.2rem; background:linear-gradient(120deg,#e0f2fe 0%, #f8fafc 100%); min-height:220px;">
                        <h6 class="fw-bold mb-2 text-primary"><i class="bi bi-pie-chart me-2"></i> Diagram Judul Form Kimia</h6>
                        <div style="width:100%; max-width:180px; margin:0 auto;">
                            <canvas id="kimiaJudulDonutChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 text-center py-4 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius:1.2rem; background:linear-gradient(120deg,#eef2ff 0%,#f8fafc 100%); min-height:220px;">
                        <div class="mb-2"><i class="bi bi-list-task text-primary" style="font-size:2.2rem;"></i></div>
                        <div class="fw-bold" id="kimiaEntryCount" style="font-size:1.7rem;">...</div>
                        <div class="text-secondary">Entry Data Kimia</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 text-center py-4 d-flex flex-column justify-content-center align-items-center h-100" style="border-radius:1.2rem; background:linear-gradient(120deg,#f7fff7 0%,#e0f2fe 100%); min-height:220px;">
                        <div class="mb-2"><i class="bi bi-person-check text-primary" style="font-size:2.2rem;"></i></div>
                        <button id="kimiaApprovalPendingBtn" class="btn btn-primary fw-bold px-4 py-2 mt-2" style="font-size:1.1rem; border-radius:1.2rem;">...</button>
                        <div class="text-secondary mt-2">Menunggu Approval Kimia</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions (Mikrobiologi kiri, Kimia kanan) -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up fade-slide-up-delay-3">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="card shadow border-0 p-4 h-100" style="border-radius:1.3rem; background:#f8fafc;">
                        <h5 class="fw-bold mb-3 text-primary d-flex align-items-center" style="font-size:1.1rem;">
                        <i class="bi bi-lightning-charge me-2"></i> Aksi Cepat Mikrobiologi
                    </h5>
                        <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('mikrobiologi-forms.create') }}" class="btn btn-outline-success px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-plus-circle me-1"></i> Form Baru</a>
                    <a href="{{ route('mikrobiologi-forms.index') }}" class="btn btn-outline-info px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-table me-1"></i> Data Form</a>
                    <a href="{{ route('mikrobiologi-forms.index') }}?template=1" class="btn btn-outline-warning px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-files me-1"></i> Template Form</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card shadow border-0 p-4 h-100" style="border-radius:1.3rem; background:#f8fafc;">
                        <h5 class="fw-bold mb-3 text-primary d-flex align-items-center" style="font-size:1.1rem;">
                            <i class="bi bi-lightning-charge me-2"></i> Aksi Cepat Kimia
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('kimia.create') }}" class="btn btn-outline-primary px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-plus-circle me-1"></i> Form Kimia Baru</a>
                            <a href="{{ route('kimia.index') }}" class="btn btn-outline-info px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-table me-1"></i> Data Form Kimia</a>
                            <a href="{{ route('kimia.index') }}?template=1" class="btn btn-outline-warning px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-files me-1"></i> Template Form</a>
                        </div>
                    </div>
                </div>
            </div>
                </div>

        <!-- Catatan (paling bawah) -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up fade-slide-up-delay-4">
            <div class="card shadow border-0 p-4" style="border-radius:1.3rem; background:#f8fafc;">
                        <h4 class="fw-bold mb-3 text-success d-flex align-items-center" style="font-size:1.2rem;">
                            <i class="bi bi-journal-text me-2"></i> Catatan Saya
                        </h4>
                        <form method="POST" action="{{ route('dashboard.note') }}">
                            @csrf
                    <textarea name="note" class="form-control mb-2" rows="5" placeholder="Tulis catatan pribadi di sini..." style="border-radius:1rem; background:#f8fafc; font-size:1.05rem; resize:vertical; width:100%;">{{ trim(old('note', Auth::user()->note ?? '')) }}</textarea>
                            <div class="text-end">
                        <button type="submit" class="btn btn-success px-4 py-2 mt-1" style="border-radius:1.2rem; font-weight:500; font-size:0.98rem;">Simpan</button>
                            </div>
                            @if(session('note_saved'))
                        <div class="alert alert-success mt-2" style="border-radius:1rem; font-size:0.97rem;">Catatan berhasil disimpan!</div>
                            @endif
                        </form>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: none; }
}
.fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
.fade-slide-up-delay-1 { animation-delay: .15s; }
.fade-slide-up-delay-2 { animation-delay: .3s; }
.fade-slide-up-delay-3 { animation-delay: .45s; }
.fade-slide-up-delay-4 { animation-delay: .6s; }
.hero-dashboard-card:hover .hero-lab-icon {
    transform: scale(1.06) rotate(-3deg);
    box-shadow:0 8px 32px #34d39933;
}
</style>
