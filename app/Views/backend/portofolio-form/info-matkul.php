<style>
    .step-circle {
        width: 40px;
        height: 40px;
        border: 2px solid #ccc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ccc;
        background-color: #f9f9f9;
    }

    .step-circle.active {
        border-color: #0f4c92;
        color: #fff;
        background-color: #0f4c92;
    }

    .step-line {
        height: 2px;
        width: 100%;
        background-color: #ccc;
        flex-shrink: 1;
    }

    .step-line.active {
        background-color: #0f4c92;
    }

    .step-label {
        max-width: 80px;
        word-wrap: break-word;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between.align-items-baseline {
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .step-line {
            display: none;
        }
    }

    .custom-select-container {
        position: relative;
    }

    .custom-select-dropdown {
        position: absolute;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        background: white;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 4px 4px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .custom-select-item {
        cursor: pointer;
        padding: 8px 12px;
    }

    .custom-select-item:hover {
        background-color: #f8f9fa;
    }

    .list-group {
        margin-bottom: 0;
    }

    .no-results {
        padding: 8px 12px;
        color: #6c757d;
        font-style: italic;
    }
</style>

<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <div class="row pt-3">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-center mb-4">
                        <h5 class="fw-bolder mb-0">Portofolio Form - Progress</h5>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline">
                        <!-- Upload RPS -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-upload"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Upload RPS</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Informasi Matkul -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-bookmark"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Informasi Matkul</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- CPL & PI -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-bulb"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPL & PI</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- CPMK & Sub CPMK -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-list-details"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPMK & Sub</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Pemetaan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-report-analytics"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Pemetaan</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Rancangan Assesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-file-text"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Assesmen</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Pelaksanaan Perkuliahan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-school"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Pelaksanaan Perkuliahan</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Hasil Asesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-checklist"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Hasil Asesmen</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Evaluasi Perkuliahan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-chart-bar"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Evaluasi Perkuliahan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Mata Kuliah -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Informasi Mata Kuliah</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi informasi mata kuliah di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form action="<?= base_url('portofolio-form/saveInfoMatkul') ?>" method="post">
                        <?= csrf_field(); ?>
                        <?php if (!empty($pdfUrl)): ?>
                            <div class="mb-3" style="height: 600px; border: 1px solid #ccc; margin-top: 20px;">
                                <iframe src="<?= esc($pdfUrl) ?>" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                        <?php else: ?>
                        <?php endif; ?>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fakultas" class="form-label">Fakultas</label>
                                    <input type="text" class="form-control" id="fakultas" name="fakultas" readonly value="<?= isset($infoMatkul['fakultas']) ? $infoMatkul['fakultas'] : '' ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="progdi" class="form-label">Program Studi</label>
                                    <input type="text" class="form-control" id="progdi" name="progdi" readonly value="<?= isset($infoMatkul['progdi']) ? $infoMatkul['progdi'] : '' ?>">
                                </div>
                            </div>
                        </div>
                        <!-- Replace the select and script sections in the view -->
                        <div class="form-group mb-3">
                            <label for="nama_mk_display" class="form-label">Nama Mata Kuliah</label>
                            <div class="custom-select-container">
                                <input type="text" class="form-control" id="nama_mk_display" placeholder="Cari mata kuliah..." autocomplete="off">
                                <input type="hidden" id="nama_mk" name="nama_mk" value="<?= isset($infoMatkul['nama_mk']) ? $infoMatkul['nama_mk'] : '' ?>">
                                <div class="custom-select-dropdown" id="custom_select_dropdown" style="display: none;">
                                    <ul class="list-group">
                                        <?php foreach ($mataKuliah as $mk): ?>
                                            <li class="list-group-item custom-select-item"
                                                data-value="<?= htmlspecialchars($mk['nama_mk']) ?>"
                                                data-fakultas="<?= htmlspecialchars($mk['fakultas']) ?>"
                                                data-progdi="<?= htmlspecialchars($mk['progdi']) ?>"
                                                data-kode_mk="<?= htmlspecialchars($mk['kode_mk']) ?>"
                                                data-kelompok_mk="<?= htmlspecialchars($mk['kelompok_mk']) ?>"
                                                data-sks_teori="<?= htmlspecialchars($mk['sks_teori']) ?>"
                                                data-sks_praktik="<?= htmlspecialchars($mk['sks_praktik']) ?>"
                                                data-tahun="<?= htmlspecialchars($mk['tahun']) ?>"
                                                data-semester="<?= htmlspecialchars($mk['semester']) ?>"
                                                data-smt_matkul="<?= htmlspecialchars($mk['smt_matkul']) ?>">
                                                <?= $mk['nama_mk'] ?> - <?= $mk['kode_mk'] ?> - <?= $mk['kelompok_mk'] ?> (<?= $mk['tahun'] ?> - <?= $mk['semester'] ?> - Smt <?= $mk['smt_matkul'] ?>)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="tahun" name="tahun" value="<?= isset($infoMatkul['tahun']) ? $infoMatkul['tahun'] : '' ?>">
                        <input type="hidden" id="semester" name="semester" value="<?= isset($infoMatkul['semester']) ? $infoMatkul['semester'] : '' ?>">
                        <input type="hidden" id="smt_matkul" name="smt_matkul" value="<?= isset($infoMatkul['smt_matkul']) ? $infoMatkul['smt_matkul'] : '' ?>">

                        <div class="form-group mb-3">
                            <label for="kode_mk" class="form-label">Kode MK</label>
                            <input type="text" class="form-control" id="kode_mk" name="kode_mk" readonly value="<?= isset($infoMatkul['kode_mk']) ? $infoMatkul['kode_mk'] : '' ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="kelompok_mk" class="form-label">Kelompok MK</label>
                            <input type="text" class="form-control" id="kelompok_mk" name="kelompok_mk" readonly value="<?= isset($infoMatkul['kelompok_mk']) ? $infoMatkul['kelompok_mk'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sks_teori" class="form-label">SKS Teori</label>
                                    <input type="number" class="form-control" id="sks_teori" name="sks_teori" readonly value="<?= isset($infoMatkul['sks_teori']) ? $infoMatkul['sks_teori'] : '' ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sks_praktik" class="form-label">SKS Praktik</label>
                                    <input type="number" class="form-control" id="sks_praktik" name="sks_praktik" readonly value="<?= isset($infoMatkul['sks_praktik']) ? $infoMatkul['sks_praktik'] : '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="mk_prasyarat" class="form-label">Mata Kuliah Prasyarat</label>
                            <textarea class="form-control" id="mk_prasyarat" name="mk_prasyarat" rows="3" placeholder="Masukkan mata kuliah prasyarat jika ada"><?= isset($infoMatkul['mk_prasyarat']) ? $infoMatkul['mk_prasyarat'] : '' ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="topik_mk" class="form-label">Topik Perkuliahan</label>
                            <textarea class="form-control" id="topik_mk" name="topik_mk" rows="3" placeholder="Masukkan topik perkuliahan"><?= isset($infoMatkul['topik_mk']) ? $infoMatkul['topik_mk'] : '' ?></textarea>
                        </div>
                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/upload-rps') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('nama_mk_display');
            const hiddenInput = document.getElementById('nama_mk');
            const dropdown = document.getElementById('custom_select_dropdown');
            const items = document.querySelectorAll('.custom-select-item');

            // Set initial value if exists
            if (hiddenInput.value) {
                const selectedItem = Array.from(items).find(item => item.getAttribute('data-value') === hiddenInput.value);
                if (selectedItem) {
                    input.value = selectedItem.textContent.trim();
                    updateFields(selectedItem);
                }
            }

            // Show dropdown when input is focused
            input.addEventListener('focus', function() {
                dropdown.style.display = 'block';
                filterItems(input.value);
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            // Filter items as user types
            input.addEventListener('input', function() {
                filterItems(this.value);
                dropdown.style.display = 'block';

                // Clear fields if input is empty
                if (!this.value) {
                    hiddenInput.value = '';
                    clearFields();
                }
            });

            // Select item when clicked
            items.forEach(item => {
                item.addEventListener('click', function() {
                    input.value = this.textContent.trim();
                    hiddenInput.value = this.getAttribute('data-value');
                    dropdown.style.display = 'none';
                    updateFields(this);
                });
            });

            // Function to filter dropdown items
            function filterItems(query) {
                const normalizedQuery = query.toLowerCase();
                let hasResults = false;

                // Remove existing "no results" message if present
                const noResultsMsg = dropdown.querySelector('.no-results');
                if (noResultsMsg) {
                    noResultsMsg.remove();
                }

                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(normalizedQuery)) {
                        item.style.display = 'block';
                        hasResults = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Show "no results" message if no matches
                if (!hasResults) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-results';
                    noResults.textContent = 'Tidak ada hasil yang ditemukan';
                    dropdown.querySelector('.list-group').appendChild(noResults);
                }
            }

            // Function to update all fields based on selected item
            function updateFields(selectedItem) {
                document.getElementById('fakultas').value = selectedItem.getAttribute('data-fakultas') || '';
                document.getElementById('progdi').value = selectedItem.getAttribute('data-progdi') || '';
                document.getElementById('kode_mk').value = selectedItem.getAttribute('data-kode_mk') || '';
                document.getElementById('kelompok_mk').value = selectedItem.getAttribute('data-kelompok_mk') || '';
                document.getElementById('sks_teori').value = selectedItem.getAttribute('data-sks_teori') || '';
                document.getElementById('sks_praktik').value = selectedItem.getAttribute('data-sks_praktik') || '';
                document.getElementById('tahun').value = selectedItem.getAttribute('data-tahun') || '';
                document.getElementById('semester').value = selectedItem.getAttribute('data-semester') || '';
                document.getElementById('smt_matkul').value = selectedItem.getAttribute('data-smt_matkul') || '';
            }

            // Function to clear all fields
            function clearFields() {
                document.getElementById('kode_mk').value = '';
                document.getElementById('kelompok_mk').value = '';
                document.getElementById('sks_teori').value = '';
                document.getElementById('sks_praktik').value = '';
                document.getElementById('fakultas').value = '';
                document.getElementById('progdi').value = '';
                document.getElementById('tahun').value = '';
                document.getElementById('semester').value = '';
                document.getElementById('smt_matkul').value = '';
            }
        });
    </script>
</div>

<!-- Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?= $this->include('backend/partials/footer') ?>