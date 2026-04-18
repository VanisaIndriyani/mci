<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit User: {{ $user->name }}</h5>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('users.index') }}">Kembali</a>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Role</label>
                        @php $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role; @endphp
                        <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                            @foreach($roles as $r)
                                <option value="{{ $r }}" @if(old('role', $roleValue) === $r) selected @endif>{{ strtoupper($r) }}</option>
                            @endforeach
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Password Baru (opsional)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="password_confirmation">
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>

            <form class="mt-3" method="POST" action="{{ route('users.destroy', $user) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Hapus user ini?')">Hapus User</button>
            </form>
        </div>
    </div>
</x-app-layout>
