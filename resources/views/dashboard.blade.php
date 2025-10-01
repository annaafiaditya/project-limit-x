@extends('layouts.app')

@section('content')

<!-- Animated Background -->
<div class="animated-background">
    <div class="floating-circles">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
        <div class="circle circle-4"></div>
        <div class="circle circle-5"></div>
        <div class="circle circle-6"></div>
        <div class="circle circle-7"></div>
        <div class="circle circle-8"></div>
        <div class="circle circle-9"></div>
        <div class="circle circle-10"></div>
        <div class="circle circle-11"></div>
        <div class="circle circle-12"></div>
        <div class="circle circle-13"></div>
        <div class="circle circle-14"></div>
        <div class="circle circle-15"></div>
        <div class="circle circle-16"></div>
        <div class="circle circle-17"></div>
        <div class="circle circle-18"></div>
        <div class="circle circle-19"></div>
        <div class="circle circle-20"></div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch("{{ route('dashboard.data') }}")
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            console.log('Dashboard data received:', data);
            
            // Donut chart judul form
            const ctx = document.getElementById('judulDonutChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.judul_labels || [],
                    datasets: [{
                        data: data.judul_data || [],
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
            document.getElementById('entryCount').textContent = data.entry_count || 0;

            // Approval pending
            const approvalBtn = document.getElementById('approvalPendingBtn');
            approvalBtn.textContent = data.approval_pending || 0;
            approvalBtn.onclick = function() {
                window.location.href = "{{ route('mikrobiologi-forms.index') }}?approval=pending";
            };

            // KIMIA widgets
            // Donut Kimia judul
            const ctxKimia = document.getElementById('kimiaJudulDonutChart').getContext('2d');
            new Chart(ctxKimia, {
                type: 'doughnut',
                data: {
                    labels: data.kimia_judul_labels || [],
                    datasets: [{
                        data: data.kimia_judul_data || [],
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
            document.getElementById('kimiaEntryCount').textContent = data.kimia_entry_count || 0;

            // Approval pending Kimia
            const approvalKimiaBtn = document.getElementById('kimiaApprovalPendingBtn');
            approvalKimiaBtn.textContent = data.kimia_approval_pending || 0;
            approvalKimiaBtn.onclick = function() {
                window.location.href = "{{ route('kimia.index') }}?approval=pending";
            };
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
            // Set default values if fetch fails
            document.getElementById('entryCount').textContent = '0';
            document.getElementById('approvalPendingBtn').textContent = '0';
            document.getElementById('kimiaEntryCount').textContent = '0';
            document.getElementById('kimiaApprovalPendingBtn').textContent = '0';
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

/* Animated Background Styles */
.animated-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    overflow: hidden;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
}

.floating-circles {
    position: absolute;
    width: 100%;
    height: 100%;
}

.circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.6;
    animation: gentleFloat 30s infinite ease-in-out;
}

.circle-1 {
    width: 120px;
    height: 120px;
    top: 5%;
    left: 10%;
    background: radial-gradient(circle, #34d399, #10b981);
    animation-delay: 0s;
    animation-duration: 25s;
}

.circle-2 {
    width: 80px;
    height: 80px;
    top: 20%;
    left: 80%;
    background: radial-gradient(circle, #60a5fa, #3b82f6);
    animation-delay: -5s;
    animation-duration: 35s;
}

.circle-3 {
    width: 100px;
    height: 100px;
    top: 40%;
    left: 5%;
    background: radial-gradient(circle, #fbbf24, #f59e0b);
    animation-delay: -10s;
    animation-duration: 28s;
}

.circle-4 {
    width: 90px;
    height: 90px;
    top: 70%;
    left: 85%;
    background: radial-gradient(circle, #34d399, #60a5fa);
    animation-delay: -15s;
    animation-duration: 32s;
}

.circle-5 {
    width: 110px;
    height: 110px;
    top: 10%;
    left: 60%;
    background: radial-gradient(circle, #fbbf24, #34d399);
    animation-delay: -8s;
    animation-duration: 22s;
}

.circle-6 {
    width: 70px;
    height: 70px;
    top: 60%;
    left: 30%;
    background: radial-gradient(circle, #60a5fa, #fbbf24);
    animation-delay: -20s;
    animation-duration: 40s;
}

.circle-7 {
    width: 130px;
    height: 130px;
    top: 80%;
    left: 50%;
    background: radial-gradient(circle, #34d399, #fbbf24);
    animation-delay: -12s;
    animation-duration: 26s;
}

.circle-8 {
    width: 60px;
    height: 60px;
    top: 30%;
    left: 70%;
    background: radial-gradient(circle, #60a5fa, #34d399);
    animation-delay: -18s;
    animation-duration: 38s;
}

.circle-9 {
    width: 95px;
    height: 95px;
    top: 50%;
    left: 15%;
    background: radial-gradient(circle, #fbbf24, #f59e0b);
    animation-delay: -3s;
    animation-duration: 24s;
}

.circle-10 {
    width: 85px;
    height: 85px;
    top: 15%;
    left: 40%;
    background: radial-gradient(circle, #34d399, #10b981);
    animation-delay: -25s;
    animation-duration: 42s;
}

.circle-11 {
    width: 75px;
    height: 75px;
    top: 85%;
    left: 20%;
    background: radial-gradient(circle, #60a5fa, #3b82f6);
    animation-delay: -7s;
    animation-duration: 30s;
}

.circle-12 {
    width: 105px;
    height: 105px;
    top: 25%;
    left: 90%;
    background: radial-gradient(circle, #fbbf24, #34d399);
    animation-delay: -22s;
    animation-duration: 36s;
}

.circle-13 {
    width: 65px;
    height: 65px;
    top: 45%;
    left: 75%;
    background: radial-gradient(circle, #60a5fa, #fbbf24);
    animation-delay: -14s;
    animation-duration: 33s;
}

.circle-14 {
    width: 115px;
    height: 115px;
    top: 75%;
    left: 5%;
    background: radial-gradient(circle, #34d399, #fbbf24);
    animation-delay: -9s;
    animation-duration: 27s;
}

.circle-15 {
    width: 55px;
    height: 55px;
    top: 35%;
    left: 25%;
    background: radial-gradient(circle, #fbbf24, #f59e0b);
    animation-delay: -16s;
    animation-duration: 29s;
}

.circle-16 {
    width: 125px;
    height: 125px;
    top: 55%;
    left: 60%;
    background: radial-gradient(circle, #60a5fa, #3b82f6);
    animation-delay: -4s;
    animation-duration: 31s;
}

.circle-17 {
    width: 85px;
    height: 85px;
    top: 90%;
    left: 40%;
    background: radial-gradient(circle, #34d399, #60a5fa);
    animation-delay: -21s;
    animation-duration: 37s;
}

.circle-18 {
    width: 95px;
    height: 95px;
    top: 65%;
    left: 90%;
    background: radial-gradient(circle, #fbbf24, #34d399);
    animation-delay: -11s;
    animation-duration: 23s;
}

.circle-19 {
    width: 70px;
    height: 70px;
    top: 5%;
    left: 35%;
    background: radial-gradient(circle, #60a5fa, #fbbf24);
    animation-delay: -19s;
    animation-duration: 41s;
}

.circle-20 {
    width: 140px;
    height: 140px;
    top: 50%;
    left: 45%;
    background: radial-gradient(circle, #34d399, #fbbf24);
    animation-delay: -6s;
    animation-duration: 34s;
}

@keyframes gentleFloat {
    0% {
        transform: translateY(0px) translateX(0px) scale(1);
        opacity: 0.6;
    }
    20% {
        transform: translateY(-20px) translateX(15px) scale(1.2);
        opacity: 0.8;
    }
    40% {
        transform: translateY(-10px) translateX(-20px) scale(0.8);
        opacity: 0.4;
    }
    60% {
        transform: translateY(-35px) translateX(10px) scale(1.1);
        opacity: 0.7;
    }
    80% {
        transform: translateY(-15px) translateX(-10px) scale(0.9);
        opacity: 0.5;
    }
    100% {
        transform: translateY(0px) translateX(0px) scale(1);
        opacity: 0.6;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .circle {
        animation-duration: 25s;
    }
    
    .circle-1, .circle-2, .circle-3, .circle-4, .circle-5, .circle-6, 
    .circle-7, .circle-8, .circle-9, .circle-10, .circle-11, .circle-12,
    .circle-13, .circle-14, .circle-15, .circle-16, .circle-17, .circle-18,
    .circle-19, .circle-20 {
        width: 50px;
        height: 50px;
    }
}
</style>
