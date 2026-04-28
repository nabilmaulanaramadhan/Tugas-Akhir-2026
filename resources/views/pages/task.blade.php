@extends('layouts.app')

@section('title', 'Task Master')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white fw-bold">📝 Buat / Edit Task</div>
            <div class="card-body">
                <input type="hidden" id="taskId">
                <div class="mb-3"><label>Nama Task</label><input type="text" id="taskName" class="form-control"></div>
                <div class="mb-3"><label>Project</label><select id="taskProject" class="form-select"></select></div>
                <div class="mb-3"><label>Deskripsi</label><textarea id="taskDesc" class="form-control" rows="2"></textarea></div>
                <div class="mb-3"><label>Deadline</label><input type="date" id="taskDeadline" class="form-control"></div>
                <div class="mb-3"><label>Jumlah Poin</label><input type="number" id="taskPoin" class="form-control" value="0"></div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" id="simpanTaskBtn">Simpan Task</button>
                    <button class="btn btn-secondary" id="batalTaskBtn" style="display:none;">Batal</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card"><div class="card-header bg-dark text-white fw-bold">📋 Semua Task</div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead class="table-light"><tr><th>ID</th><th>Nama Task</th><th>Project</th><th>Deskripsi</th><th>Deadline</th><th>Poin</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="taskTableBody"></tbody></table></div></div></div>
    </div>
</div>

<script>
    let tasks = JSON.parse(localStorage.getItem('vmedis_tasks')) || [];
    let projects = JSON.parse(localStorage.getItem('vmedis_projects')) || [];

    function populateProjectSelect() { let select = document.getElementById('taskProject'); select.innerHTML = '<option value="">Pilih Project</option>'; projects.forEach(p => select.innerHTML += `<option value="${escapeHtml(p.name)}">${escapeHtml(p.name)}</option>`); if(projects.length===0) select.innerHTML+='<option value="Tanpa Project">Tanpa Project</option>'; }
    function generateTaskId() { let maxNum=0; tasks.forEach(t=>{let m=t.id.match(/TSK(\d+)/); if(m) maxNum=Math.max(maxNum,parseInt(m[1]));}); return 'TSK'+(maxNum+1).toString().padStart(3,'0'); }
    function renderTaskTable() {
        let tbody = document.getElementById('taskTableBody'); tbody.innerHTML='';
        if(tasks.length===0){ tbody.innerHTML='<tr><td colspan="8" class="text-center">Belum ada task</td></tr>'; return; }
        tasks.forEach(task=>{
            let badgeClass = task.status==='Selesai'?'bg-success':(task.status==='Pending'?'bg-warning text-dark':'bg-info');
            let row=`<tr><td>${escapeHtml(task.id)}</td><td>${escapeHtml(task.name)}</td><td>${escapeHtml(task.project)}</td><td>${escapeHtml(task.deskripsi||'-')}</td><td>${task.deadline}</td><td>${task.poin} Poin</td><td><span class="badge ${badgeClass}">${task.status}</span></td>
            <td><button class="btn btn-sm btn-warning edit-task" data-id="${task.id}"><i class="bi bi-pencil"></i></button> <button class="btn btn-sm btn-danger delete-task" data-id="${task.id}"><i class="bi bi-trash"></i></button></td></tr>`;
            tbody.insertAdjacentHTML('beforeend',row);
        });
        document.querySelectorAll('.edit-task').forEach(btn=>btn.addEventListener('click',()=>editTask(btn.dataset.id)));
        document.querySelectorAll('.delete-task').forEach(btn=>btn.addEventListener('click',()=>deleteTask(btn.dataset.id)));
    }
    function resetForm() { document.getElementById('taskId').value=''; document.getElementById('taskName').value=''; document.getElementById('taskProject').value=''; document.getElementById('taskDesc').value=''; document.getElementById('taskDeadline').value=''; document.getElementById('taskPoin').value='0'; document.getElementById('simpanTaskBtn').innerText='Simpan Task'; document.getElementById('batalTaskBtn').style.display='none'; }
    function editTask(id) { let task=tasks.find(t=>t.id===id); if(task){ document.getElementById('taskId').value=task.id; document.getElementById('taskName').value=task.name; document.getElementById('taskProject').value=task.project; document.getElementById('taskDesc').value=task.deskripsi; document.getElementById('taskDeadline').value=task.deadline; document.getElementById('taskPoin').value=task.poin; document.getElementById('simpanTaskBtn').innerText='Update Task'; document.getElementById('batalTaskBtn').style.display='inline-block'; } }
    function deleteTask(id) { Swal.fire({ title:'Yakin hapus?', text:'Task akan dihapus permanen!', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#3085d6', confirmButtonText:'Ya, hapus!' }).then(result=>{ if(result.isConfirmed){ tasks=tasks.filter(t=>t.id!==id); localStorage.setItem('vmedis_tasks',JSON.stringify(tasks)); renderTaskTable(); resetForm(); Swal.fire('Terhapus!','Task berhasil dihapus.','success'); } }); }
    function saveTask() {
        let id=document.getElementById('taskId').value, name=document.getElementById('taskName').value.trim(), project=document.getElementById('taskProject').value, deskripsi=document.getElementById('taskDesc').value.trim(), deadline=document.getElementById('taskDeadline').value, poin=parseInt(document.getElementById('taskPoin').value);
        if(!name||!project||!deadline||isNaN(poin)){ Swal.fire('Error','Harap isi semua field!','error'); return; }
        if(id){ let idx=tasks.findIndex(t=>t.id===id); if(idx!==-1) tasks[idx]={...tasks[idx],name,project,deskripsi:deskripsi||'-',deadline,poin}; }
        else{ tasks.push({ id:generateTaskId(), name, project, deskripsi:deskripsi||'-', deadline, poin, status:'To do' }); }
        localStorage.setItem('vmedis_tasks',JSON.stringify(tasks)); renderTaskTable(); resetForm(); Swal.fire('Berhasil','Task disimpan.','success');
    }
    function escapeHtml(str){ if(!str) return ''; return str.replace(/[&<>]/g,function(m){ if(m==='&') return '&amp;'; if(m==='<') return '&lt;'; if(m==='>') return '&gt;'; return m;}); }
    document.getElementById('simpanTaskBtn').addEventListener('click',(e)=>{e.preventDefault(); saveTask();});
    document.getElementById('batalTaskBtn').addEventListener('click',()=>resetForm());
    populateProjectSelect(); renderTaskTable();
</script>
@endsection