@extends('layouts.app')

@section('title', 'Kelola Poin')

@section('content')
<h4 class="fw-bold mb-3">⭐ Kelola Poin / Verifikasi</h4>
<div class="row g-4 mb-4" id="statsBox"></div>
<div class="card"><div class="card-header fw-bold">✅ Daftar Verifikasi Poin</div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead class="table-light"><tr><th>Karyawan</th><th>Nama Task</th><th>Tanggal</th><th>File</th><th>Status</th><th>Aksi</th></tr></thead><tbody id="verifikasiTableBody"></tbody></table></div></div></div>

<script>
    let verifikasiData = JSON.parse(localStorage.getItem('vmedis_verifikasi')) || [
        { id:1, karyawan:'Budi Santoso', task:'Membuat komponen navbar', tanggal:'2025-04-25', file:'navbar.zip', status:'Pending' },
        { id:2, karyawan:'Siti Nurhaliza', task:'Desain banner promo', tanggal:'2025-04-24', file:'banner.psd', status:'Pending' },
        { id:3, karyawan:'Rian Firmansyah', task:'Debug API', tanggal:'2025-04-23', file:'log_error.txt', status:'Pending' },
        { id:4, karyawan:'Andi Prasetyo', task:'Testing Modul', tanggal:'2025-04-22', file:'test.pdf', status:'Approved' },
        { id:5, karyawan:'Dewi Lestari', task:'Desain Banner', tanggal:'2025-04-21', file:'banner.zip', status:'Rejected' }
    ];
    function updateStats(){ let p=verifikasiData.filter(v=>v.status==='Pending').length, a=verifikasiData.filter(v=>v.status==='Approved').length, r=verifikasiData.filter(v=>v.status==='Rejected').length; document.getElementById('statsBox').innerHTML=`<div class="col-md-4"><div class="card text-center p-3 border-warning"><div class="d-flex justify-content-between"><div><h1 class="fw-bold text-warning">${p}</h1><span>Pending</span></div><i class="bi bi-hourglass-split fs-1 text-warning"></i></div></div></div><div class="col-md-4"><div class="card text-center p-3 border-success"><div class="d-flex justify-content-between"><div><h1 class="fw-bold text-success">${a}</h1><span>Approved</span></div><i class="bi bi-check-circle fs-1 text-success"></i></div></div></div><div class="col-md-4"><div class="card text-center p-3 border-danger"><div class="d-flex justify-content-between"><div><h1 class="fw-bold text-danger">${r}</h1><span>Rejected</span></div><i class="bi bi-x-circle fs-1 text-danger"></i></div></div></div>`; }
    function renderTable(){ let tbody=document.getElementById('verifikasiTableBody'); tbody.innerHTML=''; verifikasiData.forEach(v=>{ let badge=v.status==='Pending'?'bg-warning text-dark':(v.status==='Approved'?'bg-success':'bg-danger'); let action=''; if(v.status==='Pending') action=`<button class="btn btn-success btn-icon-circle approve-btn" data-id="${v.id}"><i class="bi bi-check-lg"></i></button> <button class="btn btn-danger btn-icon-circle reject-btn" data-id="${v.id}"><i class="bi bi-x-lg"></i></button>`; else action='<span class="text-muted">Sudah diproses</span>'; let row=`<tr><td>${escapeHtml(v.karyawan)}</td><td>${escapeHtml(v.task)}</td><td>${v.tanggal}</td><td><a href="#" class="text-primary" onclick="alert('Download: ${v.file}')">Lihat</a></td><td><span class="badge ${badge}">${v.status}</span></td><td>${action}</td></tr>`; tbody.insertAdjacentHTML('beforeend',row); });
        document.querySelectorAll('.approve-btn').forEach(btn=>btn.addEventListener('click',()=>ubahStatus(btn.dataset.id,'Approved')));
        document.querySelectorAll('.reject-btn').forEach(btn=>btn.addEventListener('click',()=>ubahStatus(btn.dataset.id,'Rejected')));
        updateStats();
    }
    function ubahStatus(id, newStatus){ let item=verifikasiData.find(v=>v.id==id); if(!item) return; Swal.fire({ title:'Konfirmasi', text:`Ubah status task "${item.task}" (${item.karyawan}) menjadi ${newStatus}?`, icon:'question', showCancelButton:true, confirmButtonColor: newStatus==='Approved'?'#28a745':'#dc3545', confirmButtonText:'Ya, ubah' }).then(res=>{ if(res.isConfirmed){ item.status=newStatus; localStorage.setItem('vmedis_verifikasi',JSON.stringify(verifikasiData)); renderTable(); Swal.fire('Berhasil',`Status diubah menjadi ${newStatus}`,'success'); } }); }
    function escapeHtml(str){ if(!str) return ''; return str.replace(/[&<>]/g,function(m){ if(m==='&') return '&amp;'; if(m==='<') return '&lt;'; if(m==='>') return '&gt;'; return m;}); }
    renderTable();
</script>
@endsection