<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <div class="row pt-3">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-center">
                        <h4 class="fw-bolder mb-0">Detail Portofolio Mata Kuliah</h4>
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

                    <table class="table table-bordered" id="portofolio-table">
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
                        <tbody id="portofolio-tbody">
                            <?php if (isset($portofolioList) && !empty($portofolioList)): ?>
                                <?php foreach ($portofolioList as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($item['kode_mk']) ?></td>
                                        <td><?= esc($item['nama_mk']) ?></td>
                                        <td><?= esc($item['dosen_nama']) ?></td>
                                        <td><?= esc($item['npp']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($item['ins_time'])) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('portofolio-form/edit/' . $item['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data portofolio ditemukan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('backend/partials/footer') ?>