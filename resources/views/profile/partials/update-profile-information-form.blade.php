<section>
    <h4 class="mb-3 fw-bold text-success">Edit Profil</h4>
    <form method="post" action="{{ route('profile.update') }}" class="mb-0">
        @csrf
        @method('patch')
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning py-2 px-3 mt-2">
                    Email Anda belum diverifikasi. <button form="send-verification" class="btn btn-link p-0 align-baseline">Kirim ulang verifikasi</button>
                </div>
                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success py-2 px-3 mt-2">Link verifikasi baru sudah dikirim ke email Anda.</div>
                @endif
            @endif
        </div>
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success py-2 mb-3">Profil berhasil diupdate.</div>
        @endif
        <button type="submit" class="btn btn-success fw-bold px-4">Simpan Profil</button>
    </form>
</section>
