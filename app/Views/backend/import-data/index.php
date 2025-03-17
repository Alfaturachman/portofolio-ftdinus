<!-- CSS untuk drag and drop file upload -->
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
</style>

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
                                                    <p class="text-danger">*Mohon untuk mengunduh template pada tombol Download Template di bawah ini, agar sistem dapat menyimpan data dengan benar.</p>
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
                                                    <p class="text-danger">*Mohon untuk mengunduh template pada tombol Download Template di bawah ini, agar sistem dapat menyimpan data dengan benar.</p>
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
                                                    <p class="text-danger">*Mohon untuk mengunduh template pada tombol Download Template di bawah ini, agar sistem dapat menyimpan data dengan benar.</p>
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
    });
</script>

<?= $this->include('backend/partials/footer') ?>