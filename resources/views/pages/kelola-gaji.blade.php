@extends('layouts.app')

@section('title', 'Kelola Gaji')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">💰 Kelola Gaji Karyawan</h4>
    <button class="btn btn-outline-success" id="exportBtn"><i class="bi bi-file-earmark-excel"></i> Export</button>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">⭐ Rate Per Poin</h5>
                <p class="display-6 fw-bold mb-0">Rp 10.000</p>
                <small>Setiap 1 poin = Rp 10.000</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-white bg-primary">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="card-title">💰 Total Pengeluaran Gaji</h5>
                    <p class="display-6 fw-bold mb-0" id="totalGajiDisplay">Rp 0</p>
                    <small>Periode: <span id="periodeDisplay">-</span></small>
                </div>
                <div>
                    <label class="form-label text-white small mb-1">Filter Bulan</label>
                    <input type="month" id="filterBulan" class="form-control form-control-sm bg-white" value="2025-04">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-table"></i> Daftar Gaji Karyawan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Gaji Pokok</th>
                        <th>Total Poin</th>
                        <th>Bonus (Poin × 10K)</th>
                        <th>Total Gaji</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="gajiTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        min-width: 100px;
    }
    .status-paid {
        background-color: #10b981;
        color: white;
    }
    .status-pending {
        background-color: #f59e0b;
        color: #000;
    }
    .status-select {
        border: none;
        border-radius: 20px;
        padding: 0.35rem 0.75rem;
        font-weight: 600;
        font-size: 0.75rem;
        cursor: pointer;
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 12px;
        appearance: none;
        -webkit-appearance: none;
        width: auto;
        min-width: 100px;
        text-align: center;
    }
    .status-select.paid-off {
        background-color: #10b981;
        color: white;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>");
    }
    .status-select.pending {
        background-color: #f59e0b;
        color: black;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>");
    }
    .status-select option {
        background-color: white;
        color: black;
    }
</style>

<script>
    let allGaji = JSON.parse(localStorage.getItem('vmedis_gaji')) || [
        { nip:'198512342025001', nama:'Andi Prasetyo', gajiPokok:8000000, poin:45, bonus:450000, total:8450000, periode:'2025-04', status:'Pending' },
        { nip:'199003212025002', nama:'Budi Santoso', gajiPokok:7500000, poin:72, bonus:720000, total:8220000, periode:'2025-04', status:'Paid Off' },
        { nip:'199512152025003', nama:'Citra Dewi', gajiPokok:9000000, poin:110, bonus:1100000, total:10100000, periode:'2025-04', status:'Pending' },
        { nip:'198811232025004', nama:'Dian Kurnia', gajiPokok:6000000, poin:28, bonus:280000, total:6280000, periode:'2025-04', status:'Paid Off' }
    ];

    function formatRp(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }

    function renderTable(month) {
        let tbody = document.getElementById('gajiTableBody');
        tbody.innerHTML = '';
        let filtered = allGaji.filter(g => g.periode === month);
        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Tidak ada data gaji untuk periode ini</td></tr>';
            document.getElementById('totalGajiDisplay').innerText = formatRp(0);
            document.getElementById('periodeDisplay').innerText = month;
            return;
        }
        let totalSemua = 0;
        filtered.forEach(g => {
            totalSemua += g.total;
            let statusClass = g.status === 'Paid Off' ? 'paid-off' : 'pending';
            let row = `
                <tr>
                    <td>${escapeHtml(g.nip)}</td>
                    <td>${escapeHtml(g.nama)}</td>
                    <td>${formatRp(g.gajiPokok)}</td>
                    <td>${g.poin}</td>
                    <td>${formatRp(g.bonus)}</td>
                    <td class="fw-bold text-primary">${formatRp(g.total)}</td>
                    <td>
                        <select class="status-select ${statusClass}" data-nip="${escapeHtml(g.nip)}" data-periode="${g.periode}">
                            <option value="Pending" ${g.status === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="Paid Off" ${g.status === 'Paid Off' ? 'selected' : ''}>Paid Off</option>
                        </select>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
        document.getElementById('totalGajiDisplay').innerText = formatRp(totalSemua);
        document.getElementById('periodeDisplay').innerText = month;

        // Attach event listeners untuk dropdown
        document.querySelectorAll('.status-select').forEach(select => {
            // Update class saat value berubah (tanpa reload)
            select.addEventListener('change', function() {
                const nip = this.dataset.nip;
                const periode = this.dataset.periode;
                const newStatus = this.value;
                const oldStatus = allGaji.find(g => g.nip === nip && g.periode === periode)?.status;
                if (oldStatus === newStatus) return;
                Swal.fire({
                    title: 'Ubah Status Gaji?',
                    text: `NIP ${nip} periode ${periode} dari "${oldStatus}" menjadi "${newStatus}"`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ubah'
                }).then(result => {
                    if (result.isConfirmed) {
                        const index = allGaji.findIndex(g => g.nip === nip && g.periode === periode);
                        if (index !== -1) {
                            allGaji[index].status = newStatus;
                            localStorage.setItem('vmedis_gaji', JSON.stringify(allGaji));
                            renderTable(document.getElementById('filterBulan').value);
                            Swal.fire('Berhasil', 'Status gaji diubah', 'success');
                        }
                    } else {
                        // Reset dropdown ke nilai lama
                        this.value = oldStatus;
                        // Kembalikan class sesuai oldStatus
                        if (oldStatus === 'Paid Off') {
                            this.classList.remove('pending');
                            this.classList.add('paid-off');
                        } else {
                            this.classList.remove('paid-off');
                            this.classList.add('pending');
                        }
                    }
                });
            });
            // Tambahkan event untuk mengubah class ketika opsi dipilih secara langsung (opsional)
            select.addEventListener('change', function() {
                if (this.value === 'Paid Off') {
                    this.classList.remove('pending');
                    this.classList.add('paid-off');
                } else {
                    this.classList.remove('paid-off');
                    this.classList.add('pending');
                }
            });
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

    document.getElementById('filterBulan').addEventListener('change', () => renderTable(document.getElementById('filterBulan').value));
    document.getElementById('exportBtn').addEventListener('click', () => Swal.fire('Info', 'Fitur export segera hadir', 'info'));

    renderTable('2025-04');
</script>
@endsection