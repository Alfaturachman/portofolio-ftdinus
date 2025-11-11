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
    
    .upload-section {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
        background-color: #f8f9fa;
    }
    
    .upload-section.drag-over {
        border-color: #0f4c92;
        background-color: #e7f3ff;
    }
    
    .upload-preview {
        margin-top: 20px;
        max-height: 400px;
        overflow: auto;
    }
    
    .pdf-preview-btn {
        margin-top: 10px;
        display: inline-block;
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

    <!-- Upload RPS dan Informasi Mata Kuliah -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Informasi Mata Kuliah</h4>
                        <?php if (isset($rpsFile)): ?>
                            <div id="alert" class="alert alert-success" role="alert">
                                Upload RPS sudah berhasil, silahkan untuk melengkapi informasi mata kuliah!
                            </div>
                        <?php else: ?>
                            <div id="alert" class="alert alert-primary" role="alert">
                                Silahkan untuk mengupload file RPS dan mengisi informasi mata kuliah di bawah sebelum melanjutkan!
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('portofolio-form/saveInfoMatkul') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field(); ?>

                        <!-- Hidden field to indicate edit mode -->
                        <?php if (isset($idPorto)): ?>
                            <input type="hidden" name="id_porto" value="<?= $idPorto ?>">
                        <?php endif; ?>

                        <!-- Upload RPS Section -->
                        <div class="upload-section" id="uploadSection">
                            <div class="mb-3">
                                <label for="rps_file" class="form-label">Upload File RPS (PDF)</label>
                                <input type="file" class="form-control" id="rps_file" name="rps_file" accept="application/pdf">
                                <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                            </div>
                            
                            <?php if (isset($rpsFile)): ?>
                                <div class="alert alert-success mt-3">
                                    File RPS telah diupload: <?= basename($rpsFile) ?>
                                    <button type="button" class="btn btn-sm btn-info pdf-preview-btn" onclick="showPdfModal('<?= base_url('uploads/rps/' . basename($rpsFile)) ?>')">
                                        <i class="ti ti-file-text"></i> Lihat RPS
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($rpsFile) || !empty($pdfUrl)): ?>
                            <div class="upload-preview" style="height: 400px; border: 1px solid #ccc; margin-top: 20px;">
                                <iframe src="<?= !empty($pdfUrl) ? esc($pdfUrl) : base_url('uploads/rps/' . basename($rpsFile)) ?>" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                        <?php endif; ?>

                        <!-- Informasi Mata Kuliah -->
                        <div class="mt-4">
                            <h5 class="fw-bolder mb-3">Data Mata Kuliah</h5>

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
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <?php if (isset($idPorto)): ?>
                                <a class="btn btn-secondary" href="<?= base_url('portofolio-form/') ?>" onclick="return confirm('Yakin ingin kembali? Data yang belum disimpan akan hilang jika proses belum selesai.')">
                                    <i class="ti ti-arrow-left"></i> Kembali
                                </a>
                            <?php else: ?>
                                <a class="btn btn-secondary" href="<?= base_url('portofolio-form/') ?>" onclick="return confirm('Yakin ingin kembali? Data yang belum disimpan akan hilang jika proses belum selesai.')">
                                    <i class="ti ti-arrow-left"></i> Kembali
                                </a>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary">
                                <?php if (isset($idPorto)): ?>
                                    Perbarui & Lanjutkan <i class="ti ti-arrow-right"></i>
                                <?php else: ?>
                                    Selanjutnya <i class="ti ti-arrow-right"></i>
                                <?php endif; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk preview PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Preview RPS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfPreview" width="100%" height="600px" style="border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // AJAX Upload RPS File
        document.getElementById('rps_file').addEventListener('change', function() {
            const fileInput = this;
            const formData = new FormData();
            formData.append('rps_file', fileInput.files[0]);

            // Cek apakah file dipilih
            if (fileInput.files.length === 0) {
                showModal('Harap pilih file untuk diupload.');
                return;
            }

            // AJAX Request
            fetch('<?= base_url('portofolio-form/saveUploadRps') ?>', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Perbarui tampilan setelah upload berhasil
                        document.getElementById('alert').className = 'alert alert-success';
                        document.getElementById('alert').innerHTML = 'Upload RPS sudah berhasil, silahkan untuk melengkapi informasi mata kuliah!';
                        
                        // Update informasi file yang diupload
                        const filename = data.pdfUrl.split('/').pop();
                        const uploadSection = document.querySelector('.upload-section');
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success mt-3';
                        alertDiv.innerHTML = `File RPS telah diupload: ${filename}
                            <button type="button" class="btn btn-sm btn-info pdf-preview-btn" onclick="showPdfModal('${data.pdfUrl}')">
                                <i class="ti ti-file-text"></i> Lihat RPS
                            </button>`;
                        uploadSection.appendChild(alertDiv);
                        
                        // Update preview RPS
                        const previewContainer = document.querySelector('.upload-preview');
                        if (previewContainer) {
                            previewContainer.innerHTML = `<iframe src="${data.pdfUrl}" width="100%" height="100%" style="border: none;"></iframe>`;
                        } else {
                            const newPreview = document.createElement('div');
                            newPreview.className = 'upload-preview';
                            newPreview.style.height = '400px';
                            newPreview.style.border = '1px solid #ccc';
                            newPreview.style.marginTop = '20px';
                            newPreview.innerHTML = `<iframe src="${data.pdfUrl}" width="100%" height="100%" style="border: none;"></iframe>`;
                            document.querySelector('form').insertAdjacentElement('afterend', newPreview);
                        }
                        
                        // Reset file input
                        fileInput.value = '';
                    } else {
                        showModal('Gagal mengupload file: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showModal('Terjadi kesalahan saat mengupload file.');
                });
        });

        function showModal(message) {
            // Create modal element dynamically if it doesn't exist
            let modal = document.getElementById('messageModal');
            if (!modal) {
                // Create modal HTML
                const modalHtml = `
                    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="messageModalLabel">Pesan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" id="modalMessage">
                                    <!-- Pesan akan dimasukkan di sini -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                modal = document.getElementById('messageModal');
            }
            
            const modalMessage = document.getElementById('modalMessage');
            modalMessage.textContent = message;
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }

        function showPdfModal(pdfUrl) {
            const pdfPreview = document.getElementById('pdfPreview');
            pdfPreview.src = pdfUrl;
            
            const pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
            pdfModal.show();
        }

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

            // Add drag and drop functionality for upload section
            const uploadSection = document.getElementById('uploadSection');
            const fileInput = document.getElementById('rps_file');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadSection.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadSection.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadSection.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                uploadSection.classList.add('drag-over');
            }

            function unhighlight() {
                uploadSection.classList.remove('drag-over');
            }

            uploadSection.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length) {
                    fileInput.files = files;
                }
            }
        });
    </script>
</div>

<!-- Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?= $this->include('backend/partials/footer') ?>