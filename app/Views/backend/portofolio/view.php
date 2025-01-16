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
                            <div class="step-circle" data-step="info_matkul">
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
    <div class="row" data-step="info_matkul">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h5 class="card-title fw-bolder mb-3">Informasi Mata Kuliah</h5>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi informasi mata kuliah di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form>
                        <div class="form-group mb-3">
                            <label for="nama_mk" class="form-label">Nama Mata Kuliah</label>
                            <input type="text" class="form-control" id="nama_mk" name="nama_mk" placeholder="Masukkan nama mata kuliah" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="kode_mk" class="form-label">Kode MK</label>
                            <input type="text" class="form-control" id="kode_mk" name="kode_mk" placeholder="Masukkan kode mata kuliah" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="kelompok_mk" class="form-label">Kelompok MK</label>
                            <input type="text" class="form-control" id="kelompok_mk" name="kelompok_mk" placeholder="Masukkan kelompok mata kuliah" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="sks" class="form-label">SKS</label>
                            <div class="d-flex">
                                <input type="number" class="form-control me-2" id="sks_teori" name="sks_teori" placeholder="SKS Teori" required>
                                <input type="number" class="form-control" id="sks_praktik" name="sks_praktik" placeholder="SKS Praktik" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="mk_prasyarat" class="form-label">MK Prasyarat</label>
                            <textarea class="form-control" id="mk_prasyarat" name="mk_prasyarat" rows="3" placeholder="Masukkan mata kuliah prasyarat jika ada"></textarea>
                        </div>
                        <div class="d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-primary" data-current-step="info_matkul" data-next-step="topik">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Topik Perkuliahan -->
    <div class="row d-none" data-step="topik">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h5 class="card-title fw-bolder mb-3">Topik Perkuliahan</h5>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi topik perkuliahan di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="topicForm" action="<?= base_url('form/submit') ?>" method="post">
                        <div class="form-group mb-3">
                            <label for="topik_mk" class="form-label">Topik Perkuliahan</label>
                            <textarea class="form-control" id="topik_mk" name="topik_mk" rows="3" placeholder="Masukkan topik perkuliahan"></textarea>
                        </div>
                        <div class="d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </button>
                            <button type="button" id="submitBtn" class="btn btn-primary">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CPL & IKCP -->
    <div class="row d-none">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h5 class="card-title fw-bolder mb-3">Capaian Pembelajaran Lulusan (CPL) & Indikator Kinerja Capaian Pembelajaran (IKCP)</h5>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi CPL dan indikator kinerja di bawah sebelum melanjutkan!
                        </div>
                    </div>
                    <form id="topicForm" action="<?= base_url('form/submit') ?>" method="post">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 30%" colspan="2">CPL</th>
                                    <th style="width: 60%">IKCP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="3" style="white-space: nowrap;"><strong>CPL 1</strong></td>
                                    <td rowspan="3">Mampu menguasai konsep dasar...</td>
                                    <td>Mengidentifikasi masalah dasar ...</td>
                                </tr>
                                <tr>
                                    <td>Menerapkan metode analisis ...</td>
                                </tr>
                                <tr>
                                    <td>Menyampaikan hasil analisis secara tertulis ...</td>
                                </tr>

                                <tr>
                                    <td rowspan="4" style="white-space: nowrap;"><strong>CPL 2</strong></td>
                                    <td rowspan="4">Memiliki kemampuan untuk berkomunikasi ...</td>
                                    <td>Berkomunikasi secara efektif dalam tim ...</td>
                                </tr>
                                <tr>
                                    <td>Menyusun laporan sesuai standar ...</td>
                                </tr>
                                <tr>
                                    <td>Menggunakan teknologi untuk kolaborasi ...</td>
                                </tr>
                                <tr>
                                    <td>Memahami etika komunikasi profesional ...</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </button>
                            <button type="button" id="submitBtn" class="btn btn-primary">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CPMK & Sub CPMK -->
    <div class="row d-none">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h5 class="card-title fw-bolder mb-3">Capaian Pembelajaran Mata Kuliah (CPMK) & Sub Capaian Pembelajaran Mata Kuliah (Sub CPMK)</h5>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi CPMK & Sub CPMK di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="topicForm" action="<?= base_url('form/submit') ?>" method="post">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 30%">CPMK</th>
                                    <th style="width: 70%">Narasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- CPMK 1 -->
                                <tr class="table-light">
                                    <td><strong>CPMK 1</strong></td>
                                    <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit laboris.</td>
                                </tr>
                                <tr>
                                    <td><i>Sub CPMK 1</i></td>
                                    <td><i>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</i></td>
                                </tr>
                                <!-- CPMK 2 -->
                                <tr class="table-light">
                                    <td><strong>CPMK 2</strong></td>
                                    <td>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</td>
                                </tr>
                                <tr>
                                    <td><i>Sub CPMK 2</i></td>
                                    <td><i>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore.</i></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </button>
                            <button type="button" id="submitBtn" class="btn btn-primary">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cetak -->
    <div class="row d-none">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h5 class="card-title fw-bolder mb-3">Cetak</h5>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mencetak file CPMK & Sub CPMK di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="rpsForm" action="<?= base_url('form/submit') ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group mb-2">
                            <label for="rps_file" class="form-label">Cetak File CPMK & Sub CPMK</label>
                            <input type="file" class="form-control" id="rps_file" name="rps_file" accept="application/pdf" required>
                        </div>
                        <p class="text-danger">*format file: PDF</p>
                        <div class="d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload RPS -->
    <div class="row d-none">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h5 class="card-title fw-bolder mb-3">Upload RPS</h5>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengupload file RPS (PDF) di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="rpsForm" action="<?= base_url('form/submit') ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group mb-2">
                            <label for="rps_file" class="form-label">Upload File RPS (PDF)</label>
                            <input type="file" class="form-control" id="rps_file" name="rps_file" accept="application/pdf" required>
                        </div>
                        <p class="text-danger">*format file: PDF</p>
                        <div class="d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rancangan Asesmen -->
    <div class="row d-none">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h5 class="card-title fw-bolder mb-3">Rancangan Asesmen</h5>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi rancangan asesmen di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="rpsForm" action="<?= base_url('form/submit') ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label for="rps_file" class="form-label"><strong>1.</strong> Tugas</label>
                            <input type="file" class="form-control" id="rps_file" name="rps_file" accept="application/pdf" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="rps_file" class="form-label"><strong>2.</strong> Ujian Tengah Semester</label>
                            <input type="file" class="form-control" id="rps_file" name="rps_file" accept="application/pdf" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="rps_file" class="form-label"><strong>3.</strong> Ujian Akhir Semester</label>
                            <input type="file" class="form-control" id="rps_file" name="rps_file" accept="application/pdf" required>
                        </div>
                        <div class="d-flex justify-content-between pt-3">
                            <button type="button" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('') ?>backend/src/assets/js/form.js"></script>

<?= $this->include('backend/partials/footer') ?>