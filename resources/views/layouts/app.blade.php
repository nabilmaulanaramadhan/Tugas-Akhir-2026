<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VMEDIS | @yield('title', 'Manajemen Poin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background-color: #f8f9fc; color: #000000; overflow-x: hidden; }
        .sidebar {
            background-color: #1A202C;
            min-height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: #e2e8f0;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #2d3748;
            color: white;
        }
        .sidebar .nav-link i { margin-right: 12px; width: 20px; }
        .sidebar .brand {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            border-bottom: 1px solid #2d3748;
        }
        /* Posisi menu Pengaturan di bawah */
        .sidebar .nav.mt-auto {
            margin-top: auto;
        }
        .main-content { margin-left: 260px; }
        .navbar-custom {
            background: white;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }
        .dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            border: none;
        }
        /* Card & Table */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .table > :not(caption) > * > * {
            border-bottom: 1px solid #dee2e6;
            color: #000;
            vertical-align: middle;
        }
        .table thead th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .btn {
            border-radius: 12px;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }
        .btn-sm {
            border-radius: 10px;
        }
        .btn-icon-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 4px;
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid #ced4da;
            padding: 0.6rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.25);
            border-color: #86b7fe;
        }
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-weight: 500;
        }
        .dot-blue {
            width: 10px;
            height: 10px;
            background: #0d6efd;
            border-radius: 50%;
            display: inline-block;
            margin-right: 12px;
        }
        @media (max-width: 768px) {
            .sidebar { margin-left: -260px; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar d-flex flex-column">
    <div class="brand">📊 VMEDIS</div>
    <ul class="nav flex-column mt-3 flex-grow-1">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('karyawan*') ? 'active' : '' }}" href="{{ route('karyawan') }}"><i class="bi bi-people"></i> Karyawan</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('project*') ? 'active' : '' }}" href="{{ route('project.index') }}"><i class="bi bi-folder2"></i> Project</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('task*') ? 'active' : '' }}" href="{{ route('task.index') }}"><i class="bi bi-check2-square"></i> Task</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('kelola-poin') ? 'active' : '' }}" href="{{ route('kelola-poin') }}"><i class="bi bi-star"></i> Kelola Poin</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('kelola-gaji') ? 'active' : '' }}" href="{{ route('kelola-gaji') }}"><i class="bi bi-calculator"></i> Gaji</a></li>
    </ul>
    <ul class="nav flex-column mt-auto mb-3">
        <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#pengaturanModal"><i class="bi bi-gear"></i> Pengaturan</a></li>
    </ul>
</div>

<div class="main-content">
    <!-- Navbar -->
<nav class="navbar navbar-custom d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <i class="bi bi-list fs-4 me-3 d-lg-none" id="sidebarToggle"></i>
        <div class="welcome-message fw-semibold text-dark">
            Selamat datang, <span id="profileNameDisplay">Hana Wijaya</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-bell fs-5 text-dark" style="cursor: pointer;" onclick="Swal.fire('Info', 'Notifikasi akan segera hadir', 'info')"></i>
        <div class="dropdown">
            <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                <img src="https://randomuser.me/api/portraits/women/68.jpg" id="profileAvatarImg" class="profile-img" alt="profile">
                <span class="fw-semibold" id="profileNameNav">Hana Wijaya</span>
                <i class="bi bi-chevron-down small"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" id="editProfileBtn"><i class="bi bi-person-gear"></i> Edit Profil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

    <div class="container-fluid p-4">
        @yield('content')
    </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="profileName" placeholder="Nama">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="profileEmail" placeholder="Email">
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto Profil (URL)</label>
                    <input type="text" class="form-control" id="profileAvatar" placeholder="https://...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveProfileBtn">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pengaturan (placeholder) -->
<div class="modal fade" id="pengaturanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengaturan Sistem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Pengaturan aplikasi akan segera hadir.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
   // Load data profil dari localStorage
let userProfile = JSON.parse(localStorage.getItem('vmedis_profile')) || {
    name: 'Hana Wijaya',
    email: 'hana@vmedis.com',
    avatar: 'https://randomuser.me/api/portraits/women/68.jpg'
};

