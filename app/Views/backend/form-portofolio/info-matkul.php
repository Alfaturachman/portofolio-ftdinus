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
        .step-line {
            width: 50%;
        }

        .step-label {
            max-width: 60px;
        }
    }
</style>

<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <div class="row pt-3">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-center">
                        <h5 class="fw-bolder mb-0">Portofolio Form - Progress</h5>
                    </div>
                    <div id="steps" class="d-flex justify-content-between align-items-baseline mt-4">
                        <!-- Info Matkul -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-bookmark"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Info Matkul</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Topik -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle" data-step="topik">
                                <i class="ti ti-analyze"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Topik</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- CPL & Indikator -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-chart-line"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPL & Indikator</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- CPMK & Sub CPMK -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-book"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPMK & Sub</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Cetak -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-printer"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Cetak</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Upload RPS -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-upload"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Upload RPS</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Rancangan Assesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-checklist"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Assesmen</small>
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
                        <h5 class="card-title fw-bolder mb-3">Informasi Mata Kuliah</h5>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi informasi mata kuliah di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form action="<?= base_url('portofolio/saveInfoMatkul') ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label for="nama_mk" class="form-label">Nama Mata Kuliah</label>
                            <select class="form-select" id="nama_mk" name="nama_mk" required>
                                <option value="">Pilih Mata Kuliah</option>
                                <?php foreach ($mataKuliah as $mk): ?>
                                    <option value="<?= htmlspecialchars($mk['nama_mk']) ?>"
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
                        <div class="form-group mb-3">
                            <label for="sks" class="form-label">SKS</label>
                            <div class="d-flex">
                                <input type="number" class="form-control me-2" id="sks_teori" name="sks_teori" readonly value="<?= isset($infoMatkul['sks_teori']) ? $infoMatkul['sks_teori'] : '' ?>">
                                <input type="number" class="form-control" id="sks_praktik" name="sks_praktik" readonly value="<?= isset($infoMatkul['sks_praktik']) ? $infoMatkul['sks_praktik'] : '' ?>">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="mk_prasyarat" class="form-label">MK Prasyarat</label>
                            <textarea class="form-control" id="mk_prasyarat" name="mk_prasyarat" rows="3" placeholder="Masukkan mata kuliah prasyarat jika ada"><?= isset($infoMatkul['mk_prasyarat']) ? $infoMatkul['mk_prasyarat'] : '' ?></textarea>
                        </div>
                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Simpan <i class="ti ti-save"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('nama_mk').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex]; // Ambil option yang dipilih
            if (selectedOption.value) {
                document.getElementById('kode_mk').value = selectedOption.getAttribute('data-kode_mk') || '';
                document.getElementById('kelompok_mk').value = selectedOption.getAttribute('data-kelompok_mk') || '';
                document.getElementById('sks_teori').value = selectedOption.getAttribute('data-sks_teori') || '';
                document.getElementById('sks_praktik').value = selectedOption.getAttribute('data-sks_praktik') || '';
            } else {
                // Kosongkan field jika tidak ada yang dipilih
                document.getElementById('kode_mk').value = '';
                document.getElementById('kelompok_mk').value = '';
                document.getElementById('sks_teori').value = '';
                document.getElementById('sks_praktik').value = '';
            }
        });
    </script>
</div>

<?= $this->include('backend/partials/footer') ?>