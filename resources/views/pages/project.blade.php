@extends('layouts.app')

@section('title', 'Project Master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">📁 Manajemen Project</h4>
    <button class="btn btn-primary" id="tambahProjectBtn">
        <i class="bi bi-plus-circle"></i> Tambah Project
    </button>
</div>

<div class="row g-4" id="projectGrid"></div>

<!-- Modal Form Project -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectModalTitle">Tambah Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="projectId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Project</label>
                    <input type="text" id="projectName" class="form-control" placeholder="Contoh: VMEDIS Website">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID Project (otomatis)</label>
                    <input type="text" id="projectCode" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea id="projectDesc" class="form-control" rows="3" placeholder="Deskripsi project..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deadline</label>
                    <input type="date" id="projectDeadline" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Project Leader</label>
                    <input type="text" id="projectLeader" class="form-control" placeholder="Nama Leader">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveProjectBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    let projects = JSON.parse(localStorage.getItem('vmedis_projects')) || [];
    let tasks = JSON.parse(localStorage.getItem('vmedis_tasks')) || [];

    function generateProjectCode() {
        let maxNum = 0;
        projects.forEach(p => {
            let match = p.code.match(/PRJ(\d+)/);
            if (match) maxNum = Math.max(maxNum, parseInt(match[1]));
        });
        return 'PRJ' + (maxNum + 1).toString().padStart(3, '0');
    }

    function calculateProgress(projectName) {
        let projectTasks = tasks.filter(t => t.project === projectName);
        if (projectTasks.length === 0) return 0;
        let completed = projectTasks.filter(t => t.status === 'Selesai').length;
        return Math.round((completed / projectTasks.length) * 100);
    }

    function renderProjectGrid() {
        const grid = document.getElementById('projectGrid');
        grid.innerHTML = '';
        if (projects.length === 0) {
            grid.innerHTML = '<div class="col-12"><div class="alert alert-info">Belum ada project. Klik "Tambah Project".</div></div>';
            return;
        }
        projects.forEach(proj => {
            const progress = calculateProgress(proj.name);
            const card = `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div><h5 class="fw-bold">${escapeHtml(proj.name)}</h5><small class="text-muted">${proj.code}</small></div>
                                <div>
                                    <button class="btn btn-sm btn-outline-warning edit-project" data-id="${proj.id}"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger delete-project" data-id="${proj.id}"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                            <p class="mt-2">${escapeHtml(proj.deskripsi)}</p>
                            <div class="mt-3"><div class="d-flex justify-content-between small"><span>Progress</span><span>${progress}%</span></div>
                            <div class="progress"><div class="progress-bar bg-primary" style="width: ${progress}%"></div></div></div>
                            <div class="mt-3 d-flex justify-content-between"><span><i class="bi bi-calendar"></i> Deadline: ${proj.deadline}</span><span><i class="bi bi-person"></i> ${escapeHtml(proj.leader)}</span></div>
                        </div>
                    </div>
                </div>
            `;
            grid.insertAdjacentHTML('beforeend', card);
        });
        // Attach event
        document.querySelectorAll('.edit-project').forEach(btn => btn.addEventListener('click', () => editProject(btn.dataset.id)));
        document.querySelectorAll('.delete-project').forEach(btn => btn.addEventListener('click', () => deleteProject(btn.dataset.id)));
    }

    function editProject(id) {
        const proj = projects.find(p => p.id == id);
        if (proj) {
            document.getElementById('projectId').value = proj.id;
            document.getElementById('projectName').value = proj.name;
            document.getElementById('projectCode').value = proj.code;
            document.getElementById('projectDesc').value = proj.deskripsi;
            document.getElementById('projectDeadline').value = proj.deadline;
            document.getElementById('projectLeader').value = proj.leader;
            document.getElementById('projectModalTitle').innerText = 'Edit Project';
            new bootstrap.Modal(document.getElementById('projectModal')).show();
        }
    }

    function deleteProject(id) {
        Swal.fire({
            title: 'Yakin hapus project?',
            text: "Data project akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                projects = projects.filter(p => p.id != id);
                localStorage.setItem('vmedis_projects', JSON.stringify(projects));
                renderProjectGrid();
                Swal.fire('Terhapus!', 'Project berhasil dihapus.', 'success');
            }
        });
    }

    function saveProject() {
        const id = document.getElementById('projectId').value;
        const name = document.getElementById('projectName').value.trim();
        const code = document.getElementById('projectCode').value.trim();
        const deskripsi = document.getElementById('projectDesc').value.trim();
        const deadline = document.getElementById('projectDeadline').value;
        const leader = document.getElementById('projectLeader').value.trim();

        if (!name || !deadline || !leader) {
            Swal.fire('Error', 'Harap isi Nama Project, Deadline, dan Project Leader!', 'error');
            return;
        }

        if (id) {
            const index = projects.findIndex(p => p.id == id);
            if (index !== -1) {
                projects[index] = { ...projects[index], name, code, deskripsi: deskripsi || '-', deadline, leader };
            }
        } else {
            projects.push({ id: Date.now().toString(), name, code: generateProjectCode(), deskripsi: deskripsi || '-', deadline, leader });
        }
        localStorage.setItem('vmedis_projects', JSON.stringify(projects));
        renderProjectGrid();
        bootstrap.Modal.getInstance(document.getElementById('projectModal')).hide();
        Swal.fire('Berhasil', 'Project berhasil disimpan.', 'success');
    }

    function resetForm() {
        document.getElementById('projectId').value = '';
        document.getElementById('projectName').value = '';
        document.getElementById('projectCode').value = generateProjectCode();
        document.getElementById('projectDesc').value = '';
        document.getElementById('projectDeadline').value = '';
        document.getElementById('projectLeader').value = '';
        document.getElementById('projectModalTitle').innerText = 'Tambah Project';
    }

    function escapeHtml(str) { return str.replace(/[&<>]/g, function(m){ if(m==='&') return '&amp;'; if(m==='<') return '&lt;'; if(m==='>') return '&gt;'; return m; }); }

    document.getElementById('tambahProjectBtn').addEventListener('click', () => { resetForm(); new bootstrap.Modal(document.getElementById('projectModal')).show(); });
    document.getElementById('saveProjectBtn').addEventListener('click', (e) => { e.preventDefault(); saveProject(); });
    renderProjectGrid();
</script>
@endsection