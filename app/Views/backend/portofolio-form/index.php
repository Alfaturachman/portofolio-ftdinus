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

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filterTahun" class="form-label">Tahun Akademik</label>
                            <select class="form-select" id="filterTahun">
                                <option value="">Semua Tahun</option>
                                <?php
                                // Get unique years from matkulList
                                $years = array_unique(array_column($matkulList, 'tahun'));
                                sort($years);
                                foreach ($years as $year):
                                ?>
                                    <option value="<?= $year ?>"><?= $year ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="filterSemester" class="form-label">Semester</label>
                            <select class="form-select" id="filterSemester">
                                <option value="">Semua Semester</option>
                                <?php
                                // Get unique semesters from matkulList
                                $semesters = array_unique(array_column($matkulList, 'semester'));
                                sort($semesters);
                                foreach ($semesters as $semester):
                                ?>
                                    <option value="<?= $semester ?>"><?= $semester ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary" id="resetFilter">
                                <i class="fas fa-redo me-1"></i>Reset Filter
                            </button>
                        </div>
                    </div>

                    <!-- Table Section -->
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
                                    <tr data-tahun="<?= $matkul['tahun'] ?>" data-semester="<?= $matkul['semester'] ?>">
                                        <td><?= $no++ ?></td>
                                        <td><?= $matkul['kode_mk'] ?></td>
                                        <td><?= $matkul['nama_mk'] ?></td>
                                        <td><?= $matkul['kelp_matkul'] ?></td>
                                        <td><?= $matkul['tahun'] ?></td>
                                        <td><?= $matkul['semester'] ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php
                                                $importKey = $matkul['kode_mk'] . '_' . $matkul['kelp_matkul'] . '_' . $matkul['tahun'];
                                                $isImported = isset($importStatus[$importKey]) && $importStatus[$importKey];

                                                if ($isImported): ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="Data mahasiswa sudah diimport">
                                                        <i class="fas fa-check-circle me-1"></i>Imported
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-success import-btn" data-kode="<?= $matkul['kode_mk'] ?>"
                                                        data-kelp="<?= $matkul['kelp_matkul'] ?>" data-ts="<?= $matkul['tahun'] ?>">
                                                        Import
                                                    </button>
                                                <?php endif; ?>

                                                <a href="<?= base_url('portofolio-form/daftar/' . $matkul['kode_mk'] . '/' . $matkul['kode_ts'] . '/' . $matkul['semester']) ?>" class="btn btn-sm btn-primary">
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
        const filterTahun = document.getElementById('filterTahun');
        const filterSemester = document.getElementById('filterSemester');
        const resetFilter = document.getElementById('resetFilter');
        const tableRows = document.querySelectorAll('#matkulTable tbody tr');

        function filterTable() {
            const selectedTahun = filterTahun.value;
            const selectedSemester = filterSemester.value;
            let visibleCount = 0;

            tableRows.forEach(row => {
                const rowTahun = row.getAttribute('data-tahun');
                const rowSemester = row.getAttribute('data-semester');

                const tahunMatch = !selectedTahun || rowTahun === selectedTahun;
                const semesterMatch = !selectedSemester || rowSemester === selectedSemester;

                if (tahunMatch && semesterMatch) {
                    row.style.display = '';
                    visibleCount++;
                    // Update nomor urut
                    row.querySelector('td:first-child').textContent = visibleCount;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Event listeners
        filterTahun.addEventListener('change', filterTable);
        filterSemester.addEventListener('change', filterTable);

        resetFilter.addEventListener('click', function() {
            filterTahun.value = '';
            filterSemester.value = '';
            filterTable();
        });
    });
</script>

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