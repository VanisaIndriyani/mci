<x-app-layout>
    @section('title', 'Manajemen User')

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0 fw-bold text-dark">Manajemen User</h5>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah User
            </button>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('users.index') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama atau email user..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                        <i class="bi bi-funnel me-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Pengguna</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Kontak</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted text-center">Hak Akses</th>
                        <th class="py-3 text-uppercase small fw-bold text-muted">Terdaftar</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark small"><i class="bi bi-envelope me-1 text-muted"></i> {{ $user->email }}</div>
                            </td>
                            <td class="text-center">
                                @php 
                                    $roleValue = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role;
                                    $roleBadge = $roleValue === 'admin' ? 'bg-danger-subtle text-danger' : 'bg-info-subtle text-info';
                                @endphp
                                <span class="badge {{ $roleBadge }} px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    {{ strtoupper($roleValue) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-muted small">{{ $user->created_at?->format('d M Y') }}</div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-light border edit-user-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editUserModal"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $roleValue }}"
                                            title="Edit User">
                                        <i class="bi bi-pencil-square text-warning"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light border" title="Hapus User">
                                                <i class="bi bi-trash3-fill text-danger"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people h1 d-block mb-3 opacity-25"></i>
                                    Belum ada data user.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-4 px-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Tambah User Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="admin">ADMIN</option>
                                <option value="manager">MANAGER</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" id="edit_role" class="form-select" required>
                                <option value="admin">ADMIN</option>
                                <option value="manager">MANAGER</option>
                            </select>
                        </div>
                        <div class="bg-light p-3 rounded-3 border">
                            <label class="form-label fw-semibold small mb-1">Ganti Password (Opsional)</label>
                            <input type="password" name="password" class="form-control form-control-sm" placeholder="Kosongkan jika tidak ingin ganti">
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning px-4 fw-bold">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-user-btn');
            const editForm = document.getElementById('editUserForm');
            const editName = document.getElementById('edit_name');
            const editEmail = document.getElementById('edit_email');
            const editRole = document.getElementById('edit_role');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const email = this.getAttribute('data-email');
                    const role = this.getAttribute('data-role');

                    editForm.action = `/users/${id}`;
                    editName.value = name;
                    editEmail.value = email;
                    editRole.value = role;
                });
            });
        });
    </script>
</x-app-layout>
