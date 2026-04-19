<x-guest-layout>
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-aesthetic p-3 p-md-4">
        <div class="card border-0 shadow-aesthetic overflow-hidden rounded-4" style="max-width: 850px; width: 100%;">
            <div class="row g-0">
                <!-- Left Side: Premium Branding -->
                <div class="col-lg-5 bg-brand d-flex flex-column align-items-center justify-content-center p-4 p-lg-5 text-center border-end position-relative overflow-hidden">
                    <!-- Subtle Background Decoration -->
                    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-5 pointer-events-none" style="background-image: radial-gradient(var(--mci-blue) 1px, transparent 1px); background-size: 20px 20px;"></div>
                    
                    <div class="position-relative z-1">
                        <div class="mb-4">
                            <div class="logo-wrapper p-3 rounded-circle bg-primary bg-opacity-10 d-inline-block mb-3 shadow-sm">
                                <img src="{{ asset('img/logomci.png') }}" alt="Logo MCI" style="width: 55px; height: 55px; object-fit: contain;">
                            </div>
                            <h4 class="fw-bold text-dark mb-2 tracking-tight">{{ \App\Models\Setting::getValue('company_name', 'MCI SYSTEM') }}</h4>
                            <div class="bg-primary opacity-25 mb-3 mx-auto" style="width: 35px; height: 3px; border-radius: 2px;"></div>
                            <p class="small text-muted mb-0 px-2 lh-base fw-medium">
                                {{ \App\Models\Setting::getValue('company_slogan', 'Designing and manufacture for Jig, SPM and Mechanical component') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-auto pt-4 opacity-50 d-none d-lg-block position-relative z-1">
                        <small class="text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">&copy; 2026 {{ \App\Models\Setting::getValue('company_name', 'CV MIRSA CIPTA INDONESIA') }}</small>
                    </div>
                </div>

                <!-- Right Side: Modern Login Form -->
                <div class="col-lg-7 bg-white p-4 p-md-5">
                    <div class="mb-4">
                        <h3 class="fw-bold text-dark mb-1 h2">Selamat Datang</h3>
                        <p class="text-muted small">Silakan masuk untuk mengakses dashboard Anda.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 p-3 small" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-muted mb-2">Email Address</label>
                            <div class="input-group-aesthetic">
                                <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                <input id="email" class="form-control-aesthetic" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@perusahaan.com">
                            </div>
                            @error('email') <div class="text-danger small mt-1" style="font-size: 0.7rem;">{{ $message }}</div> @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-muted mb-2">Password</label>
                            <div class="input-group-aesthetic">
                                <span class="input-icon"><i class="bi bi-lock"></i></span>
                                <input id="password" class="form-control-aesthetic" type="password" name="password" required placeholder="Masukkan password">
                                <button class="btn-toggle-pw" type="button" id="togglePassword">
                                    <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                </button>
                            </div>
                            @error('password') <div class="text-danger small mt-1" style="font-size: 0.7rem;">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input id="remember_me" class="form-check-input custom-check" type="checkbox" name="remember">
                                <label class="form-check-label text-muted small" for="remember_me">Ingat Saya</label>
                            </div>
                        
                        </div>

                        <button type="submit" class="btn btn-aesthetic w-100 py-2 fw-bold shadow-sm rounded-3 mb-4">
                            Masuk Sekarang <i class="bi bi-arrow-right ms-2 small"></i>
                        </button>

                        <!-- Demo Access -->
                        <div class="demo-section p-3 rounded-4 border border-dashed text-center bg-light bg-opacity-50">
                            <div class="text-muted small mb-3 fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.5px;">Akses Demo Cepat</div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="demo-pill" onclick="fillLogin('admin@mci.test')">
                                        <span class="fw-bold">Admin</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="demo-pill" onclick="fillLogin('manager@mci.test')">
                                        <span class="fw-bold">Manager</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --mci-blue: #0d6efd;
            --mci-accent: #7c3aed;
            --primary-gradient: linear-gradient(135deg, var(--mci-blue) 0%, var(--mci-accent) 100%);
            --aesthetic-bg:
                radial-gradient(900px circle at 15% 0%, rgba(13, 110, 253, 0.12) 0%, rgba(13, 110, 253, 0) 55%),
                radial-gradient(900px circle at 85% 15%, rgba(124, 58, 237, 0.10) 0%, rgba(124, 58, 237, 0) 55%),
                #f4f7fe;
        }

        .bg-aesthetic {
            background: var(--aesthetic-bg);
            background-attachment: fixed;
        }

        .shadow-aesthetic {
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08) !important;
        }

        .tracking-tight { letter-spacing: -0.02em; }
        .bg-brand {
            background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,250,255,0.95) 100%);
        }
        
        /* Custom Input Styling */
        .input-group-aesthetic {
            position: relative;
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
            padding: 2px;
        }

        .input-group-aesthetic:focus-within {
            background: #fff;
            border-color: var(--mci-blue);
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        .input-icon {
            padding: 0 12px;
            color: #adb5bd;
            font-size: 0.9rem;
        }

        .form-control-aesthetic {
            flex: 1;
            background: transparent;
            border: none;
            padding: 10px 12px 10px 0;
            font-size: 0.875rem;
            color: #495057;
            outline: none;
        }

        .btn-toggle-pw {
            background: transparent;
            border: none;
            padding: 0 15px;
            color: #adb5bd;
            transition: color 0.2s;
        }

        .btn-toggle-pw:hover { color: var(--mci-blue); }

        /* Button Styling */
        .btn-aesthetic {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 12px !important;
            transition: all 0.3s ease;
        }

        .btn-aesthetic:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2);
            filter: brightness(1.1);
        }

        /* Checkbox Styling */
        .custom-check:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* Demo Pill Styling */
        .demo-pill {
            background: white;
            border: 1px solid #e9ecef;
            padding: 8px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.75rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .demo-pill:hover {
            border-color: var(--mci-blue);
            background: #f8f9ff;
            color: var(--mci-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.1);
        }

        .hover-underline:hover { text-decoration: underline !important; }
        
        .logo-wrapper {
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .card:hover .logo-wrapper {
            transform: rotate(5deg) scale(1.05);
        }

        .border-dashed { border-style: dashed !important; border-width: 1.5px !important; }
    </style>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }
        });

        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</x-guest-layout>
