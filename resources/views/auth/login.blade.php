@extends('layouts.guest')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    .login-card {
        background: rgba(24,24,40,0.68);
        border-radius: 2rem;
        padding: 2.2rem 1.5rem 2rem 1.5rem;
        max-width: 370px;
        width: 100%;
        margin: 0 auto;
        box-shadow: 0 8px 32px #0003;
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
    }
    .logo-row {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1.2rem;
        margin-bottom: 1.7rem;
    }
    .logo-row img {
        max-height: 48px;
        background: rgba(255,255,255,0.13);
        border-radius: 1rem;
        padding: 0.3rem 1.2rem;
    }
    .login-label {
        color: #fff;
        font-size: 1rem;
        margin-bottom: 0.2rem;
        font-weight: 500;
    }
    .login-input {
        background: transparent;
        color: #fff;
        border: none;
        border-bottom: 2px solid #fff;
        border-radius: 0;
        font-size: 1.08rem;
        padding: 0.7rem 0.2rem 0.7rem 0.2rem;
        margin-bottom: 1.2rem;
        box-shadow: none;
    }
    .login-input:focus {
        background: transparent;
        color: #fff;
        border-bottom: 2px solid #ff2d2d;
        box-shadow: none;
    }
    .login-input::placeholder {
        color: #bbb;
        opacity: 1;
    }
    .login-btn {
        background: #ff2d2d;
        color: #fff;
        border: none;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 1.18rem;
        letter-spacing: 1px;
        box-shadow: 0 2px 8px #0002;
        padding: 0.95rem 0;
        margin-top: 0.7rem;
        margin-bottom: 0.7rem;
        width: 100%;
        transition: all .2s;
    }
    .login-btn:hover, .login-btn:focus {
        background: #d60000;
        color: #fff;
        box-shadow: 0 4px 16px #0003;
    }
    .login-links {
        font-size: 0.97rem;
        color: #fff;
        margin-bottom: 0.2rem;
        text-align: center;
    }
    .login-links a {
        color: #fff;
        text-decoration: underline;
        margin: 0 0.2rem;
    }
    @keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: none; } }
    .fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
</style>
<div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="login-card d-flex flex-column align-items-center justify-content-center fade-slide-up">
        <div class="logo-row">
            <img src="{{ asset('assets/img/logo_futami.png') }}" alt="Futami Logo">
            <img src="{{ asset('assets/img/logo_limit_x.png') }}" alt="Limit X Logo">
        </div>
        <form method="POST" action="{{ route('login') }}" class="w-100 d-flex flex-column align-items-center">
            @csrf
            <div class="w-100" style="max-width: 270px;">
                <label for="email" class="login-label">Username</label>
                <input id="email" type="email" name="email" class="form-control login-input @error('email') is-invalid @enderror" placeholder="Enter Email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-100" style="max-width: 270px;">
                <label for="password" class="login-label">Password</label>
                <input id="password" type="password" name="password" class="form-control login-input @error('password') is-invalid @enderror" placeholder="Enter Password" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn login-btn">Login</button>
            <div class="login-links w-100 mt-2">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa Password?</a>
                @endif
                <a href="{{ route('register') }}">Belum punya akun?</a>
            </div>
        </form>
    </div>
</div>
@section('scripts')
<script>
</script>
@endsection
@endsection
