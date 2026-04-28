@extends('layouts.employee')
@section('title', 'Task')
@section('content')
<h4 class="fw-bold mb-3"><i class="bi bi-inbox"></i> Task Tersedia</h4>
<div id="taskTersediaContainer" class="row g-4 mb-5"></div>

<h4 class="fw-bold mb-3"><i class="bi bi-play-circle"></i> Task Sedang Dikerjakan</h4>
<div id="taskDikerjakanContainer" class="row g-4"></div>

<script>
    let emp = JSON.parse(localStorage.getItem('vmedis_employee_login'));
    let nip = emp.nip;
    let tasks = JSON.parse(localStorage.getItem('vmedis_tasks')) || [];
    let projects = JSON.parse(localStorage.getItem('vmedis_projects')) || [];

    function getProjectLeader(projectName) {
        let proj = projects.find(p => p.name === projectName);
        return proj ? proj.leader : '-';
    }
    function escapeHtml(str) { if (!str) return ''; return str.replace(/[&<>]/g, m => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;' }[m])); }

    let todoTasks = tasks.filter(t => t.status === 'To do' && !t.assigned_to);
    let myTasks = tasks.filter(t => t.assigned_to === nip && t.status !== 'Selesai');

    function renderTersedia() {
        let container = document.getElementById('taskTersediaContainer');
        if (todoTasks.length === 0) { container.innerHTML = '<div class="col-12"><div class="alert alert-secondary text-center">Tidak ada task tersedia</div></div>'; return; }
        let html = '';
        todoTasks.forEach(task => {
            html += `<div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between"><h5 class="fw-bold">${escapeHtml(task.name)}</h5><span class="badge bg-secondary">${task.id}</span></div>
                        <p class="small text-muted mt-2">${escapeHtml(task.deskripsi || '-')}</p>
                        <div><i class="bi bi-folder"></i> ${escapeHtml(task.project)}</div>
                        <div><i class="bi bi-calendar"></i> Deadline: ${task.deadline}</div>
                        <button class="btn btn-sm btn-success w-100 mt-3 claim-task" data-id="${task.id}"><i class="bi bi-hand-index-thumb"></i> Claim Task</button>
                    </div>
                </div>
            </div>`;
        });
        container.innerHTML = html;
        document.querySelectorAll('.claim-task').forEach(btn => btn.addEventListener('click', claimHandler));
    }

    function renderDikerjakan() {
        let container = document.getElementById('taskDikerjakanContainer');
        if (myTasks.length === 0) { container.innerHTML = '<div class="col-12"><div class="alert alert-secondary text-center">Tidak ada task dalam pengerjaan</div></div>'; return; }
        let html = '';
        myTasks.forEach(task => {
            html += `<div class="col-md-6 col-lg-4">
                <div class="card h-100 border-warning shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between"><h5 class="fw-bold">${escapeHtml(task.name)}</h5><span class="badge bg-warning text-dark">On Progress</span></div>
                        <p class="small text-muted mt-2">${escapeHtml(task.deskripsi || '-')}</p>
                        <div><i class="bi bi-person"></i> Leader: ${escapeHtml(getProjectLeader(task.project))}</div>
                        <div><i class="bi bi-calendar"></i> Deadline: ${task.deadline}</div>
                        <button class="btn btn-sm btn-primary w-100 mt-3 complete-task" data-id="${task.id}"><i class="bi bi-check2-square"></i> Selesaikan & Ajukan</button>
                    </div>
                </div>
            </div>`;
        });
        container.innerHTML = html;
        document.querySelectorAll('.complete-task').forEach(btn => btn.addEventListener('click', completeHandler));
    }

    async function claimHandler(e) {
        let id = e.currentTarget.dataset.id;
        let result = await Swal.fire({ title: 'Claim Task', text: 'Ambil task ini?', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Claim' });
        if (result.isConfirmed) {
            let idx = tasks.findIndex(t => t.id === id);
            if (idx !== -1) {
                tasks[idx].status = 'On Progress';
                tasks[idx].assigned_to = nip;
                localStorage.setItem('vmedis_tasks', JSON.stringify(tasks));
                Swal.fire('Berhasil', 'Task berhasil di-claim', 'success');
                location.reload();
            }
        }
    }

    async function completeHandler(e) {
        let id = e.currentTarget.dataset.id;
        let result = await Swal.fire({ title: 'Selesaikan Task', text: 'Ajukan verifikasi poin ke HRD?', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Ajukan' });
        if (result.isConfirmed) {
            let idx = tasks.findIndex(t => t.id === id);
            if (idx !== -1) {
                let task = tasks[idx];
                task.status = 'Pending';
                localStorage.setItem('vmedis_tasks', JSON.stringify(tasks));
                let verif = JSON.parse(localStorage.getItem('vmedis_verifikasi')) || [];
                verif.push({ id: Date.now(), karyawan: emp.name, task: task.name, tanggal: new Date().toISOString().slice(0,10), file: 'submitted.zip', status: 'Pending', poin: task.poin });
                localStorage.setItem('vmedis_verifikasi', JSON.stringify(verif));
                Swal.fire('Berhasil', 'Task diajukan, tunggu verifikasi HRD', 'success');
                location.reload();
            }
        }
    }

    renderTersedia();
    renderDikerjakan();
</script>
@endsection