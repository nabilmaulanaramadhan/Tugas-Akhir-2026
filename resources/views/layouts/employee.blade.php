<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VMEDIS | @yield('title', 'Karyawan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background-color: #f8f9fc; color: #000; overflow-x: hidden; }
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
        .main-content { margin-left: 260px; }
        .navbar-custom {
            background: white;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .profile-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; cursor: pointer; }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .btn {
            border-radius: 40px;
            padding: 0.5rem 1.2rem;
            font-weight: 500;
        }
        .btn-sm { border-radius: 30px; padding: 0.25rem 0.8rem; }
        .table > :not(caption) > * > * {
            border-bottom: 1px solid #dee2e6;
            color: #000;
            vertical-align: middle;
        }
        .table thead th { background: #f8f9fa; font-weight: 600; }
        .dropdown-menu { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; }
        @media (max-width: 768px) {
            .sidebar { margin-left: -260px; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="sidebar">
    <div class="brand">📊 VMEDIS</div>
    <ul class="nav flex-column mt-3">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('employee.absensi') ? 'active' : '' }}" href="{{ route('employee.absensi') }}"><i class="bi bi-calendar-check"></i> Absensi</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('employee.task') ? 'active' : '' }}" href="{{ route('employee.task') }}"><i class="bi bi-check2-square"></i> Task</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('employee.poin') ? 'active' : '' }}" href="{{ route('employee.poin') }}"><i class="bi bi-star"></i> Kelola Poin</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('employee.gaji') ? 'active' : '' }}" href="{{ route('employee.gaji') }}"><i class="bi bi-calculator"></i> Gaji</a></li>
        <li class="nav-item"><a class="nav-link" href="#" id="empPengaturan"><i class="bi bi-gear"></i> Pengaturan</a></li>
    </ul>
</div>
<div class="main-content">
    <nav class="navbar navbar-custom d-flex justify-content-between align-items-center">
        <div class="fw-semibold text-dark" id="welcomeMessage">Selamat datang, <span id="empName">Karyawan</span></div>
        <div class="d-flex gap-3 align-items-center">
            <i class="bi bi-bell fs-5 text-dark" style="cursor: pointer;" onclick="Swal.fire('Info', 'Notifikasi akan segera hadir', 'info')"></i>
            <div class="dropdown">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" class="profile-img" data-bs-toggle="dropdown" aria-expanded="false">
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" id="empProfile"><i class="bi bi-person-circle"></i> Profil Saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" id="empLogout"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid p-4">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Hindari deklarasi ulang dengan window.emp
    if (!window.emp) {
        window.emp = JSON.parse(localStorage.getItem('vmedis_employee_login'));
    }
    if (!window.emp) {
        window.location.href = "/login";
    }

    // Tampilkan nama di navbar
    const empNameSpan = document.getElementById('empName');
    const welcomeMsg = document.getElementById('welcomeMessage');
    if (empNameSpan) empNameSpan.innerText = window.emp.name;
    if (welcomeMsg) welcomeMsg.innerHTML = `Selamat datang, <span id="empName">${window.emp.name}</span>`;

    // Logout
    const logoutBtn = document.getElementById('empLogout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Logout',
                text: 'Yakin ingin keluar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, keluar'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('vmedis_employee_login');
                    window.location.href = "/login";
                }
            });
        });
    }

    // Profil & Pengaturan
    const profileBtn = document.getElementById('empProfile');
    if (profileBtn) {
        profileBtn.addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire('Profil', `Nama: ${window.emp.name}\nNIP: ${window.emp.nip}`, 'info');
        });
    }

    const pengaturanBtn = document.getElementById('empPengaturan');
    if (pengaturanBtn) {
        pengaturanBtn.addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire('Info', 'Fitur pengaturan akan segera hadir', 'info');
        });
    }
</script>
@stack('scripts')
</body>
</html>