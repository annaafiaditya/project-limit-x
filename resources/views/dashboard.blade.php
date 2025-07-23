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
        });
});
</script>
@endpush
<div class="container py-5">
    <div class="row justify-content-center">
        <!-- Hero Section -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between p-4 p-md-5 shadow-lg hero-dashboard-card" style="border-radius:2rem; background: linear-gradient(120deg, #e0f7fa 0%, #f8fafc 100%); border: 1.5px solid #e0e7ef; box-shadow: 0 8px 32px #0001;">
                <div class="mb-4 mb-md-0 text-center text-md-start flex-grow-1">
                    <h1 class="fw-bold text-success mb-2" style="font-size:2.7rem; letter-spacing:1px; text-shadow:0 2px 8px #b6f0e6;">
                        Selamat Datang "{{ Auth::user()->name }}" di Futami Limit-X
                    </h1>
                    <div style="height:3px; width:80px; background:linear-gradient(90deg,#34d399,#60a5fa,#fbbf24); border-radius:2px; margin-bottom:1.1rem;"></div>
                    <p class="lead text-secondary mb-3" style="font-size:1.18rem; max-width:650px;">
                        Saatnya Melangkah Menuju Laboratorium Digital yang Cerdas<br>
                        Dengan Futami Limit-X, Anda tidak hanya mencatat data—Anda membangun fondasi keputusan yang berbasis informasi akurat dan real-time.<br>
                        Sistem kami mengintegrasikan seluruh proses kerja laboratorium dalam satu platform modern yang aman, efisien, dan mudah digunakan.<br>
                        Catat setiap data penting, pantau perkembangan secara menyeluruh, dan hasilkan laporan yang siap audit dengan lebih cepat dan presisi.<br>
                        <span class="text-info">Silakan scroll ke bawah untuk melihat petunjuk penggunaan, statistik, aksi cepat, dan catatan pribadi.</span>
                    </p>
                </div>
                <div class="text-center ms-md-4 d-flex flex-column align-items-center justify-content-center">
                    <div class="hero-lab-icon" style="background:rgba(52,211,153,0.10); border-radius:2rem; padding:1.5rem 1.7rem; box-shadow:0 4px 24px #34d39922; transition:transform .2s;">
                        <i class="bi bi-beaker" style="font-size:5.5rem; color:#34d399;"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Petunjuk Penggunaan -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up fade-slide-up-delay-1">
            <div class="card shadow border-0" style="border-radius: 1.3rem; background: linear-gradient(120deg, #f1f5f9 0%, #f8fafc 100%);">
                <div class="card-body">
                    <h4 class="fw-bold mb-3 text-primary d-flex align-items-center" style="font-size:1.3rem;"><i class="bi bi-info-circle me-2"></i> Cara Menggunakan Web Ini</h4>
                    <ul class="list-group list-group-flush text-start" style="font-size:1.08rem;">
                        <li class="list-group-item border-0 ps-0"><i class="bi bi-arrow-right-circle text-success me-2"></i>Pilih menu di atas untuk mengakses data atau membuat form baru.</li>
                        <li class="list-group-item border-0 ps-0"><i class="bi bi-arrow-right-circle text-success me-2"></i>Isi data pada form sesuai kebutuhan laboratorium.</li>
                        <li class="list-group-item border-0 ps-0"><i class="bi bi-arrow-right-circle text-success me-2"></i>Simpan data, dan lakukan approval jika diperlukan.</li>
                        <li class="list-group-item border-0 ps-0"><i class="bi bi-arrow-right-circle text-success me-2"></i>Data yang sudah diinput bisa dipantau, diubah, atau diunduh sesuai kebutuhan.</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Statistik & Chart -->
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
        <!-- Quick Actions -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up fade-slide-up-delay-3">
            <div class="card shadow border-0 p-4" style="border-radius:1.3rem; background: #f8fafc;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h5 class="fw-bold mb-2 text-primary d-flex align-items-center" style="font-size:1.1rem;"><i class="bi bi-lightning-charge me-2"></i> Aksi Cepat Mikrobiologi</h5>
                    </div>
                    <div>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary px-3 py-2 ms-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-person me-1"></i> Profil</a>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    <a href="{{ route('mikrobiologi-forms.create') }}" class="btn btn-outline-success px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-plus-circle me-1"></i> Form Baru</a>
                    <a href="{{ route('mikrobiologi-forms.index') }}" class="btn btn-outline-info px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-table me-1"></i> Data Form</a>
                    <a href="{{ route('mikrobiologi-forms.index') }}?template=1" class="btn btn-outline-warning px-3 py-2" style="border-radius:1.2rem; font-weight:500;"><i class="bi bi-files me-1"></i> Template Form</a>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-6">
                        <h4 class="fw-bold mb-2 text-success d-flex align-items-center" style="font-size:1.2rem;"><i class="bi bi-journal-text me-2"></i> Catatan Saya</h4>
                        <form method="POST" action="{{ route('dashboard.note') }}">
                            @csrf
                            <textarea name="note" class="form-control mb-2" rows="2" placeholder="Tulis catatan pribadi di sini..." style="border-radius: 1rem; background: #f8fafc; font-size:1.05rem;">{{ old('note', Auth::user()->note) }}</textarea>
                            <button type="submit" class="btn btn-success px-3 py-1" style="border-radius: 1.2rem; font-weight:500; font-size:0.98rem;">Simpan</button>
                            @if(session('note_saved'))
                                <div class="alert alert-success mt-2" style="border-radius:1rem; font-size:0.97rem;">Catatan berhasil disimpan!</div>
                            @endif
                        </form>
                    </div>
                </div>
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
