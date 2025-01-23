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
                    </div>

                    <form id="rpsForm" action="<?= base_url('form/submit') ?>" method="post" enctype="multipart/form-data">
                        <!-- Tugas -->
                        <h5 class="fw-bolder mb-3">1. Hasil Tugas</h5>
                        <div class="form-group mb-4">
                            <label for="jawaban_tugas" class="form-label mt-2">Jawaban</label>
                            <input type="file" class="form-control mb-2" id="jawaban_tugas" name="jawaban_tugas" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*contoh jawaban benar, jawaban sedang, jawaban salah</p>
                        </div>

                        <!-- Ujian Tengah Semester -->
                        <h5 class="fw-bolder mb-3">2. Hasil Ujian Tengah Semester</h5>
                        <div class="form-group mb-4">
                            <label for="jawaban_uts" class="form-label mt-2">Jawaban</label>
                            <input type="file" class="form-control mb-2" id="jawaban_uts" name="jawaban_uts" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*contoh jawaban benar, jawaban sedang, jawaban salah</p>
                        </div>

                        <!-- Ujian Akhir Semester -->
                        <h5 class="fw-bolder mb-3">3. Hasil Ujian Akhir Semester</h5>
                        <div class="form-group mb-4">
                            <label for="jawaban_uas" class="form-label mt-2">Jawaban</label>
                            <input type="file" class="form-control mb-2" id="jawaban_uas" name="jawaban_uas" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*contoh jawaban benar, jawaban sedang, jawaban salah</p>
                        </div>

                        <!-- 4.	Nilai Mata Kuliah -->
                        <h5 class="fw-bolder mb-3">4. Nilai Mata Kuliah</h5>
                        <div class="form-group mb-4">
                            <input type="file" class="form-control mb-2" id="nilai_mata_kuliah" name="nilai_mata_kuliah" accept="application/pdf">
                            <p class="mt-2" style="color: #5a6a85!important;">*opsional</p>
                        </div>

                        <!-- 5.	Nilai Mata Kuliah -->
                        <h5 class="fw-bolder mb-3">5. Nilai CPMK</h5>
                        <div class="form-group mb-4">
                            <input type="file" class="form-control mb-2" id="nilai_cpmk" name="nilai_cpmk" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*wajib diisi</p>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/pelaksanaan-perkuliahan') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <a class="btn btn-primary" href="<?= base_url('portofolio-form/nilai-cpmk') ?>">
                                Simpan <i class="ti ti-download"></i>
                            </a>
                            <!-- <button type="submit" class="btn btn-primary">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button> -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai CPMK -->
    <div class="row d-none">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Nilai Capaian Pembelajaran Mata Kuliah</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi nilai CPMK di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="rpsForm" action="<?= base_url('form/submit') ?>" method="post" enctype="multipart/form-data">
                        <!-- Tabel Header -->
                        <table class="table mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start" style="width: 350px;">Fakultas</th>
                                    <th class="text-end">:</th>
                                    <th>Fakultas Teknik</th>
                                </tr>
                                <tr>
                                    <th class="text-start" style="width: 350px;">Progdi</th>
                                    <th class="text-end">:</th>
                                    <th>Teknik Elektro - S1</th>
                                </tr>
                                <tr>
                                    <th class="text-start" style="width: 350px;">Kode Mata Kuliah</th>
                                    <th class="text-end">:</th>
                                    <th>E1144902</th>
                                </tr>
                                <tr>
                                    <th class="text-start" style="width: 350px;">Nama Mata Kuliah</th>
                                    <th class="text-end">:</th>
                                    <th>Sistem Robotika</th>
                                </tr>
                                <tr>
                                    <th class="text-start" style="width: 350px;">Kelompok</th>
                                    <th class="text-end">:</th>
                                    <th>01</th>
                                </tr>
                                <tr>
                                    <th class="text-start" style="width: 350px;">Jenis Mata Kuliah</th>
                                    <th class="text-end">:</th>
                                    <th>Teori</th>
                                </tr>
                                <tr>
                                    <th class="text-start" style="width: 350px;">Semester</th>
                                    <th class="text-end">:</th>
                                    <th>Genap</th>
                                </tr>
                                <tr>
                                    <th class="text-start" style="width: 350px;">Tahun Akademik</th>
                                    <th class="text-end">:</th>
                                    <th>2024/2025</th>
                                </tr>
                                <tr>
                                    <th class="text-start" style="width: 350px;">Nama Dosen</th>
                                    <th class="text-end">:</th>
                                    <th>Arga Dwi Pambudi, M.T.</th>
                                </tr>
                            </thead>
                        </table>

                        <!-- Tabel Data Mahasiswa -->
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>CPMK 1</th>
                                    <th>CPMK 2</th>
                                    <th>CPMK 3</th>
                                    <th>CPMK 4</th>
                                    <th>CPMK 5</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>123456</td>
                                    <td>Ahmad Zaki</td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox"></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>789012</td>
                                    <td>Rina Amalia</td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox"></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/pemetaan') ?>">
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
</div>

<?= $this->include('backend/partials/footer') ?>