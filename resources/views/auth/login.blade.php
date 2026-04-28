<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PT VMEDIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f0f2f5; height: 100vh; overflow: hidden; }
        .split-left { background: #0a58ca; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .split-right { background: white; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 440px; padding: 2rem; }
        .nav-pills .nav-link { color: #000; font-weight: 500; }
        .nav-pills .nav-link.active { background-color: #0a58ca; color: white; }
        .form-control, .btn { border-radius: 10px; padding: 0.75rem 1rem; }
        .btn-primary { background: #0a58ca; border: none; font-weight: 600; }
        .toggle-password { cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #6c757d; }
        .brand-logo { font-size: 3rem; font-weight: 800; }
        @media (max-width: 768px) { .split-left { display: none; } .split-right { width: 100%; } }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-lg-6 split-left">
            <div class="text-center px-4">
                <div class="brand-logo mb-4">VMEDIS</div>
                <h2 class="fw-bold">Welcome to VMEDIS Portal</h2>
                <p class="lead mt-3">Solusi lengkap pengelolaan tim <br> Programmer & Marketing berbasis poin</p>
                <hr class="w-25 mx-auto my-4 bg-white">
                <p class="small">Efisien, Transparan, Profesional</p>
            </div>
        </div>
        <div class="col-lg-6 split-right">
            <div class="login-card">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="color: #0a58ca;">Sign In</h3>
                    <p class="text-muted">Masuk ke akun Anda</p>
                </div>
                <ul class="nav nav-pills justify-content-center mb-4" id="roleTab" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-role="hrd" data-bs-toggle="pill" data-bs-target="#hrd" type="button">HRD Admin</button></li>
                    <li class="nav-item"><button class="nav-link" data-role="employee" data-bs-toggle="pill" data-bs-target="#employee" type="button">Employee</button></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="hrd">
                        <form id="loginFormHrd">
                            @csrf
                            <div class="mb-3"><label class="form-label fw-semibold">Email</label><input type="email" id="hrdEmail" class="form-control" value="hrd@vmedis.com"></div>
                            <div class="mb-3 position-relative"><label class="form-label fw-semibold">Password</label><input type="password" id="hrdPassword" class="form-control"><i class="bi bi-eye-slash toggle-password" onclick="togglePassword('hrdPassword')"></i></div>
                            <button type="submit" class="btn btn-primary w-100">Sign In →</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="employee">
                        <form id="loginFormEmp">
                            @csrf
                            <div class="mb-3"><label class="form-label fw-semibold">NIP</label><input type="text" id="empNip" class="form-control" placeholder="Contoh: 198512342025001"></div>
                            <div class="mb-3 position-relative"><label class="form-label fw-semibold">Password</label><input type="password" id="empPassword" class="form-control"><i class="bi bi-eye-slash toggle-password" onclick="togglePassword('empPassword')"></i></div>
                            <button type="submit" class="btn btn-primary w-100">Sign In →</button>
                        </form>
                    </div>
                </div>
                <p class="text-center text-muted mt-4 small">© 2025 PT VMEDIS</p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePassword(id) {
        let input = document.getElementById(id);
        let icon = input.nextElementSibling;
        if (input.type === "password") { input.type = "text"; icon.classList.remove("bi-eye-slash"); icon.classList.add("bi-eye"); }
        else { input.type = "password"; icon.classList.remove("bi-eye"); icon.classList.add("bi-eye-slash"); }
    }

    // HRD Login
    document.getElementById('loginFormHrd')?.addEventListener('submit', function(e) {
        e.preventDefault();
        window.location.href = "{{ route('dashboard') }}";
    });

    // Employee Login
    document.getElementById('loginFormEmp')?.addEventListener('submit', function(e) {
        e.preventDefault();
        let nip = document.getElementById('empNip').value;
        let karyawan = JSON.parse(localStorage.getItem('vmedis_karyawan')) || [];
        let found = karyawan.find(k => k.nip === nip);
        if (found) {
            localStorage.setItem('vmedis_employee_login', JSON.stringify({ nip: found.nip, name: found.nama }));
            window.location.href = "{{ route('employee.dashboard') }}";
        } else {
            Swal.fire('Error', 'NIP tidak terdaftar', 'error');
        }
    });
</script>
</body>
</html>