@extends('layouts.employee')
@section('title', 'Absensi')
@section('content')
<div class="row g-4">
    <!-- Check-in Card -->
    <div class="col-md-5">
        <div class="card text-center p-4 shadow-sm">
            <h5><i class="bi bi-fingerprint"></i> Absen Hari Ini</h5>
            <h2 class="display-3 fw-bold text-primary" id="jamAbsen">--:--:--</h2>
            <p class="text-muted" id="tanggalAbsen"></p>
            <button class="btn btn-success btn-lg" id="checkinBtn"><i class="bi bi-check2-circle"></i> Check-In</button>
        </div>
    </div>
    <!-- Ringkasan -->
    <div class="col-md-7">
        <div class="row g-4">
            <div class="col-sm-4"><div class="card p-3 text-center"><h6>Minggu Ini</h6><h3 class="fw-bold" id="mingguJam">0 Jam</h3></div></div>
            <div class="col-sm-4"><div class="card p-3 text-center"><h6>Bulan Ini</h6><h3 class="fw-bold" id="bulanJam">0 Jam</h3></div></div>
            <div class="col-sm-4"><div class="card p-3 text-center"><h6>Rate Kehadiran</h6><h3 class="fw-bold" id="rateKehadiran">0%</h3></div></div>
        </div>
    </div>
</div>

<!-- History Absensi -->
<div class="card mt-4">
    <div class="card-header bg-white fw-bold"><i class="bi bi-clock-history"></i> History Absensi</div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush" id="historyList"></ul>
    </div>
</div>

<script>
    let emp = JSON.parse(localStorage.getItem('vmedis_employee_login'));
    let nip = emp.nip;
    let history = JSON.parse(localStorage.getItem(`vmedis_absensi_${nip}`)) || [];
    let now = new Date();
    let bulanIni = now.getMonth();
    let tahunIni = now.getFullYear();
    let mingguIni = () => { /* sederhana, ambil data 7 hari terakhir */ return history.filter(h => { let tgl = new Date(h.tanggal); return (now - tgl) <= 7*24*3600000; }).reduce((s,h) => s + (h.jam||0),0); };
    let totalJamBulan = history.filter(h => { let tgl = new Date(h.tanggal); return tgl.getMonth() === bulanIni && tgl.getFullYear() === tahunIni; }).reduce((s,h) => s + (h.jam||0),0);
    let totalJamMinggu = mingguIni();
    let rate = Math.round((totalJamBulan / (22*8)) * 100);
    document.getElementById('mingguJam').innerText = totalJamMinggu + ' Jam';
    document.getElementById('bulanJam').innerText = totalJamBulan + ' Jam';
    document.getElementById('rateKehadiran').innerText = rate + '%';
    document.getElementById('tanggalAbsen').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    setInterval(() => { document.getElementById('jamAbsen').innerText = new Date().toLocaleTimeString('id-ID'); }, 1000);

    let historyHtml = '';
    if (history.length === 0) historyHtml = '<li class="list-group-item text-center">Belum ada riwayat absensi</li>';
    else {
        history.slice().reverse().forEach(h => {
            let statusClass = h.status === 'On Time' ? 'success' : 'warning';
            historyHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                <div><i class="bi bi-calendar-check"></i> ${h.tanggal}</div>
                <div><span class="badge bg-${statusClass}">${h.status}</span> <span class="ms-2">${h.jam} Jam</span></div>
            </li>`;
        });
    }
    document.getElementById('historyList').innerHTML = historyHtml;

    document.getElementById('checkinBtn').addEventListener('click', () => {
        let today = new Date().toISOString().slice(0,10);
        if (history.some(h => h.tanggal === today)) {
            Swal.fire('Info', 'Anda sudah check-in hari ini', 'info');
            return;
        }
        Swal.fire({
            title: 'Check-in',
            text: 'Apakah Anda siap memulai kerja?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Check-in'
        }).then(res => {
            if (res.isConfirmed) {
                let newHistory = [...history, { tanggal: today, jam: 8, status: 'On Time' }];
                localStorage.setItem(`vmedis_absensi_${nip}`, JSON.stringify(newHistory));
                Swal.fire('Berhasil', 'Check-in tercatat!', 'success');
                location.reload();
            }
        });
    });
</script>
@endsection