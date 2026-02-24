<!-- CSS untuk drag and drop file upload dan DataTables -->
<style>
    .dropzone-wrapper {
        border: 2px dashed #ccc;
        border-radius: 10px;
        background-color: #f8f9fa;
        color: #6c757d;
        position: relative;
        min-height: 150px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
    }

    .dropzone-wrapper:hover {
        background-color: #f1f3f5;
        border-color: #6c757d;
    }

    .dropzone-wrapper.dragover {
        background-color: #e9ecef;
        border-color: #007bff;
    }

    .dropzone-desc {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        text-align: center;
    }

    .dropzone {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    /* Tambahan untuk modal */
    .modal-success .modal-header {
        background-color: #28a745;
        color: white;
    }

    .modal-error .modal-header {
        background-color: #dc3545;
        color: white;
    }

    /* DataTables styling */
    .table-responsive {
        margin-top: 20px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_pagination {
        margin-top: 15px;
    }

    .btn-action {
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .table thead {
        background-color: #f8f9fa;
    }
</style>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<?= $this->include('backend/partials/header') ?>

<div class="container-fluid">
    <div class="row pt-3">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-sm-flex d-block align-items-center justify-content-center">
                        <h4 class="fw-bolder mb-0">Import Data</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Data View -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#cpl-pi" role="tab">
                                <span class="d-none d-md-block">Import CPL dan PI</span>
                                <span class="d-block d-md-none"><i class="fas fa-file-upload"></i></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#mata-kuliah" role="tab">
                                <span class="d-none d-md-block">Import Mata Kuliah</span>
                                <span class="d-block d-md-none"><i class="fas fa-book"></i></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#mata-kuliah-diampu" role="tab">
                                <span class="d-none d-md-block">Import Mata Kuliah Diampu</span>
                                <span class="d-block d-md-none"><i class="fas fa-chalkboard-teacher"></i></span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content p-3">
                        <!-- CPL dan PI Tab -->
                        <div class="tab-pane active" id="cpl-pi" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">Import Data CPL dan PI</h5>
                                            <form action="<?= base_url('import-data/saveImportCplPi') ?>" method="post" enctype="multipart/form-data">
                                                <?= csrf_field() ?>
                                                <div class="mb-4">
                                                    <div class="dropzone-wrapper">
                                                        <div class="dropzone-desc">
                                                            <i class="fas fa-cloud-upload-alt fa-3x"></i>
                                                            <p>Pilih file Excel atau drag dan drop di sini</p>
                                                            <p class="text-muted small">Format file: .xlsx, .xls (max 50MB)</p>
                                                        </div>
                                                        <input type="file" name="file_cpl_pi" class="dropzone" accept=".xls,.xlsx" required>
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <p class="text-danger">*Mohon untuk mengunduh dan menggunakan template pada tombol Download Template di bawah ini, agar sistem dapat menyimpan data dengan benar.</p>
                                                </div>
                                                <div class="text-center">
                                                    <a href="<?= base_url('downloads/template_cpl_pi.xlsx') ?>" class="btn btn-outline-primary me-2">
                                                        <i class="fas fa-download me-1"></i> Download Template
                                                    </a>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-upload me-1"></i> Import Data
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data CPL dan PI Table -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">Data CPL dan PI</h5>
                                            <div class="table-responsive">
                                                <table id="cplPiTable" class="table table-striped table-hover"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Mata Kuliah</th>
                                                            <th>Kode Matkul</th>
                                                            <th>No CPL</th>
                                                            <th>CPL (Indonesia)</th>
                                                            <th>No PI</th>
                                                            <th>Isi PI</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mata Kuliah Tab -->
                        <div class="tab-pane" id="mata-kuliah" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">Import Data Mata Kuliah</h5>
                                            <form action="<?= base_url('import-data/saveImportMatkul') ?>" method="post" enctype="multipart/form-data">
                                                <?= csrf_field() ?>
                                                <div class="mb-4">
                                                    <div class="dropzone-wrapper">
                                                        <div class="dropzone-desc">
                                                            <i class="fas fa-cloud-upload-alt fa-3x"></i>
                                                            <p>Pilih file Excel atau drag dan drop di sini</p>
                                                            <p class="text-muted small">Format file: .xlsx, .xls (max 50MB)</p>
                                                        </div>
                                                        <input type="file" name="file_mata_kuliah" class="dropzone" accept=".xls,.xlsx" required>
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <p class="text-danger">*Mohon untuk mengunduh dan menggunakan template pada tombol Download Template di bawah ini, agar sistem dapat menyimpan data dengan benar.</p>
                                                </div>
                                                <div class="text-center">
                                                    <a href="<?= base_url('downloads/template_matkul.xlsx') ?>" class="btn btn-outline-primary me-2">
                                                        <i class="fas fa-download me-1"></i> Download Template
                                                    </a>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-upload me-1"></i> Import Data
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Mata Kuliah Table -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">Data Mata Kuliah</h5>
                                            <div class="table-responsive">
                                                <table id="matkulTable"
                                                    class="table table-striped table-hover"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Mata Kuliah</th>
                                                            <th>Kode Matkul</th>
                                                            <th>Kelompok</th>
                                                            <th>Semester</th>
                                                            <th>SKS</th>
                                                            <th>Kurikulum</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mata Kuliah Diampu Tab -->
                        <div class="tab-pane" id="mata-kuliah-diampu" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">Import Data Mata Kuliah Diampu</h5>
                                            <form action="<?= base_url('import-data/saveImportMatkulDiampu') ?>" method="post" enctype="multipart/form-data">
                                                <?= csrf_field() ?>
                                                <div class="mb-4">
                                                    <div class="dropzone-wrapper">
                                                        <div class="dropzone-desc">
                                                            <i class="fas fa-cloud-upload-alt fa-3x"></i>
                                                            <p>Pilih file Excel atau drag dan drop di sini</p>
                                                            <p class="text-muted small">Format file: .xlsx, .xls (max 50MB)</p>
                                                        </div>
                                                        <input type="file" name="file_mata_kuliah_diampu" class="dropzone" accept=".xls,.xlsx" required>
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <p class="text-danger">*Mohon untuk mengunduh dan menggunakan template pada tombol Download Template di bawah ini, agar sistem dapat menyimpan data dengan benar.</p>
                                                </div>
                                                <div class="text-center">
                                                    <a href="<?= base_url('downloads/template_matkul_diampu.xlsx') ?>" class="btn btn-outline-primary me-2">
                                                        <i class="fas fa-download me-1"></i> Download Template
                                                    </a>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-upload me-1"></i> Import Data
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Mata Kuliah Diampu Table -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">Data Mata Kuliah Diampu</h5>
                                            <div class="table-responsive">
                                                <table id="matkulDiampuTable" class="table table-striped table-hover"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Mata Kuliah</th>
                                                            <th>Kode Matkul</th>
                                                            <th>Kelompok Matkul</th>
                                                            <th>Dosen</th>
                                                            <th>NPP</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk pesan sukses atau error -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <!-- Pesan akan ditampilkan di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal View CPL-PI -->
<div class="modal fade" id="viewCplPiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail CPL dan PI</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Mata Kuliah</th>
                        <td id="viewMatkul"></td>
                    </tr>
                    <tr>
                        <th>Kode Matkul</th>
                        <td id="viewKodeMatkul"></td>
                    </tr>
                    <tr>
                        <th>No CPL</th>
                        <td id="viewNoCpl"></td>
                    </tr>
                    <tr>
                        <th>CPL (Indonesia)</th>
                        <td id="viewCplIndo"></td>
                    </tr>
                    <tr>
                        <th>CPL (Inggris)</th>
                        <td id="viewCplInggris"></td>
                    </tr>
                    <tr>
                        <th>No PI</th>
                        <td id="viewNoPi"></td>
                    </tr>
                    <tr>
                        <th>Isi PI</th>
                        <td id="viewIsiPi"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit CPL-PI -->
<div class="modal fade" id="editCplPiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit CPL dan PI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCplPiForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="editCplPiId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Mata Kuliah</label>
                        <input type="text" class="form-control" id="editMatkul" name="matkul">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Matkul</label>
                        <input type="text" class="form-control" id="editKodeMatkul" name="kode_matkul" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No CPL</label>
                            <input type="text" class="form-control" id="editNoCpl" name="no_cpl" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No PI</label>
                            <input type="text" class="form-control" id="editNoPi" name="no_pi" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CPL (Indonesia)</label>
                        <textarea class="form-control" id="editCplIndo" name="cpl_indo" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CPL (Inggris)</label>
                        <textarea class="form-control" id="editCplInggris" name="cpl_inggris" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi PI</label>
                        <textarea class="form-control" id="editIsiPi" name="isi_pi" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Mata Kuliah -->
<div class="modal fade" id="viewMatkulModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Mata Kuliah</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Mata Kuliah</th>
                        <td id="viewMatakuliah"></td>
                    </tr>
                    <tr>
                        <th>Kode Matkul</th>
                        <td id="viewMatkulKode"></td>
                    </tr>
                    <tr>
                        <th>Kelompok</th>
                        <td id="viewKelpMatkul"></td>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <td id="viewSmtMatkul"></td>
                    </tr>
                    <tr>
                        <th>Jenis</th>
                        <td id="viewJenisMatkul"></td>
                    </tr>
                    <tr>
                        <th>Tipe</th>
                        <td id="viewTipeMatkul"></td>
                    </tr>
                    <tr>
                        <th>SKS Teori</th>
                        <td id="viewTeori"></td>
                    </tr>
                    <tr>
                        <th>SKS Praktik</th>
                        <td id="viewPraktek"></td>
                    </tr>
                    <tr>
                        <th>Kurikulum</th>
                        <td id="viewKurikulum"></td>
                    </tr>
                    <tr>
                        <th>Prodi</th>
                        <td id="viewProdi"></td>
                    </tr>
                    <tr>
                        <th>Jenjang</th>
                        <td id="viewJenjang"></td>
                    </tr>
                    <tr>
                        <th>Fakultas</th>
                        <td id="viewFakultas"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Mata Kuliah -->
<div class="modal fade" id="editMatkulModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMatkulForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="editMatkulId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Mata Kuliah</label>
                        <input type="text" class="form-control" id="editMatakuliah" name="matakuliah" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Matkul</label>
                        <input type="text" class="form-control" id="editMatkulKode" name="kode_matkul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelompok Matkul</label>
                        <input type="text" class="form-control" id="editKelpMatkul" name="kelp_matkul">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Semester</label>
                            <input type="number" class="form-control" id="editSmtMatkul" name="smt_matkul" min="1" max="14">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis</label>
                            <select class="form-control" id="editJenisMatkul" name="jenis_matkul">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Wajib">Wajib</option>
                                <option value="Pilihan">Pilihan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipe</label>
                            <select class="form-control" id="editTipeMatkul" name="tipe_matkul">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="Teori">Teori</option>
                                <option value="Praktik">Praktik</option>
                                <option value="Teori & Praktik">Teori & Praktik</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKS Teori</label>
                            <input type="number" class="form-control" id="editTeori" name="teori" min="0" max="6">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKS Praktik</label>
                            <input type="number" class="form-control" id="editPraktek" name="praktek" min="0" max="6">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kurikulum</label>
                        <input type="text" class="form-control" id="editKurikulum" name="kurikulum" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prodi</label>
                            <input type="text" class="form-control" id="editProdi" name="prodi">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenjang</label>
                            <select class="form-control" id="editJenjang" name="jenjang">
                                <option value="">-- Pilih Jenjang --</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fakultas</label>
                        <input type="text" class="form-control" id="editFakultas" name="fakultas">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Mata Kuliah Diampu -->
<div class="modal fade" id="viewMatkulDiampuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Mata Kuliah Diampu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Mata Kuliah</th>
                        <td id="viewDiampuMatkul"></td>
                    </tr>
                    <tr>
                        <th>Kode Matkul</th>
                        <td id="viewDiampuKodeMatkul"></td>
                    </tr>
                    <tr>
                        <th>Kelompok</th>
                        <td id="viewDiampuKelpMatkul"></td>
                    </tr>
                    <tr>
                        <th>Dosen</th>
                        <td id="viewDiampuDosen"></td>
                    </tr>
                    <tr>
                        <th>NPP</th>
                        <td id="viewDiampuNpp"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Mata Kuliah Diampu -->
<div class="modal fade" id="editMatkulDiampuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Mata Kuliah Diampu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMatkulDiampuForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="editDiampuId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Mata Kuliah</label>
                        <input type="text" class="form-control" id="editDiampuMatkul" name="matkul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Matkul</label>
                        <input type="text" class="form-control" id="editDiampuKodeMatkul" name="kode_matkul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelompok Matkul</label>
                        <input type="text" class="form-control" id="editDiampuKelpMatkul" name="kelp_matkul">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosen</label>
                        <input type="text" class="form-control" id="editDiampuDosen" name="dosen" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NPP</label>
                        <input type="text" class="form-control" id="editDiampuNpp" name="npp" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data ini?</p>
                <p class="text-danger small">*Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Hapus</a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript untuk drag and drop file upload dan modal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropzoneWrappers = document.querySelectorAll('.dropzone-wrapper');

        // Highlight dropzone when file is dragged over it
        dropzoneWrappers.forEach(wrapper => {
            const dropzone = wrapper.querySelector('.dropzone');
            const dropzoneDesc = wrapper.querySelector('.dropzone-desc');

            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                wrapper.classList.add('dragover');
            });

            dropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                wrapper.classList.remove('dragover');
            });

            dropzone.addEventListener('drop', function(e) {
                wrapper.classList.remove('dragover');
            });

            // Update filename when file is selected
            dropzone.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    const fileSize = Math.round(this.files[0].size / 1024); // Convert to KB

                    const fileNameElement = document.createElement('p');
                    fileNameElement.innerHTML = `<strong>${fileName}</strong> (${fileSize} KB)`;

                    // Remove previous filename if exists
                    const existingFileName = wrapper.querySelector('.file-info');
                    if (existingFileName) {
                        existingFileName.remove();
                    }

                    fileNameElement.classList.add('file-info', 'mt-2');
                    wrapper.appendChild(fileNameElement);
                }
            });
        });

        // Menampilkan modal untuk pesan sukses atau error
        const showMessage = (message, isSuccess) => {
            const modal = document.getElementById('messageModal');
            const modalBody = document.getElementById('messageModalBody');
            const modalContent = modal.querySelector('.modal-content');
            const modalTitle = document.getElementById('messageModalLabel');

            // Set content dan styling berdasarkan tipe pesan
            if (isSuccess) {
                modalContent.classList.remove('modal-error');
                modalContent.classList.add('modal-success');
                modalTitle.textContent = 'Sukses';
                modalTitle.innerHTML = '<i class="fas fa-check-circle me-2"></i>Sukses';
            } else {
                modalContent.classList.remove('modal-success');
                modalContent.classList.add('modal-error');
                modalTitle.textContent = 'Error';
                modalTitle.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Error';
            }

            modalBody.innerHTML = message;

            // Tampilkan modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        };

        // Cek apakah ada pesan sukses atau error dari session flash data
        <?php if (session()->getFlashdata('success')) : ?>
            showMessage('<?= session()->getFlashdata('success') ?>', true);
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            showMessage('<?= session()->getFlashdata('error') ?>', false);
        <?php endif; ?>

        // ==================== DataTables Initialization ====================

        // CPL-PI DataTable
        var cplPiTable = $('#cplPiTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url("import-data/getDataCplPi") ?>',
                type: 'GET',
                data: function(d) {
                    d.search = d.search.value;
                    d.length = d.length;
                    d.start = d.start;
                    d.order_column = d.columns[d.order[0].column].data;
                    d.order_dir = d.order[0].dir;
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'matkul'
                },
                {
                    data: 'kode_matkul'
                },
                {
                    data: 'no_cpl'
                },
                {
                    data: 'cpl_indo'
                },
                {
                    data: 'no_pi'
                },
                {
                    data: 'isi_pi'
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-info px-3" onclick="viewCplPi(${data})" title="View">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning px-3" onclick="editCplPi(${data})" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger px-3" onclick="deleteCplPi(${data})" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
            }
        });

        // Mata Kuliah DataTable
        var matkulTable = $('#matkulTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url("import-data/getDataMatkul") ?>',
                type: 'GET',
                data: function(d) {
                    d.search = d.search.value;
                    d.length = d.length;
                    d.start = d.start;
                    d.order_column = d.columns[d.order[0].column].data;
                    d.order_dir = d.order[0].dir;
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'matakuliah'
                },
                {
                    data: 'kode_matkul'
                },
                {
                    data: 'kelp_matkul'
                },
                {
                    data: 'smt_matkul'
                },
                {
                    data: null,
                    render: function(data) {
                        return data.teori + 'T + ' + data.praktek + 'P';
                    }
                },
                {
                    data: 'kurikulum'
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex gap-2">
                                <button class="btn btn-info px-3" onclick="viewMatkul(${data})" title="View">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-warning px-3" onclick="editMatkul(${data})" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-danger px-3" onclick="deleteMatkul(${data})" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
            }
        });

        // Mata Kuliah Diampu DataTable
        var matkulDiampuTable = $('#matkulDiampuTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url("import-data/getDataMatkulDiampu") ?>',
                type: 'GET',
                data: function(d) {
                    d.search = d.search.value;
                    d.length = d.length;
                    d.start = d.start;
                    d.order_column = d.columns[d.order[0].column].data;
                    d.order_dir = d.order[0].dir;
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'matkul'
                },
                {
                    data: 'kode_matkul'
                },
                {
                    data: 'kelp_matkul'
                },
                {
                    data: 'dosen'
                },
                {
                    data: 'npp'
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-info px-3" onclick="viewMatkulDiampu(${data})" title="View">
                                    <i class="ti ti-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning px-3" onclick="editMatkulDiampu(${data})" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger px-3" onclick="deleteMatkulDiampu(${data})" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
            }
        });

        // ==================== CPL-PI CRUD Functions ====================
        
        window.viewCplPi = function(id) {
            console.log('View CPL-PI ID:', id);
            fetch('<?= base_url("import-data/getCplPi/") ?>' + id)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById('viewMatkul').textContent = data.matkul || '-';
                    document.getElementById('viewKodeMatkul').textContent = data.kode_matkul || '-';
                    document.getElementById('viewNoCpl').textContent = data.no_cpl || '-';
                    document.getElementById('viewCplIndo').textContent = data.cpl_indo || '-';
                    document.getElementById('viewCplInggris').textContent = data.cpl_inggris || '-';
                    document.getElementById('viewNoPi').textContent = data.no_pi || '-';
                    document.getElementById('viewIsiPi').textContent = data.isi_pi || '-';
                    var modal = new bootstrap.Modal(document.getElementById('viewCplPiModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data');
                });
        }

        window.editCplPi = function(id) {
            console.log('Edit CPL-PI ID:', id);
            fetch('<?= base_url("import-data/getCplPi/") ?>' + id)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById('editCplPiId').value = data.id;
                    document.getElementById('editMatkul').value = data.matkul || '';
                    document.getElementById('editKodeMatkul').value = data.kode_matkul || '';
                    document.getElementById('editNoCpl').value = data.no_cpl || '';
                    document.getElementById('editNoPi').value = data.no_pi || '';
                    document.getElementById('editCplIndo').value = data.cpl_indo || '';
                    document.getElementById('editCplInggris').value = data.cpl_inggris || '';
                    document.getElementById('editIsiPi').value = data.isi_pi || '';
                    
                    document.getElementById('editCplPiForm').action = '<?= base_url("import-data/updateCplPi/") ?>' + id;
                    var modal = new bootstrap.Modal(document.getElementById('editCplPiModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data');
                });
        }

        window.deleteCplPi = function(id) {
            console.log('Delete CPL-PI ID:', id);
            document.getElementById('confirmDeleteBtn').href = '<?= base_url("import-data/deleteCplPi/") ?>' + id;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // ==================== Mata Kuliah CRUD Functions ====================
        
        window.viewMatkul = function(id) {
            console.log('View Mata Kuliah ID:', id);
            fetch('<?= base_url("import-data/getMatkul/") ?>' + id)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById('viewMatakuliah').textContent = data.matakuliah || '-';
                    document.getElementById('viewMatkulKode').textContent = data.kode_matkul || '-';
                    document.getElementById('viewKelpMatkul').textContent = data.kelp_matkul || '-';
                    document.getElementById('viewSmtMatkul').textContent = data.smt_matkul || '-';
                    document.getElementById('viewJenisMatkul').textContent = data.jenis_matkul || '-';
                    document.getElementById('viewTipeMatkul').textContent = data.tipe_matkul || '-';
                    document.getElementById('viewTeori').textContent = data.teori || '-';
                    document.getElementById('viewPraktek').textContent = data.praktek || '-';
                    document.getElementById('viewKurikulum').textContent = data.kurikulum || '-';
                    document.getElementById('viewProdi').textContent = data.prodi || '-';
                    document.getElementById('viewJenjang').textContent = data.jenjang || '-';
                    document.getElementById('viewFakultas').textContent = data.fakultas || '-';
                    var modal = new bootstrap.Modal(document.getElementById('viewMatkulModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data');
                });
        }

        window.editMatkul = function(id) {
            console.log('Edit Mata Kuliah ID:', id);
            fetch('<?= base_url("import-data/getMatkul/") ?>' + id)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById('editMatkulId').value = data.id;
                    document.getElementById('editMatakuliah').value = data.matakuliah || '';
                    document.getElementById('editMatkulKode').value = data.kode_matkul || '';
                    document.getElementById('editKelpMatkul').value = data.kelp_matkul || '';
                    document.getElementById('editSmtMatkul').value = data.smt_matkul || '';
                    document.getElementById('editJenisMatkul').value = data.jenis_matkul || '';
                    document.getElementById('editTipeMatkul').value = data.tipe_matkul || '';
                    document.getElementById('editTeori').value = data.teori || '';
                    document.getElementById('editPraktek').value = data.praktek || '';
                    document.getElementById('editKurikulum').value = data.kurikulum || '';
                    document.getElementById('editProdi').value = data.prodi || '';
                    document.getElementById('editJenjang').value = data.jenjang || '';
                    document.getElementById('editFakultas').value = data.fakultas || '';
                    
                    document.getElementById('editMatkulForm').action = '<?= base_url("import-data/updateMatkul/") ?>' + id;
                    var modal = new bootstrap.Modal(document.getElementById('editMatkulModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data');
                });
        }

        window.deleteMatkul = function(id) {
            console.log('Delete Mata Kuliah ID:', id);
            document.getElementById('confirmDeleteBtn').href = '<?= base_url("import-data/deleteMatkul/") ?>' + id;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // ==================== Mata Kuliah Diampu CRUD Functions ====================

        window.viewMatkulDiampu = function(id) {
            console.log('View Mata Kuliah Diampu ID:', id);
            fetch('<?= base_url("import-data/getMatkulDiampu/") ?>' + id)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById('viewDiampuMatkul').textContent = data.matkul || '-';
                    document.getElementById('viewDiampuKodeMatkul').textContent = data.kode_matkul || '-';
                    document.getElementById('viewDiampuKelpMatkul').textContent = data.kelp_matkul || '-';
                    document.getElementById('viewDiampuDosen').textContent = data.dosen || '-';
                    document.getElementById('viewDiampuNpp').textContent = data.npp || '-';
                    var modal = new bootstrap.Modal(document.getElementById('viewMatkulDiampuModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data');
                });
        }

        window.editMatkulDiampu = function(id) {
            console.log('Edit Mata Kuliah Diampu ID:', id);
            fetch('<?= base_url("import-data/getMatkulDiampu/") ?>' + id)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById('editDiampuId').value = data.id;
                    document.getElementById('editDiampuMatkul').value = data.matkul || '';
                    document.getElementById('editDiampuKodeMatkul').value = data.kode_matkul || '';
                    document.getElementById('editDiampuKelpMatkul').value = data.kelp_matkul || '';
                    document.getElementById('editDiampuDosen').value = data.dosen || '';
                    document.getElementById('editDiampuNpp').value = data.npp || '';

                    document.getElementById('editMatkulDiampuForm').action = '<?= base_url("import-data/updateMatkulDiampu/") ?>' + id;
                    var modal = new bootstrap.Modal(document.getElementById('editMatkulDiampuModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data');
                });
        }

        window.deleteMatkulDiampu = function(id) {
            console.log('Delete Mata Kuliah Diampu ID:', id);
            document.getElementById('confirmDeleteBtn').href = '<?= base_url("import-data/deleteMatkulDiampu/") ?>' + id;
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    });
</script>

<?= $this->include('backend/partials/footer') ?>