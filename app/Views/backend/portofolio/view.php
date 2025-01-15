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

    /* Responsivitas */
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
                    <div class="d-flex justify-content-between align-items-baseline mt-4">
                        <!-- Info Matkul -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-bookmark"></i> <!-- Ikon Buku untuk Info Matkul -->
                            </div>
                            <small class="d-block mt-2 step-label">Info Matkul</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Topik -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-analyze"></i> <!-- Ikon Gear untuk Topik -->
                            </div>
                            <small class="d-block mt-2 step-label">Topik</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- CPL & Indikator -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-chart-line"></i> <!-- Ikon Grafik untuk CPL & Indikator -->
                            </div>
                            <small class="d-block mt-2 step-label">CPL & Indikator</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- CPMK & Sub CPMK -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-book"></i> <!-- Ikon Target untuk CPMK & Sub CPMK -->
                            </div>
                            <small class="d-block mt-2 step-label">CPMK & Sub CPMK</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Cetak -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-printer"></i> <!-- Ikon Printer untuk Cetak -->
                            </div>
                            <small class="d-block mt-2 step-label">Cetak</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Upload RPS -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-upload"></i> <!-- Ikon Upload untuk Upload RPS -->
                            </div>
                            <small class="d-block mt-2 step-label">Upload RPS</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Rancangan Assesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-award"></i> <!-- Ikon Centang untuk Rancangan Assesmen -->
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Assesmen</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">

                    <form action="<?= base_url('form/submit') ?>" method="post">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Enter your message" required></textarea>
                        </div>
                        <div class="d-flex justify-content-between pt-3">
                            <button type="submit" class="btn btn-secondary">Kembali</button>
                            <button type="submit" class="btn btn-primary">Selanjutnya <i class="ti ti-arrow-right"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('backend/partials/footer') ?>