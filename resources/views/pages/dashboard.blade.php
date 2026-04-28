@extends('layouts.app')

@section('title', 'Dashboard HRD')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">👋 Selamat Datang, Hana Wijaya</h3>
</div>

<div class="row g-4 mb-5" id="statsCards">
    <!-- Statistik akan diisi JavaScript secara dinamis -->
</div>

<h5 class="fw-semibold mb-3">📌 Aktivitas Terakhir</h5>
<div class="card p-3" id="aktivitasContainer">
    <!-- Aktivitas akan diisi JavaScript -->
</div>

<script>
    // Fungsi ambil data dari localStorage (sesuai key yang sudah digunakan)
    function getKaryawan() {
        return JSON.parse(localStorage.getItem('vmedis_karyawan')) || [];
    }

    function getProjects() {
        return JSON.parse(localStorage.getItem('vmedis_projects')) || [];
    }

    function getTasks() {
        return JSON.parse(localStorage.getItem('vmedis_tasks')) || [];
    }

    function getVerifikasi() {
        return JSON.parse(localStorage.getItem('vmedis_verifikasi')) || [];
    }

    // 1. Total karyawan aktif (status 'Active')
    function getTotalKaryawan() {
        const karyawan = getKaryawan();
        return karyawan.filter(k => k.status === 'Active').length;
    }

    // 2. Task aktif: semua task yang belum selesai (status bukan 'Selesai')
    function getTaskAktif() {
        const tasks = getTasks();
        return tasks.filter(t => t.status !== 'Selesai').length;
    }

    // 3. Project jalan: project yang progressnya < 100%
    function getProjectJalan() {
        const projects = getProjects();
        const tasks = getTasks();
        let jalan = 0;
        for (const proj of projects) {
            const relatedTasks = tasks.filter(t => t.project === proj.name);
            const total = relatedTasks.length;
            if (total === 0) {
                // project tanpa task dianggap belum jalan? atau 0% progress -> jalan
                jalan++;
            } else {
                const completed = relatedTasks.filter(t => t.status === 'Selesai').length;
                const progress = (completed / total) * 100;
                if (progress < 100) jalan++;
            }
        }
        return jalan;
    }

    // 4. Review task: verifikasi poin yang masih 'Pending'
    function getReviewTask() {
        const verifikasi = getVerifikasi();
        return verifikasi.filter(v => v.status === 'Pending').length;
    }

    // Aktivitas terakhir: ambil beberapa kejadian dari data terbaru
    function getAktivitas() {
        let aktivitas = [];
        const karyawan = getKaryawan();
        const tasks = getTasks();
        const projects = getProjects();
        const verifikasi = getVerifikasi();

        // Jika ada karyawan baru (asumsikan yang terakhir ditambahkan)
        if (karyawan.length > 0) {
            const lastKaryawan = karyawan[karyawan.length - 1];
            aktivitas.push({
                waktu: 'Baru saja',
                aksi: `Karyawan baru: ${lastKaryawan.nama} (${lastKaryawan.nip}) ditambahkan`
            });
        }
        // Jika ada task terakhir
        if (tasks.length > 0) {
            const lastTask = tasks[tasks.length - 1];
            aktivitas.push({
                waktu: 'Hari ini',
                aksi: `Task baru: "${lastTask.name}" - ${lastTask.project}`
            });
        }
        // Jika ada verifikasi pending
        const pendingCount = verifikasi.filter(v => v.status === 'Pending').length;
        if (pendingCount > 0) {
            aktivitas.push({
                waktu: 'Sekarang',
                aksi: `${pendingCount} verifikasi poin menunggu persetujuan`
            });
        }
        // Jika ada project dengan progress 100%
        const projectsDone = projects.filter(p => {
            const related = tasks.filter(t => t.project === p.name);
            const total = related.length;
            if (total === 0) return false;
            const completed = related.filter(t => t.status === 'Selesai').length;
            return completed === total;
        });
        if (projectsDone.length > 0) {
            aktivitas.push({
                waktu: 'Hari ini',
                aksi: `${projectsDone.length} project telah selesai 100%`
            });
        }

        if (aktivitas.length === 0) {
            aktivitas.push({ waktu: 'Sekarang', aksi: 'Belum ada aktivitas terbaru' });
        }
        return aktivitas.slice(0, 5);
    }

    // Render semua statistik dan aktivitas
    function renderDashboard() {
        const totalKaryawan = getTotalKaryawan();
        const taskAktif = getTaskAktif();
        const projectJalan = getProjectJalan();
        const reviewTask = getReviewTask();

        const statsHtml = `
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div><h1 class="fw-bold">${totalKaryawan}</h1><span>Total Karyawan</span></div>
                        <i class="bi bi-people fs-1 text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div><h1 class="fw-bold">${taskAktif}</h1><span>Task Aktif</span></div>
                        <i class="bi bi-check-lg fs-1 text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div><h1 class="fw-bold">${projectJalan}</h1><span>Project Jalan</span></div>
                        <i class="bi bi-folder-symlink fs-1 text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div><h1 class="fw-bold">${reviewTask}</h1><span>Review Task</span></div>
                        <i class="bi bi-chat-dots fs-1 text-primary"></i>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('statsCards').innerHTML = statsHtml;

        const aktivitas = getAktivitas();
        let aktivitasHtml = '';
        aktivitas.forEach(act => {
            aktivitasHtml += `
                <div class="aktivitas-item">
                    <div class="d-flex">
                        <span class="dot-blue"></span>
                        <div>
                            <div class="fw-semibold">${escapeHtml(act.aksi)}</div>
                            <small class="text-muted">${escapeHtml(act.waktu)}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        document.getElementById('aktivitasContainer').innerHTML = aktivitasHtml;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Render awal
    renderDashboard();

    // Refresh setiap 3 detik untuk menangkap perubahan dari tab lain (CRUD)
    setInterval(renderDashboard, 3000);
</script>
@endsection