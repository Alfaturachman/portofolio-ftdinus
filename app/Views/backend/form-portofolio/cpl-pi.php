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

    <!-- CPL & IKCP -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Capaian Pembelajaran Lulusan (CPL) & Performa Index (PI)</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi CPL dan indikator kinerja di bawah sebelum melanjutkan!
                        </div>
                    </div>
                    <form id="topicForm" action="<?= base_url('form/submit') ?>" method="post">
                        <?php if (!empty($pdfUrl)): ?>
                            <div class="mb-3" style="height: 600px; border: 1px solid #ccc; margin-top: 20px;">
                                <iframe src="<?= esc($pdfUrl) ?>" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                        <?php else: ?>
                        <?php endif; ?>
                        <table class="table table-bordered">
                            <thead class="text-white" style="background-color: #0f4c92;">
                                <tr>
                                    <th style="width: 30%" colspan="2">Capaian Pembelajaran Lulusan</th>
                                    <th style="width: 60%">Performa Index</th>
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
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/info-matkul') ?>">
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