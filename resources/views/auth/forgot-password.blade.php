<x-guest-layout>
    <div class="text-muted mb-3">
        Masukkan email untuk menerima link reset password.
    </div>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
        </div>
    </form>
</x-guest-layout>
