@extends('layouts.employee')
@section('title', 'Poin Saya')
@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card bg-primary text-white p-4 text-center">
            <h5><i class="bi bi-star-fill"></i> Total Poin Diterima</h5>
            <h2 class="display-4 fw-bold" id="totalPoin">0</h2>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white p-4 text-center">
            <h5><i class="bi bi-calculator"></i> Rate per Poin</h5>
            <h2 class="display-4 fw-bold">Rp 10.000</h2>
            <small>/ Poin</small>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-bold"><i class="bi bi-table"></i> History Poin Saya</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Task ID</th><th>Nama Task</th><th>Project</th><th>Submit</th><th>Poin</th><th>Status</th></tr></thead>
                <tbody id="historiPoinBody"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let emp = JSON.parse(localStorage.getItem('vmedis_employee_login'));
    let verifikasi = JSON.parse(localStorage.getItem('vmedis_verifikasi')) || [];
    let tasks = JSON.parse(localStorage.getItem('vmedis_tasks')) || [];
    let poinSaya = verifikasi.filter(v => v.karyawan === emp.name);
    let totalPoin = poinSaya.filter(v => v.status === 'Approved').reduce((s,v) => s + (v.poin||0), 0);
    document.getElementById('totalPoin').innerText = totalPoin;

    let tbody = document.getElementById('historiPoinBody');
    if (poinSaya.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Belum ada riwayat poin</td></tr>';
    } else {
        let rows = '';
        poinSaya.forEach(v => {
            let taskObj = tasks.find(t => t.name === v.task);
            let taskId = taskObj ? taskObj.id : '-';
            let badgeClass = v.status === 'Approved' ? 'bg-success' : (v.status === 'Rejected' ? 'bg-danger' : 'bg-warning');
            rows += `<tr>
                <td>${taskId}</td>
                <td>${escapeHtml(v.task)}</td>
                <td>${taskObj ? taskObj.project : '-'}</td>
                <td>${v.tanggal}</td>
                <td>${v.poin || 0}</td>
                <td><span class="badge ${badgeClass}">${v.status}</span></td>
            </tr>`;
        });
        tbody.innerHTML = rows;
    }
    function escapeHtml(str) { if (!str) return ''; return str.replace(/[&<>]/g, m => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;' }[m])); }
</script>
@endsection