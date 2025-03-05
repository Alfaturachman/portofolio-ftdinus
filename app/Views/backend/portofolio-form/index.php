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

    <!-- Portofolio Table -->
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
                                <th style="width: 5%">No</th>
                                <th style="width: 15%">Kode Mata Kuliah</th>
                                <th style="width: 20%">Mata Kuliah</th>
                                <th style="width: 20%">Dosen Pembuat</th>
                                <th style="width: 15%">NPP Dosen</th>
                                <th style="width: 15%">Waktu Terbuat</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($portofolios as $portofolio): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $portofolio['kode_mk'] ?></td>
                                <td><?= $portofolio['nama_mk'] ?></td>
                                <td><?= $portofolio['dosen_nama'] ?></td>
                                <td><?= $portofolio['npp'] ?></td>
                                <td><?= date('d M Y H:i', strtotime($portofolio['ins_time'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('portofolio/edit/'.$portofolio['id']) ?>" class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        <a href="<?= base_url('portofolio/cetak/'.$portofolio['id']) ?>" class="btn btn-sm btn-primary">
                                            Cetak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('backend/partials/footer') ?>