function updateProfileUI() {
    document.getElementById('profileNameDisplay').innerText = userProfile.name;
    document.getElementById('profileAvatarImg').src = userProfile.avatar;
    document.querySelector('.welcome-message').innerHTML = `Selamat datang, ${userProfile.name}`;
}

// Buka modal dan isi data saat ini
document.getElementById('editProfileBtn').addEventListener('click', function() {
    document.getElementById('profileName').value = userProfile.name;
    document.getElementById('profileEmail').value = userProfile.email;
    document.getElementById('profileAvatar').value = userProfile.avatar;
    new bootstrap.Modal(document.getElementById('editProfileModal')).show();
});

// Tombol simpan (menggunakan event listener, bukan onclick)
document.getElementById('saveProfileBtn').addEventListener('click', function() {
    const newName = document.getElementById('profileName').value.trim();
    const newEmail = document.getElementById('profileEmail').value.trim();
    const newAvatar = document.getElementById('profileAvatar').value.trim();

    if (newName === '') {
        Swal.fire('Error', 'Nama tidak boleh kosong', 'error');
        return;
    }

    userProfile.name = newName;
    userProfile.email = newEmail;
    userProfile.avatar = newAvatar || 'https://randomuser.me/api/portraits/women/68.jpg';
    localStorage.setItem('vmedis_profile', JSON.stringify(userProfile));
    updateProfileUI();

    // Tutup modal
    bootstrap.Modal.getInstance(document.getElementById('editProfileModal')).hide();
    Swal.fire('Berhasil', 'Profil berhasil diperbarui', 'success');
 );


    document.getElementById('logoutBtn').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin logout?',
            text: "Anda akan keluar dari sistem",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, logout!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke login (hardcode, sesuaikan)
                window.location.href = '/login';
            }
        });
    });

    // Sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        let sidebar = document.querySelector('.sidebar');
        sidebar.style.marginLeft = sidebar.style.marginLeft === '0px' ? '-260px' : '0px';
    });
</script>
<script>
    // Data profil dari localStorage
    let userProfile = JSON.parse(localStorage.getItem('vmedis_profile')) || {
        name: 'Hana Wijaya',
        email: 'hana@vmedis.com',
        avatar: 'https://randomuser.me/api/portraits/women/68.jpg'
    };

    function updateProfileUI() {
        document.getElementById('profileNameDisplay').innerText = userProfile.name;
        document.getElementById('profileNameNav').innerText = userProfile.name;
        document.getElementById('profileAvatarImg').src = userProfile.avatar;
    }

    updateProfileUI();

    // Logout
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin logout?',
            text: 'Anda akan diarahkan ke halaman login.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, logout'
        }).then((result) => {
            if (result.isConfirmed) {
                // Hapus sesi (opsional: hapus data localStorage tertentu)
                // Redirect ke halaman login
                window.location.href = "{{ route('login') }}";
            }
        });
    });

    // Edit Profil - buka modal (pastikan modal edit profil sudah ada di layout)
    document.getElementById('editProfileBtn').addEventListener('click', function(e) {
        e.preventDefault();
        // Isi modal dengan data userProfile lalu tampilkan
        document.getElementById('profileName').value = userProfile.name;
        document.getElementById('profileEmail').value = userProfile.email;
        document.getElementById('profileAvatar').value = userProfile.avatar;
        new bootstrap.Modal(document.getElementById('editProfileModal')).show();
    });

    // Simpan perubahan profil (tombol di modal)
    document.getElementById('saveProfileBtn')?.addEventListener('click', function() {
        const newName = document.getElementById('profileName').value.trim();
        const newEmail = document.getElementById('profileEmail').value.trim();
        const newAvatar = document.getElementById('profileAvatar').value.trim();
        if (!newName) {
            Swal.fire('Error', 'Nama tidak boleh kosong', 'error');
            return;
        }
        userProfile.name = newName;
        userProfile.email = newEmail;
        userProfile.avatar = newAvatar || 'https://randomuser.me/api/portraits/women/68.jpg';
        localStorage.setItem('vmedis_profile', JSON.stringify(userProfile));
        updateProfileUI();
        bootstrap.Modal.getInstance(document.getElementById('editProfileModal')).hide();
        Swal.fire('Berhasil', 'Profil telah diperbarui', 'success');
    });
</script>

@stack('scripts')
</body>
</html>