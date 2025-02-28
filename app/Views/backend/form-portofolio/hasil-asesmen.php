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

                        <div class="step-line active"></div>

                        <!-- Informasi Matkul -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-bookmark"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Informasi Matkul</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- CPL & PI -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-bulb"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPL & PI</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- CPMK & Sub CPMK -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-list-details"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPMK & Sub</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Pemetaan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-report-analytics"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Pemetaan</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Rancangan Assesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-file-text"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Assesmen</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Pelaksanaan Perkuliahan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-school"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Pelaksanaan Perkuliahan</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Hasil Asesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
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

    <!-- Hasil Asesmen -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Hasil Asesmen</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengupload hasil asesmen di bawah sebelum melanjutkan!
                        </div>
                        <div id="successAlert" class="alert alert-success" style="display: none;" role="alert">
                            Data hasil asesmen berhasil disimpan!
                        </div>
                        <div id="errorAlert" class="alert alert-danger" style="display: none;" role="alert">
                            Terjadi kesalahan saat menyimpan data!
                        </div>
                    </div>

                    <form id="hasilAsesmenForm" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <!-- Tugas -->
                        <h5 class="fw-bolder mb-3">1. Hasil Tugas</h5>
                        <div class="form-group mb-4">
                            <label for="jawaban_tugas" class="form-label">Jawaban</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="jawaban_tugas" name="jawaban_tugas" accept="application/pdf" <?= !isset(session()->get('hasil_asesmen_files')['jawaban_tugas']) ? 'required' : '' ?>>
                                <?php if(isset(session()->get('hasil_asesmen_files')['jawaban_tugas'])): ?>
                                    <span class="input-group-text bg-success text-white">
                                        <i class="ti ti-check"></i> File Terunggah
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if(isset(session()->get('hasil_asesmen_files')['jawaban_tugas'])): ?>
                                <div class="mt-2">
                                    <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('hasil_asesmen_files')['jawaban_tugas']['name']) ?></span>
                                    <p class="text-muted small">Ukuran: <?= round(session()->get('hasil_asesmen_files')['jawaban_tugas']['size'] / 1024, 2) ?> KB</p>
                                </div>
                            <?php endif; ?>
                            <p class="mt-2" style="color: #5a6a85!important;">*contoh jawaban benar, jawaban sedang, jawaban salah</p>
                        </div>

                        <!-- Ujian Tengah Semester -->
                        <h5 class="fw-bolder mb-3">2. Hasil Ujian Tengah Semester</h5>
                        <div class="form-group mb-4">
                            <label for="jawaban_uts" class="form-label">Jawaban</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="jawaban_uts" name="jawaban_uts" accept="application/pdf" <?= !isset(session()->get('hasil_asesmen_files')['jawaban_uts']) ? 'required' : '' ?>>
                                <?php if(isset(session()->get('hasil_asesmen_files')['jawaban_uts'])): ?>
                                    <span class="input-group-text bg-success text-white">
                                        <i class="ti ti-check"></i> File Terunggah
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if(isset(session()->get('hasil_asesmen_files')['jawaban_uts'])): ?>
                                <div class="mt-2">
                                    <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('hasil_asesmen_files')['jawaban_uts']['name']) ?></span>
                                    <p class="text-muted small">Ukuran: <?= round(session()->get('hasil_asesmen_files')['jawaban_uts']['size'] / 1024, 2) ?> KB</p>
                                </div>
                            <?php endif; ?>
                            <p class="mt-2" style="color: #5a6a85!important;">*contoh jawaban benar, jawaban sedang, jawaban salah</p>
                        </div>

                        <!-- Ujian Akhir Semester -->
                        <h5 class="fw-bolder mb-3">3. Hasil Ujian Akhir Semester</h5>
                        <div class="form-group mb-4">
                            <label for="jawaban_uas" class="form-label">Jawaban</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="jawaban_uas" name="jawaban_uas" accept="application/pdf" <?= !isset(session()->get('hasil_asesmen_files')['jawaban_uas']) ? 'required' : '' ?>>
                                <?php if(isset(session()->get('hasil_asesmen_files')['jawaban_uas'])): ?>
                                    <span class="input-group-text bg-success text-white">
                                        <i class="ti ti-check"></i> File Terunggah
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if(isset(session()->get('hasil_asesmen_files')['jawaban_uas'])): ?>
                                <div class="mt-2">
                                    <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('hasil_asesmen_files')['jawaban_uas']['name']) ?></span>
                                    <p class="text-muted small">Ukuran: <?= round(session()->get('hasil_asesmen_files')['jawaban_uas']['size'] / 1024, 2) ?> KB</p>
                                </div>
                            <?php endif; ?>
                            <p class="mt-2" style="color: #5a6a85!important;">*contoh jawaban benar, jawaban sedang, jawaban salah</p>
                        </div>

                        <!-- 4. Nilai Mata Kuliah -->
                        <h5 class="fw-bolder mb-3">4. Nilai Mata Kuliah</h5>
                        <div class="form-group mb-4">
                            <label for="nilai_mata_kuliah" class="form-label">Upload File</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="nilai_mata_kuliah" name="nilai_mata_kuliah" accept="application/pdf">
                                <?php if(isset(session()->get('hasil_asesmen_files')['nilai_mata_kuliah'])): ?>
                                    <span class="input-group-text bg-success text-white">
                                        <i class="ti ti-check"></i> File Terunggah
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if(isset(session()->get('hasil_asesmen_files')['nilai_mata_kuliah'])): ?>
                                <div class="mt-2">
                                    <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('hasil_asesmen_files')['nilai_mata_kuliah']['name']) ?></span>
                                    <p class="text-muted small">Ukuran: <?= round(session()->get('hasil_asesmen_files')['nilai_mata_kuliah']['size'] / 1024, 2) ?> KB</p>
                                </div>
                            <?php endif; ?>
                            <p class="mt-2" style="color: #5a6a85!important;">*opsional</p>
                        </div>

                        <!-- 5. Nilai CPMK -->
                        <h5 class="fw-bolder mb-3">5. Nilai CPMK</h5>
                        <div class="form-group mb-4">
                            <label for="nilai_cpmk" class="form-label">Upload File</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="nilai_cpmk" name="nilai_cpmk" accept="application/pdf" <?= !isset(session()->get('hasil_asesmen_files')['nilai_cpmk']) ? 'required' : '' ?>>
                                <?php if(isset(session()->get('hasil_asesmen_files')['nilai_cpmk'])): ?>
                                    <span class="input-group-text bg-success text-white">
                                        <i class="ti ti-check"></i> File Terunggah
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if(isset(session()->get('hasil_asesmen_files')['nilai_cpmk'])): ?>
                                <div class="mt-2">
                                    <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('hasil_asesmen_files')['nilai_cpmk']['name']) ?></span>
                                    <p class="text-muted small">Ukuran: <?= round(session()->get('hasil_asesmen_files')['nilai_cpmk']['size'] / 1024, 2) ?> KB</p>
                                </div>
                            <?php endif; ?>
                            <p class="mt-2" style="color: #5a6a85!important;">*wajib diisi</p>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/pelaksanaan-perkuliahan') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="submit" id="submitBtn" class="btn btn-success me-2">
                                    <i class="ti ti-device-floppy"></i> Simpan
                                </button>
                                <a id="nextBtn" class="btn btn-primary" href="<?= base_url('portofolio-form/evaluasi-perkuliahan') ?>">
                                    Selanjutnya <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('hasilAsesmenForm');
        const submitBtn = document.getElementById('submitBtn');
        const nextBtn = document.getElementById('nextBtn');
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        
        // Check if we have data in session
        <?php if(null === session()->get('hasil_asesmen_files') || empty(session()->get('hasil_asesmen_files'))): ?>
            nextBtn.style.display = 'none';
        <?php endif; ?>
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Hide any previous alerts
            successAlert.style.display = 'none';
            errorAlert.style.display = 'none';
            
            // Create FormData object
            const formData = new FormData(form);
            
            // Disable submit button to prevent multiple submissions
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ti ti-loader animate-spin"></i> Menyimpan...';
            
            // Send AJAX request
            fetch('<?= base_url('portofolio-form/saveHasilAsesmen') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    successAlert.textContent = data.message;
                    successAlert.style.display = 'block';
                    
                    // Show next button
                    nextBtn.style.display = 'inline-block';
                    
                    // Scroll to top to see the alert
                    window.scrollTo({top: 0, behavior: 'smooth'});
                    
                    // Reload page after successful save to show updated file info
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    errorAlert.textContent = data.message;
                    errorAlert.style.display = 'block';
                    
                    // Scroll to top to see the alert
                    window.scrollTo({top: 0, behavior: 'smooth'});
                }
            })
            .catch(error => {
                // Show error message
                errorAlert.textContent = 'Terjadi kesalahan saat menyimpan data.';
                errorAlert.style.display = 'block';
                
                // Scroll to top to see the alert
                window.scrollTo({top: 0, behavior: 'smooth'});
                
                console.error('Error:', error);
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-device-floppy"></i> Simpan';
            });
        });
    });
</script>

<?= $this->include('backend/partials/footer') ?>