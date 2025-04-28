<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <div class="row pt-3">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-center">
                        <h4 class="fw-bolder mb-0">Portofolio Mata Kuliah</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Portofolio Table -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <a class="btn btn-primary" href="<?= base_url('portofolio-form/upload-rps') ?>">
                                    Tambah Portofolio
                                </a>
                            </div>
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" id="searchInput" placeholder="Cari...">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">Cari</button>
                            </div>
                        </div>

                        <div id="alert" class="alert alert-primary" role="alert">
                            Di bawah merupakan portofolio mata kuliah!
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="matkulTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 15%">Kode Mata Kuliah</th>
                                    <th style="width: 20%">Mata Kuliah</th>
                                    <th style="width: 15%">Kelompok Mata Kuliah</th>
                                    <th style="width: 10%">Tahun Akademik</th>
                                    <th style="width: 10%">Semester</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($matkulList as $matkul): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $matkul['kode_matkul'] ?></td>
                                        <td><?= $matkul['matkul'] ?></td>
                                        <td><?= $matkul['kelp_matkul'] ?></td>
                                        <td><?= $matkul['kode_ts'] ?></td>
                                        <td><?= $matkul['semester'] ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php 
                                                $importKey = $matkul['kode_matkul'] . '_' . $matkul['kelp_matkul'] . '_' . $matkul['kode_ts'];
                                                $isImported = isset($importStatus[$importKey]) && $importStatus[$importKey];
                                                
                                                if ($isImported): ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="Data mahasiswa sudah diimport">
                                                        <i class="fas fa-check-circle me-1"></i>Imported
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-success import-btn" data-kode="<?= $matkul['kode_matkul'] ?>" 
                                                            data-kelp="<?= $matkul['kelp_matkul'] ?>" data-ts="<?= $matkul['kode_ts'] ?>">
                                                        Import
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <a href="<?= base_url('portofolio-form/daftar/' . $matkul['kode_matkul']) ?>" class="btn btn-sm btn-primary">
                                                    Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('import-data/saveMahasiswaKelas') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Pilih File Excel</label>
                        <input class="form-control" type="file" id="importFile" name="importFile" accept=".xlsx, .xls">
                    </div>
                    <div class="form-text">Format file: Excel (.xlsx, .xls)</div>
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i> 
                            Pastikan format file Excel berisi kolom NIM dan Nama Mahasiswa.
                        </small>
                    </div>
                    <!-- Hidden inputs for course information -->
                    <input type="hidden" name="kode_matkul" id="importKodeMatkul">
                    <input type="hidden" name="matkul" id="importMatkul">
                    <input type="hidden" name="kelp_matkul" id="importKelpMatkul">
                    <input type="hidden" name="kode_ts" id="importKodeTs">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="notificationModalBody">
                <!-- Notification message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const table = document.getElementById('matkulTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    function searchTable() {
        const filter = searchInput.value.toLowerCase();
        
        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent || cells[j].innerText;
                
                if (cellText.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            
            rows[i].style.display = found ? '' : 'none';
        }
    }

    searchButton.addEventListener('click', searchTable);
    searchInput.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            searchTable();
        }
    });

    // Import button functionality
    const importBtns = document.querySelectorAll('.import-btn');
    importBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const kode = this.getAttribute('data-kode');
            const kelpMatkul = this.getAttribute('data-kelp'); // Get from data attribute
            const kodeTs = this.getAttribute('data-ts'); // Get from data attribute
            const row = this.closest('tr');
            const matkul = row.querySelector('td:nth-child(3)').textContent;
            
            // Set values to hidden inputs
            document.getElementById('importKodeMatkul').value = kode;
            document.getElementById('importMatkul').value = matkul;
            document.getElementById('importKelpMatkul').value = kelpMatkul;
            document.getElementById('importKodeTs').value = kodeTs;
            
            // Update modal title
            document.getElementById('importModalLabel').textContent = 'Import Data Mahasiswa untuk ' + matkul;
            
            // Show modal
            const importModal = new bootstrap.Modal(document.getElementById('importModal'));
            importModal.show();
        });
    });
    
    // Function to show notification modal
    function showNotification(message, isSuccess) {
        const modalElement = document.getElementById('notificationModal');
        const modalBody = document.getElementById('notificationModalBody');
        const modalTitle = document.getElementById('notificationModalLabel');
        const modalContent = modalElement.querySelector('.modal-content');
        
        // Style based on success or error
        if (isSuccess) {
            modalContent.classList.remove('border-danger');
            modalContent.classList.add('border-success');
            modalTitle.textContent = 'Sukses';
            modalTitle.classList.remove('text-danger');
            modalTitle.classList.add('text-success');
            modalBody.innerHTML = `<div class="alert alert-success mb-0"><i class="fas fa-check-circle me-2"></i>${message}</div>`;
        } else {
            modalContent.classList.remove('border-success');
            modalContent.classList.add('border-danger');
            modalTitle.textContent = 'Error';
            modalTitle.classList.remove('text-success');
            modalTitle.classList.add('text-danger');
            modalBody.innerHTML = `<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-circle me-2"></i>${message}</div>`;
        }
        
        // Show modal
        const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
        notificationModal.show();
    }
    
    // Check for flash messages from session
    <?php if (session()->getFlashdata('success')): ?>
        showNotification('<?= session()->getFlashdata('success') ?>', true);
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        showNotification('<?= session()->getFlashdata('error') ?>', false);
    <?php endif; ?>
});
</script>

<?= $this->include('backend/partials/footer') ?>