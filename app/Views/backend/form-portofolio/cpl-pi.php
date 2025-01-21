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
                        <!-- Info Matkul -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-bookmark"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Info Matkul</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Topik -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active data-step=" topik">
                                <i class="ti ti-analyze"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Topik</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- CPL & PI -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-chart-line"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPL & PI</small>
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
                                <i class="ti ti-report-analytics"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Pemetaan</small>
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

    <!-- CPL & IKCP -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h5 class="card-title fw-bolder mb-3">Capaian Pembelajaran Lulusan (CPL) & Performa Index (PI)</h5>
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
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/topik-perkuliahan') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <a class="btn btn-primary" href="<?= base_url('portofolio-form/cpmk-subcpmk') ?>">
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