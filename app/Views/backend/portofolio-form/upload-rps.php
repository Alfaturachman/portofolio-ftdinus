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

                        <div class="step-line"></div>

                        <!-- Informasi Matkul -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
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

    <!-- Upload RPS -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Upload RPS</h4>
                        <?php if (!empty($pdfUrl)): ?>
                            <div id="alert" class="alert alert-success" role="alert">
                                Upload RPS sudah berhasil, silahkan untuk ke tahap selanjutnya.
                            </div>
                        <?php else: ?>
                            <div id="alert" class="alert alert-primary" role="alert">
                                Silahkan untuk mengupload file RPS di bawah sebelum melanjutkan!
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('portofolio-form/saveUploadRps') ?>" method="post" enctype="multipart/form-data">
                        <?php if (!empty($pdfUrl)): ?>
                            <div class="form-group mb-2">
                                <label for="rps_file" class="form-label">Upload File RPS (PDF)</label>
                                <input type="file" class="form-control" id="rps_file" name="rps_file" accept="application/pdf" required>
                                <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                            </div>
                        <?php else: ?>
                            <div class="form-group mb-2">
                                <label for="rps_file" class="form-label">Upload File RPS (PDF)</label>
                                <input type="file" class="form-control" id="rps_file" name="rps_file" accept="application/pdf" required>
                                <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pdfUrl)): ?>
                            <div class="mb-3" style="height: 600px; border: 1px solid #ccc; margin-top: 20px;">
                                <iframe src="<?= esc($pdfUrl) ?>" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                        <?php else: ?>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <?php if (!empty($pdfUrl)): ?>
                                <a class="btn btn-primary" href="<?= base_url('portofolio-form/info-matkul') ?>">
                                    Selanjutnya <i class="ti ti-arrow-right"></i>
                                </a>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary">
                                    Selanjutnya <i class="ti ti-arrow-right"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
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

<script>
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
                    showModal('File berhasil diupload.');
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
        const modalMessage = document.getElementById('modalMessage');
        modalMessage.textContent = message;
        const modal = new bootstrap.Modal(document.getElementById('messageModal'));

        // Event saat modal ditutup
        document.getElementById('messageModal').addEventListener('hidden.bs.modal', function() {
            window.location.reload();
        });

        modal.show();
    }
</script>

<?= $this->include('backend/partials/footer') ?>