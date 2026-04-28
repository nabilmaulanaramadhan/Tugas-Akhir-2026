@extends('layouts.employee')
@section('title', 'Dashboard')
@section('content')
<div class="row g-4">
    <div class="col-md-5">
        <div class="card p-4 text-center bg-gradient text-dark" style="background: linear-gradient(135deg, #f0f9ff 0%, #e6f2f9 100%);">
            <h5 class="text-primary"><i class="bi bi-clock-history"></i> Absensi Hari Ini</h5>
            <h2 class="display-3 fw-bold mt-2" id="jamDigital">--:--:--</h2>
            <p id="tanggalDisplay" class="text-muted"></p>
            <button class="btn btn-primary mt-2" id="quickCheckinBtn"><i class="bi bi-check-circle"></i> Check-In Sekarang</button>
        </div>
    </div>
    <div class="col-md-7">
        <div class="row g-4">
            <div class="col-sm-6">
                <div class="card p-3 text-center">
                    <h6 class="text-muted">✅ Task Selesai</h6>
                    <h2 class="fw-bold" id="taskCompletion">0/0</h2>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card p-3 text-center">
                    <h6 class="text-muted">⭐ Poin Bulan Ini</h6>
                    <h2 class="fw-bold" id="poinBulanIni">0</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-white fw-bold"><i class="bi bi-list-check"></i> Task Saya</div>
    <div class="card-body p-0">
        <div id="taskListContainer" class="list-group list-group-flush">
            <div class="list-group-item text-center">Memuat data...</div>
        </div>
    </div>
</div>

<script>
    (function() {
        // Jangan deklarasi ulang window.emp, gunakan yang sudah ada dari layout
        if (!window.emp) {
            console.error('Employee data not found');
            return;
        }
        const nip = window.emp.nip;
        const name = window.emp.name;

        // Update jam dan tanggal
        function updateDateTime() {
            const now = new Date();
            const jamElem = document.getElementById('jamDigital');
            const tglElem = document.getElementById('tanggalDisplay');
            if (jamElem) jamElem.innerText = now.toLocaleTimeString('id-ID');
            if (tglElem) tglElem.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Ambil data dari localStorage
        let tasks = JSON.parse(localStorage.getItem('vmedis_tasks')) || [];
        let verifikasi = JSON.parse(localStorage.getItem('vmedis_verifikasi')) || [];

        // Task milik karyawan
        let myTasks = tasks.filter(t => t.assigned_to === nip);
        let total = myTasks.length;
        let completed = myTasks.filter(t => t.status === 'Selesai').length;
        document.getElementById('taskCompletion').innerHTML = `${completed}/${total}`;

        let poinApproved = verifikasi.filter(v => v.karyawan === name && v.status === 'Approved').reduce((sum, v) => sum + (v.poin || 0), 0);
        document.getElementById('poinBulanIni').innerText = poinApproved;

        // Tampilkan list task (max 3)
        let taskHtml = '';
        if (myTasks.length === 0) {
            taskHtml = '<div class="list-group-item text-center text-muted">Tidak ada task yang sedang dikerjakan</div>';
        } else {
            myTasks.slice(0, 3).forEach(task => {
                taskHtml += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${escapeHtml(task.name)}</strong><br>
                            <small class="text-muted">${task.project} · Deadline ${task.deadline}</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">${task.poin} Poin</span>
                    </div>
                `;
            });
        }
        document.getElementById('taskListContainer').innerHTML = taskHtml;

        // Tombol check-in langsung menuju halaman absensi
        const checkinBtn = document.getElementById('quickCheckinBtn');
        if (checkinBtn) {
            checkinBtn.addEventListener('click', () => {
                window.location.href = "{{ route('employee.absensi') }}";
            });
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
    })();
</script>
@endsection