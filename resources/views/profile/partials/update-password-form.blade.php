<section>
    <h4 class="mb-3 fw-bold text-success">Ubah Password</h4>
    <form method="post" action="{{ route('password.update') }}" class="mb-0">
        @csrf
        @method('put')
        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Password Lama</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="update_password_password" class="form-label">Password Baru</label>
            <input id="update_password_password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @if (session('status') === 'password-updated')
            <div class="alert alert-success py-2 mb-3">Password berhasil diubah.</div>
        @endif
        <button type="submit" class="btn btn-success fw-bold px-4">Simpan Password</button>
    </form>
</section>
