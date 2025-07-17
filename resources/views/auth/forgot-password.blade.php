@extends('layouts.guest')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow border-0 p-4" style="max-width:400px; width:100%;">
        <h3 class="mb-3 fw-bold text-success text-center">Lupa Password</h3>
        <p class="text-secondary text-center mb-4">Masukkan email Anda, kami akan mengirimkan link untuk reset password.</p>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-success w-100 fw-bold">Kirim Link Reset Password</button>
        </form>
    </div>
</div>
@endsection
