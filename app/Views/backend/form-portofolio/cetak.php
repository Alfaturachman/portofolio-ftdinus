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
                    <div id="steps" class="d-flex justify-content-between align-items-baseline">
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

                        <!-- CPL & Indikator -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-chart-line"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPL & Indikator</small>
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

                        <!-- Cetak -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
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

    <!-- Cetak -->
    <div class="row">
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
</div>

<?= $this->include('backend/partials/footer') ?>