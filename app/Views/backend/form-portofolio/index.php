<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <div class="row pt-3">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-center">
                        <h5 class="fw-bolder mb-0">Portofolio Mata Kuliah</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CPMK & Sub CPMK -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h5 class="card-title fw-bolder mb-3">Portofolio Mata Kuliah</h5>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi CPMK & Sub CPMK di bawah sebelum melanjutkan!
                        </div>
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-primary" href="<?= base_url('portofolio-form/info-matkul') ?>">
                                Tambah Portofolio
                            </a>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-light">
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
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('backend/partials/footer') ?>