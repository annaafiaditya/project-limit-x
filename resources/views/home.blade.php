<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futami Limit-X</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('build/assets/img/logo_daun_futami.png') }}">
    <style>
        body { background: linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%); min-height: 100vh; }
        .btn-green { background: #16a34a; color: #fff; }
        .btn-green:hover { background: #166534; color: #fff; }
        .btn-yellow { background: #fde047; color: #166534; }
        .btn-yellow:hover { background: #facc15; color: #166534; }
        .home-card { background: #fff; border-radius: 1.5rem; box-shadow: 0 4px 32px 0 #0001; padding: 2.5rem 2rem; max-width: 480px; }
        .logo-row img { max-height: 60px; }
        @media (max-width: 600px) { .home-card { padding: 1.5rem 0.5rem; } .logo-row img { max-height: 40px; } }
    </style>
</head>
<body>
    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">
        <div class="home-card w-100 text-center">
            <div class="d-flex justify-content-center align-items-center gap-4 mb-4 logo-row">
                <img src="{{ asset('build/assets/img/futami_bg_excel.png') }}" alt="Futami" class="img-fluid">
                <img src="{{ asset('build/assets/img/logo_limit_x.png') }}" alt="Limit X" class="img-fluid">
            </div>
            <h1 class="display-5 fw-bold text-success mb-3">Futami Limit-X</h1>
            <p class="lead text-secondary mb-4">Sistem pencatatan, monitoring, dan pelaporan data mikrobiologi laboratorium dengan fitur modern, mudah digunakan, dan aman.</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('login') }}" class="btn btn-green btn-lg px-5 rounded-pill shadow">Login</a>
                <a href="{{ route('register') }}" class="btn btn-yellow btn-lg px-5 rounded-pill shadow">Register</a>
            </div>
        </div>
    </div>
</body>
</html> 