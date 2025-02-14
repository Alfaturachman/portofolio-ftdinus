<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <div class="row pt-3">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-center">
                        <h4 class="fw-bolder mb-0">Portofolio Mata Kuliah</h4>
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

                        <div class="d-flex justify-content-start mb-3">
                            <a class="btn btn-primary" href="<?= base_url('portofolio-form/upload-rps') ?>">
                                Tambah Portofolio
                            </a>
                        </div>

                        <div id="alert" class="alert alert-primary" role="alert">
                            Di bawah merupakan portofolio mata kuliah!
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%">No</th>
                                <th style="width: 20%">Program Studi</th>
                                <th style="width: 70%">Mata Kuliah</th>
                                <th style="width: 30%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><strong>Teknik Elektro</strong></td>
                                <td><strong>Pemrograman Web</strong></td>
                                <td>
                                    <a class="btn btn-primary" href="<?= base_url('detail-portofolio') ?>">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><strong>Teknik Industri</strong></td>
                                <td><strong>Basis Data</strong></td>
                                <td>
                                    <a class="btn btn-primary" href="<?= base_url('detail-portofolio') ?>">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('backend/partials/footer') ?>