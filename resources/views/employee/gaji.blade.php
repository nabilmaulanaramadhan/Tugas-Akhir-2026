@extends('layouts.employee')
@section('title', 'Gaji Saya')
@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm h-100">
            <h5 class="text-primary"><i class="bi bi-wallet2"></i> Pembayaran Bulan Ini</h5>
            <h2 class="display-5 fw-bold" id="totalGajiDisplay">Rp 0</h2>
            <p class="text-muted" id="periodeDisplay"></p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4 shadow-sm h-100">
            <h5><i class="bi bi-graph-up"></i> Pendapatan Detail</h5>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Gaji Pokok</span><strong id="gajiPokokDisplay">Rp 0</strong></div>
            <div class="d-flex justify-content-between border-bottom py-2"><span>Bonus Poin</span><strong id="bonusDisplay">Rp 0</strong></div>
            <div class="d-flex justify-content-between pt-2 fw-bold fs-5"><span>Total</span><strong id="totalPendapatanDisplay">Rp 0</strong></div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-white fw-bold"><i class="bi bi-clock-history"></i> History Gaji</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Periode</th><th>Gaji Pokok</th><th>Bonus</th><th>Total</th><th>Status</th></tr></thead>
                <tbody id="historyGajiBody"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let emp = JSON.parse(localStorage.getItem('vmedis_employee_login'));
    let verifikasi = JSON.parse(localStorage.getItem('vmedis_verifikasi')) || [];
    let poinApproved = verifikasi.filter(v => v.karyawan === emp.name && v.status === 'Approved').reduce((s,v) => s + (v.poin||0), 0);
    const rate = 10000;
    let gajiPokok = 3000000;
    let bonus = poinApproved * rate;
    let total = gajiPokok + bonus;
    function rp(x) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(x); }
    document.getElementById('gajiPokokDisplay').innerText = rp(gajiPokok);
    document.getElementById('bonusDisplay').innerText = rp(bonus);
    document.getElementById('totalGajiDisplay').innerText = rp(total);
    document.getElementById('totalPendapatanDisplay').innerText = rp(total);
    document.getElementById('periodeDisplay').innerText = new Date().toLocaleString('id-ID', { month: 'long', year: 'numeric' });

    let historyGaji = JSON.parse(localStorage.getItem(`vmedis_gaji_emp_${emp.nip}`));
    if (!historyGaji) {
        historyGaji = [
            { periode: 'Januari 2026', gajiPokok: 3000000, bonus: 500000, total: 3500000, status: 'Sudah Dibayar' },
            { periode: 'Februari 2026', gajiPokok: 3000000, bonus: 1500000, total: 4500000, status: 'Sudah Dibayar' },
            { periode: 'Maret 2026', gajiPokok: 3000000, bonus: 1000000, total: 4000000, status: 'Sudah Dibayar' }
        ];
        localStorage.setItem(`vmedis_gaji_emp_${emp.nip}`, JSON.stringify(historyGaji));
    }
    let rows = '';
    historyGaji.forEach(h => {
        rows += `<tr><td>${h.periode}</td><td>${rp(h.gajiPokok)}</td><td>${rp(h.bonus)}</td><td class="fw-bold text-primary">${rp(h.total)}</td><td><span class="badge bg-success">${h.status}</span></td></tr>`;
    });
    document.getElementById('historyGajiBody').innerHTML = rows;
</script>
@endsection