<x-app-layout>
    @section('title', 'Profil Pengguna')

    <x-slot name="header">
        <h5 class="mb-0 fw-bold text-dark">Profil Pengguna</h5>
    </x-slot>

    <div class="row g-4">
        <!-- Profile Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4 h-100">
                <div class="card-body">
                    <div class="mb-4 d-inline-block position-relative">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Foto Profil" class="rounded-circle shadow-lg" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-lg" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white border-2 rounded-circle p-2" title="Online"></span>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark">{{ $user->name }}</h4>
                    <p class="text-muted mb-3 small"><i class="bi bi-envelope me-1"></i> {{ $user->email }}</p>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill text-uppercase mb-4" style="font-size: 0.7rem; letter-spacing: 1px;">
                        {{ strtoupper($user->role->value ?? (string)$user->role) }}
                    </span>
                    
                    <div class="border-top pt-4 mt-2">
                        <div class="row text-start g-3">
                            <div class="col-12">
                                <div class="text-muted small mb-1">Terdaftar Sejak</div>
                                <div class="fw-semibold text-dark small">{{ $user->created_at?->translatedFormat('d F Y') }}</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small mb-1">Terakhir Login</div>
                                <div class="fw-semibold text-dark small">{{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white p-0 border-bottom">
                    <ul class="nav nav-tabs border-0 px-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-3 px-4 fw-semibold border-0 border-bottom border-2 border-transparent" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">
                                <i class="bi bi-person-lines-fill me-2"></i> Informasi Profil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 px-4 fw-semibold border-0 border-bottom border-2 border-transparent" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-pane" type="button" role="tab">
                                <i class="bi bi-shield-lock-fill me-2"></i> Keamanan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-3 px-4 fw-semibold border-0 border-bottom border-2 border-transparent text-danger" id="danger-tab" data-bs-toggle="tab" data-bs-target="#danger-pane" type="button" role="tab">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Bahaya
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Information Pane -->
                        <div class="tab-pane fade show active" id="info-pane" role="tabpanel" tabindex="0">
                            <h6 class="fw-bold mb-3">Informasi Akun</h6>
                            <p class="text-muted small mb-4">Perbarui informasi profil dan alamat email akun Anda.</p>
                            
                            <form method="post" action="{{ route('profile.update') }}" class="row g-3" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Alamat Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Foto Profil</label>
                                    <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">JPG, PNG, WEBP (maks 5MB)</div>
                                </div>

                                @if($user->profile_photo_path)
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="remove_profile_photo" name="remove_profile_photo">
                                            <label class="form-check-label small text-muted" for="remove_profile_photo">Hapus foto profil</label>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                                    </button>
                                    @if (session('status') === 'profile-updated')
                                        <span class="text-success small ms-3 animate-fade-out"><i class="bi bi-check-circle-fill"></i> Berhasil disimpan.</span>
                                    @endif
                                </div>
                            </form>
                        </div>

                        <!-- Password Pane -->
                        <div class="tab-pane fade" id="password-pane" role="tabpanel" tabindex="0">
                            <h6 class="fw-bold mb-3">Perbarui Kata Sandi</h6>
                            <p class="text-muted small mb-4">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>
                            
                            <form method="post" action="{{ route('password.update') }}" class="row g-3">
                                @csrf
                                @method('put')
                                
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Kata Sandi Saat Ini</label>
                                    <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                                    @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Kata Sandi Baru</label>
                                    <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                                    @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Konfirmasi Kata Sandi</label>
                                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                                    @error('password_confirmation', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                        <i class="bi bi-shield-check me-1"></i> Perbarui Password
                                    </button>
                                    @if (session('status') === 'password-updated')
                                        <span class="text-success small ms-3 animate-fade-out"><i class="bi bi-check-circle-fill"></i> Password berhasil diganti.</span>
                                    @endif
                                </div>
                            </form>
                        </div>

                        <!-- Danger Zone Pane -->
                        <div class="tab-pane fade" id="danger-pane" role="tabpanel" tabindex="0">
                            <div class="bg-danger-subtle p-4 rounded-3 border border-danger-subtle">
                                <h6 class="fw-bold text-danger mb-3">Hapus Akun</h6>
                                <p class="text-danger small mb-4">Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Silakan unduh data apa pun yang ingin Anda simpan sebelum melanjutkan.</p>
                                
                                <button type="button" class="btn btn-danger shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#confirmDeletionModal">
                                    <i class="bi bi-trash3-fill me-1"></i> Hapus Akun Selamanya
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeletionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">Konfirmasi Hapus Akun</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-body p-4">
                        <p class="fw-bold mb-2">Apakah Anda yakin ingin menghapus akun?</p>
                        <p class="text-muted small mb-4">Harap masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.</p>
                        
                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Kata Sandi Konfirmasi</label>
                            <input type="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Masukkan password Anda" required>
                            @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4">Ya, Hapus Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .nav-tabs .nav-link {
            color: #6c757d;
            border-radius: 0;
            transition: all 0.2s ease;
        }
        .nav-tabs .nav-link:hover {
            color: var(--mci-blue);
            background-color: #f8faff;
        }
        .nav-tabs .nav-link.active {
            color: var(--mci-blue) !important;
            border-bottom-color: var(--mci-blue) !important;
            background-color: transparent !important;
        }
        .nav-tabs .nav-link.text-danger.active {
            color: #dc3545 !important;
            border-bottom-color: #dc3545 !important;
        }
        .animate-fade-out {
            animation: fadeOut 3s forwards;
        }
        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
</x-app-layout>
