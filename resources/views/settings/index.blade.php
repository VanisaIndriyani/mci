<x-app-layout>
    @section('title', 'Pengaturan Sistem')

    <x-slot name="header">
        <h5 class="mb-0 fw-bold text-dark">Pengaturan Sistem</h5>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')
                
                <!-- Company Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-building me-2 text-primary"></i> Identitas Perusahaan</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small">Nama Perusahaan</label>
                                <input type="text" name="company_name" class="form-control" value="{{ $settings['company_name'] }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small">Slogan / Deskripsi</label>
                                <textarea name="company_slogan" class="form-control" rows="2" required>{{ $settings['company_slogan'] }}</textarea>
                                <div class="form-text">Akan tampil di navigasi dan halaman login.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- App Preferences -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-gear-wide-connected me-2 text-primary"></i> Preferensi Aplikasi</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Mata Uang (Currency)</label>
                                <input type="text" name="currency_symbol" class="form-control" value="{{ $settings['currency_symbol'] }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Unit Default</label>
                                <input type="text" name="default_unit" class="form-control" value="{{ $settings['default_unit'] }}" required>
                                <div class="form-text">Contoh: Pcs, Kg, Set.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Versi Aplikasi</label>
                                <input type="text" name="app_version" class="form-control" value="{{ $settings['app_version'] }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 shadow-sm py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px;">Info Sistem</h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small opacity-75">Laravel Version</span>
                            <span class="fw-bold small">{{ app()->version() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small opacity-75">PHP Version</span>
                            <span class="fw-bold small">{{ PHP_VERSION }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small opacity-75">Environment</span>
                            <span class="badge bg-white bg-opacity-25">{{ app()->environment() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <img src="{{ asset('img/logomci.png') }}" alt="Logo" class="mb-3" style="width: 80px; height: 80px; object-fit: contain;">
                    <h6 class="fw-bold mb-1">Preview Identitas</h6>
                    <p class="text-muted small mb-0">{{ $settings['company_name'] }}</p>
                    <hr class="my-3 opacity-25">
                    <p class="text-muted small italic" style="font-size: 0.7rem;">"{{ $settings['company_slogan'] }}"</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
