@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <h4 class="fw-bold mb-0">📋 Data Karyawan</h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" id="filterBtn"><i class="bi bi-funnel"></i> Filter</button>
        <button class="btn btn-outline-success" id="exportBtn"><i class="bi bi-download"></i> Export</button>
        <button class="btn btn-primary" id="tambahKaryawanBtn"><i class="bi bi-person-plus"></i> Tambah Karyawan</button>
    </div>
</div>

<!-- Search Box -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Cari Karyawan</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchKaryawan" class="form-control" placeholder="NIP, Nama, atau Divisi...">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Karyawan -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>NIP</th><th>Nama</th><th>Divisi</th><th>Jabatan</th><th>Status</th><th>Kehadiran</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="karyawanTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Tambah/Edit Karyawan -->
<div class="modal fade" id="karyawanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="karyawanId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">NIP</label>
                    <input type="text" id="karyawanNIP" class="form-control" placeholder="Contoh: 198512342025001">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" id="karyawanNama" class="form-control" placeholder="Nama karyawan">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Divisi</label>
                    <select id="karyawanDivisi" class="form-select">
                        <option value="">Pilih Divisi</option>
                        <option value="Programmer">Programmer</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Tester">Tester</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jabatan</label>
                    <select id="karyawanJabatan" class="form-select">
                        <option value="">Pilih Jabatan</option>
                        <option value="Senior Programmer">Senior Programmer</option>
                        <option value="Junior Programmer">Junior Programmer</option>
                        <option value="Senior Marketing">Senior Marketing</option>
                        <option value="Junior Marketing">Junior Marketing</option>
                        <option value="Tester Website">Tester Website</option>
                        <option value="Tester Mobile">Tester Mobile</option>
                    </select>
                </div>
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle"></i> Status dan kehadiran akan diperbarui otomatis dari sistem absensi karyawan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveKaryawanBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="mt-3 text-muted small">
    <i class="bi bi-info-circle"></i> Status dan kehadiran diambil dari sistem absensi otomatis (simulasi).
</div>

