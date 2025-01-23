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

                        <div class="step-line active"></div>

                        <!-- CPMK & Sub CPMK -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-book"></i>
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

                        <div class="step-line"></div>

                        <!-- Rancangan Assesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-checklist"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Assesmen</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Hasil Asesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-checklist"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Hasil Asesmen</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cetak -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Pemetaan CPL - CPMK - Sub CPMK</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mencetak file CPMK & Sub CPMK di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="rpsForm" action="<?= base_url('form/submit') ?>" method="post" enctype="multipart/form-data">
                        <?php if (!empty($pdfUrl)): ?>
                            <div class="mb-3" style="height: 600px; border: 1px solid #ccc; margin-top: 20px;">
                                <iframe src="<?= esc($pdfUrl) ?>" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                        <?php else: ?>
                        <?php endif; ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="text-white" style="background-color: #0f4c92;">
                                    <tr class="align-middle text-center">
                                        <th style="width: 20%" rowspan="2">CPL</th>
                                        <th style="width: 30%" rowspan="2">CPMK</th>
                                        <th colspan="5">Sub CPMK</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                        <th>5</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- CPL 1 -->
                                    <tr>
                                        <td rowspan="3" class="align-middle"><strong>CPL 1</strong></td>
                                        <td>Mengidentifikasi masalah dasar ...</td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_1[]" value="1"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_1[]" value="2"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_1[]" value="3"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_1[]" value="4"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_1[]" value="5"></td>
                                    </tr>
                                    <tr>
                                        <td>Menerapkan metode analisis ...</td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_2[]" value="1"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_2[]" value="2"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_2[]" value="3"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_2[]" value="4"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_2[]" value="5"></td>
                                    </tr>
                                    <tr>
                                        <td>Menyampaikan hasil analisis secara tertulis ...</td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_3[]" value="1"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_3[]" value="2"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_3[]" value="3"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_3[]" value="4"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_3[]" value="5"></td>
                                    </tr>

                                    <!-- CPL 2 -->
                                    <tr>
                                        <td rowspan="4" class="align-middle"><strong>CPL 2</strong></td>
                                        <td class="align-middle">Memiliki kemampuan untuk berkomunikasi ...</td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_4[]" value="1"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_4[]" value="2"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_4[]" value="3"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_4[]" value="4"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_4[]" value="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle">Memiliki kemampuan untuk berkomunikasi ...</td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_5[]" value="1"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_5[]" value="2"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_5[]" value="3"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_5[]" value="4"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_5[]" value="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle">Memiliki kemampuan untuk berkomunikasi ...</td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_6[]" value="1"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_6[]" value="2"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_6[]" value="3"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_6[]" value="4"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_6[]" value="5"></td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle">Memiliki kemampuan untuk berkomunikasi ...</td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_7[]" value="1"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_7[]" value="2"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_7[]" value="3"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_7[]" value="4"></td>
                                        <td class="text-center align-middle"><input type="checkbox" name="sub_cpmk_7[]" value="5"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/cpmk-subcpmk') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <a class="btn btn-primary" href="<?= base_url('portofolio-form/rancangan-asesmen') ?>">
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