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

    <!-- Rancangan Asesmen -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-5">
                        <h4 class="fw-bolder mb-3">Rancangan Asesmen</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi rancangan asesmen di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="rpsForm" action="<?= base_url('form/submit') ?>" method="post" enctype="multipart/form-data">
                        <h5 class="fw-bolder mb-3">Rancangan Jadwal Assesmen</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead class="text-white" style="background-color: #0f4c92;">
                                    <tr class="align-middle text-center">
                                        <th>CPMK</th>
                                        <th>Sub CPMK</th>
                                        <th>TUGAS</th>
                                        <th>UTS</th>
                                        <th>UAS</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td class="align-middle" rowspan="2">CPMK 1</td>
                                    <td>Sub CPMK 1</td>
                                    <td class="text-center"><input type="checkbox" checked></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                </tr>
                                <tr>
                                    <td class="align-middle">Sub CPMK 2</td>
                                    <td class="text-center"><input type="checkbox" checked></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                </tr>
                                <tr>
                                    <td class="align-middle">CPMK 2</td>
                                    <td>Sub CPMK 3</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox" checked></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                </tr>
                                <tr>
                                    <td class="align-middle" rowspan="2">CPMK 3</td>
                                    <td>Sub CPMK 4</td>
                                    <td class="text-center"><input type="checkbox" checked></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                </tr>
                                <tr>
                                    <td class="align-middle">Sub CPMK 5</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox" checked></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                </tr>
                                <tr>
                                    <td class="align-middle">CPMK 4</td>
                                    <td>Sub CPMK 6</td>
                                    <td class="text-center"><input type="checkbox" checked></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                </tr>
                                <tr>
                                    <td class="align-middle" rowspan="3">CPMK n</td>
                                    <td>Sub CPMK 5</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="align-middle">Sub CPMK 6</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox" checked></td>
                                </tr>
                                <tr>
                                    <td class="align-middle">Sub CPMK n</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center"><input type="checkbox" checked></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Tugas -->
                        <h5 class="fw-bolder mb-3">1. Tugas</h5>
                        <div class="form-group mb-4">
                            <label for="soal_tugas" class="form-label">Soal</label>
                            <input type="file" class="form-control" id="soal_tugas" name="soal_tugas" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                            <label for="rubrik_tugas" class="form-label mt-2">Rubrik</label>
                            <input type="file" class="form-control" id="rubrik_tugas" name="rubrik_tugas" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                        </div>

                        <!-- Ujian Tengah Semester -->
                        <h5 class="fw-bolder mb-3">2. Ujian Tengah Semester</h5>
                        <div class="form-group mb-4">
                            <label for="soal_uts" class="form-label">Soal</label>
                            <input type="file" class="form-control" id="soal_uts" name="soal_uts" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                            <label for="rubrik_uts" class="form-label mt-2">Rubrik</label>
                            <input type="file" class="form-control" id="rubrik_uts" name="rubrik_uts" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                        </div>

                        <!-- Ujian Akhir Semester -->
                        <h5 class="fw-bolder mb-3">3. Ujian Akhir Semester</h5>
                        <div class="form-group mb-4">
                            <label for="soal_uas" class="form-label">Soal</label>
                            <input type="file" class="form-control" id="soal_uas" name="soal_uas" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                            <label for="rubrik_uas" class="form-label mt-2">Rubrik</label>
                            <input type="file" class="form-control" id="rubrik_uas" name="rubrik_uas" accept="application/pdf" required>
                            <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/pemetaan') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <a class="btn btn-primary" href="<?= base_url('portofolio-form/pelaksanaan-perkuliahan') ?>">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
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
</div>

<?= $this->include('backend/partials/footer') ?>