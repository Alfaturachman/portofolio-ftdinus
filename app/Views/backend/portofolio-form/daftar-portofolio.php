<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <div class="row pt-3">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-center">
                        <h4 class="fw-bolder mb-0">Portofolio Mata Kuliah <?= $kode_matkul ?> <?= isset($matkul['matkul']) ? $matkul['matkul'] : '' ?></h4>
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
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <a class="btn btn-secondary" href="<?= base_url('portofolio-form') ?>">
                                    Kembali
                                </a>
                            </div>
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" id="searchInput" placeholder="Cari...">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">Cari</button>
                            </div>
                        </div>

                        <div id="alert" class="alert alert-primary" role="alert">
                            Di bawah merupakan daftar portofolio untuk mata kuliah <?= $kode_matkul ?> - <?= isset($matkul['matkul']) ? $matkul['matkul'] : '' ?>!
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="portofolioTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 15%">Kode Mata Kuliah</th>
                                    <th style="width: 20%">Nama Mata Kuliah</th>
                                    <th style="width: 10%">NPP</th>
                                    <th style="width: 20%">Nama Dosen</th>
                                    <th style="width: 15%">Waktu Dibuat</th>
                                    <th style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                if (!empty($portofolioList)):
                                    foreach ($portofolioList as $porto): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $porto['kode_mk'] ?></td>
                                            <td><?= $porto['nama_mk'] ?></td>
                                            <td><?= $porto['npp'] ?></td>
                                            <td><?= $porto['nama'] ?></td>
                                            <td><?= formatTanggalIndo($porto['ins_time']) ?></td>
                                            <td>
                                                <div class="d-flex justify-content-around">
                                                    <a href="<?= base_url('portofolio-form/edit/' . $porto['id']) ?>" class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="<?= base_url('cetak-pdf/' . $porto['id']) ?>" class="btn btn-sm btn-primary" title="Cetak">
                                                        <i class="fas fa-print"></i> Cetak
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data portofolio</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');
        const table = document.getElementById('portofolioTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        function searchTable() {
            const filter = searchInput.value.toLowerCase();

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;

                    if (cellText.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }

                rows[i].style.display = found ? '' : 'none';
            }
        }

        searchButton.addEventListener('click', searchTable);
        searchInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchTable();
            }
        });
    });
</script>

<?= $this->include('backend/partials/footer') ?>