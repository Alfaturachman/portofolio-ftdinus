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
                            <label for="nama_mk" class="form-label">Nama Mata Kuliah</label>
                            <select class="form-select" id="nama_mk" name="nama_mk" required>
                                <option value="" hidden>Pilih Mata Kuliah</option>
                                <?php foreach ($mataKuliah as $mk): ?>
                                    <option value="<?= htmlspecialchars($mk['nama_mk']) ?>"
                                        data-fakultas="<?= htmlspecialchars($mk['fakultas']) ?>"
                                        data-progdi="<?= htmlspecialchars($mk['progdi']) ?>"
                                        data-kode_mk="<?= htmlspecialchars($mk['kode_mk']) ?>"
                                        data-kelompok_mk="<?= htmlspecialchars($mk['kelompok_mk']) ?>"
                                        data-sks_teori="<?= htmlspecialchars($mk['sks_teori']) ?>"
                                        data-sks_praktik="<?= htmlspecialchars($mk['sks_praktik']) ?>"
                                        <?= (isset($infoMatkul['nama_mk']) && $infoMatkul['nama_mk'] === $mk['nama_mk']) ? 'selected' : '' ?>>
                                        <?= $mk['nama_mk'] ?> - <?= $mk['kode_mk'] ?> - <?= $mk['kelompok_mk'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
        // Clear all fields on page load if no mata kuliah is selected
        document.addEventListener('DOMContentLoaded', function() {
            const selectedOption = document.getElementById('nama_mk');
            if (!selectedOption.value) {
                clearFields();
            }
        });

        document.getElementById('nama_mk').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                // Mengisi field berdasarkan atribut data pada option
                document.getElementById('kode_mk').value = selectedOption.getAttribute('data-kode_mk') || '';
                document.getElementById('kelompok_mk').value = selectedOption.getAttribute('data-kelompok_mk') || '';
                document.getElementById('sks_teori').value = selectedOption.getAttribute('data-sks_teori') || '';
                document.getElementById('sks_praktik').value = selectedOption.getAttribute('data-sks_praktik') || '';
                document.getElementById('fakultas').value = selectedOption.getAttribute('data-fakultas') || '';
                document.getElementById('progdi').value = selectedOption.getAttribute('data-progdi') || '';
            } else {
                // Kosongkan field jika tidak ada yang dipilih
                clearFields();
            }
        });

        // Function to clear all fields
        function clearFields() {
            document.getElementById('kode_mk').value = '';
            document.getElementById('kelompok_mk').value = '';
            document.getElementById('sks_teori').value = '';
            document.getElementById('sks_praktik').value = '';
            document.getElementById('fakultas').value = '';
            document.getElementById('progdi').value = '';
            document.getElementById('mk_prasyarat').value = '';
            document.getElementById('topik_mk').value = '';
        }
    </script>
</div>

<?= $this->include('backend/partials/footer') ?>