<script>
    // Data karyawan dari localStorage
    let karyawanList = JSON.parse(localStorage.getItem('vmedis_karyawan')) || [
        { nip: '198512342025001', nama: 'Andy Sugar', divisi: 'Programmer', jabatan: 'Senior Programmer', status: 'Active', kehadiran: 90 },
        { nip: '199003212025002', nama: 'Rudy', divisi: 'Programmer', jabatan: 'Senior Programmer', status: 'Active', kehadiran: 90 },
        { nip: '199512152025003', nama: 'Rudy', divisi: 'Programmer & Junior Marketing', jabatan: 'Senior Manager', status: 'Active', kehadiran: 90 },
        { nip: '198811232025004', nama: 'Rudy', divisi: 'Programmer & Junior Marketing', jabatan: 'Senior Manager', status: 'Inactive', kehadiran: 85 }
    ];

    function syncKaryawan() {
        localStorage.setItem('vmedis_karyawan', JSON.stringify(karyawanList));
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

    function renderKaryawanTable(filter = '') {
        const tbody = document.getElementById('karyawanTableBody');
        tbody.innerHTML = '';
        const filterLower = filter.toLowerCase();
        const filtered = karyawanList.filter(k => 
            k.nip.toLowerCase().includes(filterLower) ||
            k.nama.toLowerCase().includes(filterLower) ||
            k.divisi.toLowerCase().includes(filterLower) ||
            k.jabatan.toLowerCase().includes(filterLower)
        );

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center">Tidak ada data karyawan</td></tr>';
            return;
        }

        filtered.forEach(k => {
            const statusBadge = k.status === 'Active' ? 'bg-success' : 'bg-secondary';
            const attendanceHtml = `
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold">${k.kehadiran}%</span>
                    <div class="progress" style="width: 70px; height: 6px;">
                        <div class="progress-bar bg-primary" style="width: ${k.kehadiran}%"></div>
                    </div>
                </div>
            `;
            const row = `
                <tr>
                    <td>${escapeHtml(k.nip)}</td>
                    <td>${escapeHtml(k.nama)}</td>
                    <td>${escapeHtml(k.divisi)}</td>
                    <td>${escapeHtml(k.jabatan)}</td>
                    <td><span class="badge ${statusBadge}">${k.status}</span></td>
                    <td>${attendanceHtml}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-1 edit-karyawan" data-nip="${escapeHtml(k.nip)}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-danger hapus-karyawan" data-nip="${escapeHtml(k.nip)}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        // Attach event untuk tombol edit dan hapus
        document.querySelectorAll('.edit-karyawan').forEach(btn => {
            btn.removeEventListener('click', handleEdit);
            btn.addEventListener('click', handleEdit);
        });
        document.querySelectorAll('.hapus-karyawan').forEach(btn => {
            btn.removeEventListener('click', handleHapus);
            btn.addEventListener('click', handleHapus);
        });
    }

    function handleEdit(e) {
        const nip = e.currentTarget.getAttribute('data-nip');
        const karyawan = karyawanList.find(k => k.nip === nip);
        if (karyawan) {
            document.getElementById('karyawanId').value = karyawan.nip;
            document.getElementById('karyawanNIP').value = karyawan.nip;
            document.getElementById('karyawanNama').value = karyawan.nama;
            document.getElementById('karyawanDivisi').value = karyawan.divisi;
            document.getElementById('karyawanJabatan').value = karyawan.jabatan;
            document.getElementById('modalTitle').innerText = 'Edit Karyawan';
            new bootstrap.Modal(document.getElementById('karyawanModal')).show();
        }
    }

    function handleHapus(e) {
        const nip = e.currentTarget.getAttribute('data-nip');
        Swal.fire({
            title: 'Yakin hapus?',
            text: `Karyawan dengan NIP ${nip} akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                karyawanList = karyawanList.filter(k => k.nip !== nip);
                syncKaryawan();
                renderKaryawanTable(document.getElementById('searchKaryawan').value);
                Swal.fire('Terhapus!', 'Data karyawan berhasil dihapus.', 'success');
            }
        });
    }

    function resetKaryawanForm() {
        document.getElementById('karyawanId').value = '';
        document.getElementById('karyawanNIP').value = '';
        document.getElementById('karyawanNama').value = '';
        document.getElementById('karyawanDivisi').value = '';
        document.getElementById('karyawanJabatan').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Karyawan';
    }

    function simpanKaryawan() {
        const nipLama = document.getElementById('karyawanId').value;
        const nipBaru = document.getElementById('karyawanNIP').value.trim();
        const nama = document.getElementById('karyawanNama').value.trim();
        const divisi = document.getElementById('karyawanDivisi').value;
        const jabatan = document.getElementById('karyawanJabatan').value;

        if (!nipBaru || !nama || !divisi || !jabatan) {
            Swal.fire('Error', 'Harap isi semua field (NIP, Nama, Divisi, Jabatan)!', 'error');
            return;
        }

        // Cek duplikat NIP
        if (!nipLama && karyawanList.some(k => k.nip === nipBaru)) {
            Swal.fire('Error', `NIP "${nipBaru}" sudah ada. Gunakan NIP lain.`, 'error');
            return;
        }
        if (nipLama && nipLama !== nipBaru && karyawanList.some(k => k.nip === nipBaru)) {
            Swal.fire('Error', `NIP "${nipBaru}" sudah digunakan karyawan lain.`, 'error');
            return;
        }

        if (nipLama) {
            const index = karyawanList.findIndex(k => k.nip === nipLama);
            if (index !== -1) {
                karyawanList[index] = {
                    ...karyawanList[index],
                    nip: nipBaru,
                    nama: nama,
                    divisi: divisi,
                    jabatan: jabatan
                };
            }
        } else {
            karyawanList.push({
                nip: nipBaru,
                nama: nama,
                divisi: divisi,
                jabatan: jabatan,
                status: 'Active',
                kehadiran: 0
            });
        }
        syncKaryawan();
        renderKaryawanTable(document.getElementById('searchKaryawan').value);
        bootstrap.Modal.getInstance(document.getElementById('karyawanModal')).hide();
        Swal.fire('Berhasil', 'Data karyawan berhasil disimpan.', 'success');
    }

    // Event listener untuk tombol simpan (dipasang setelah DOM siap)
    document.addEventListener('DOMContentLoaded', function() {
        const saveBtn = document.getElementById('saveKaryawanBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                simpanKaryawan();
            });
        }

        document.getElementById('tambahKaryawanBtn').addEventListener('click', function() {
            resetKaryawanForm();
            new bootstrap.Modal(document.getElementById('karyawanModal')).show();
        });

        document.getElementById('filterBtn').addEventListener('click', function() {
            Swal.fire('Info', 'Fitur filter akan segera hadir', 'info');
        });

        document.getElementById('exportBtn').addEventListener('click', function() {
            Swal.fire('Info', 'Fitur export akan segera hadir', 'info');
        });

        document.getElementById('searchKaryawan').addEventListener('keyup', function() {
            renderKaryawanTable(this.value);
        });
    });

    // Render awal
    renderKaryawanTable();
</script>
@endsection