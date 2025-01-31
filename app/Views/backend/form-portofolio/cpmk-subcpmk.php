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
        .d-flex.justify-content-between.align-items-baseline {
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .step-line {
            display: none;
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

                        <!-- Informasi Matkul -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-bookmark"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Informasi Matkul</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- CPL & PI -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-bulb"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPL & PI</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- CPMK & Sub CPMK -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-list-details"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPMK & Sub</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Pemetaan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-report-analytics"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Pemetaan</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Rancangan Assesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-file-text"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Assesmen</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Pelaksanaan Perkuliahan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-school"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Pelaksanaan Perkuliahan</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Hasil Asesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-checklist"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Hasil Asesmen</small>
                        </div>
                        
                        <div class="step-line"></div>

                        <!-- Evaluasi Perkuliahan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
                                <i class="ti ti-chart-bar"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Evaluasi Perkuliahan</small>
                        </div>
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
                        <h4 class="fw-bolder mb-3">Capaian Pembelajaran Mata Kuliah (CPMK) & Sub Capaian Pembelajaran Mata Kuliah (Sub CPMK)</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi CPMK & Sub CPMK di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <?php if (!empty($pdfUrl)): ?>
                        <div class="mb-3" style="height: 600px; border: 1px solid #ccc; margin-top: 20px;">
                            <iframe src="<?= esc($pdfUrl) ?>" width="100%" height="100%" style="border: none;"></iframe>
                        </div>
                    <?php else: ?>
                    <?php endif; ?>

                    <table class="table table-bordered">
                        <thead class="text-white" style="background-color: #0f4c92;">
                            <tr>
                                <th style="width: 30%">CPMK</th>
                                <th style="width: 70%">Narasi</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cpmkTableBody">
                            <!-- CPMK rows will be dynamically added here -->
                        </tbody>
                    </table>
                    <button class="btn btn-success" onclick="addCPMK()">Tambah CPMK</button>
                    <form id="topicForm" action="<?= base_url('form/submit') ?>" method="post">
                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/cpl-pi') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <a class="btn btn-primary" href="<?= base_url('portofolio-form/pemetaan') ?>">
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

<script>
    let cpmkCounter = 1;
    let globalSubCpmkCounter = 1; // Add global counter for Sub CPMK

    // Function to get the total count of existing Sub CPMKs
    function getTotalSubCPMKCount() {
        let total = 0;
        document.querySelectorAll('.sub-cpmk-wrapper').forEach(wrapper => {
            total += wrapper.childElementCount;
        });
        return total;
    }

    // Tambahkan CPMK baru
    function addCPMK() {
        const tbody = document.getElementById('cpmkTableBody');
        const newRow = `
        <tr class="table-light cpmk-row" data-cpmk="${cpmkCounter}">
            <td class="align-middle"><strong>CPMK ${cpmkCounter}</strong></td>
            <td><input type="text" class="form-control" placeholder="Narasi CPMK ${cpmkCounter}" name="cpmk[${cpmkCounter}][narasi]"></td>
            <td><button class="btn btn-sm btn-danger" onclick="removeCPMK(this)">Hapus CPMK</button></td>
        </tr>
        <tr>
            <td colspan="3">
                <button class="btn btn-sm btn-primary mb-3" onclick="addSubCPMK(${cpmkCounter})">Tambah Sub CPMK</button>
                <div class="sub-cpmk-wrapper" id="subCpmkWrapper${cpmkCounter}">
                    <!-- Sub CPMK rows will be appended here -->
                </div>
            </td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', newRow);
        cpmkCounter = document.querySelectorAll('.cpmk-row').length + 1;
    }

    // Tambahkan Sub CPMK pada CPMK tertentu
    function addSubCPMK(cpmkId) {
        const wrapper = document.getElementById(`subCpmkWrapper${cpmkId}`);
        globalSubCpmkCounter = getTotalSubCPMKCount() + 1;
        const subRow = `
        <div class="row g-2 align-items-center mb-2">
            <div class="col-auto sub-cpmk-label">
                <strong>
                    Sub CPMK 
                    <input type="text" class="form-control" name="no_cpmk[${cpmkId}][sub][${globalSubCpmkCounter}]" value="${globalSubCpmkCounter}">
                </strong>
            </div>
            <div class="col">
                <input type="text" class="form-control" placeholder="Narasi Sub CPMK ${globalSubCpmkCounter}" name="cpmk[${cpmkId}][sub][${globalSubCpmkCounter}]">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-danger" onclick="removeSubCPMK(this, ${cpmkId})">Hapus</button>
            </div>
        </div>`;
        wrapper.insertAdjacentHTML('beforeend', subRow);
    }

    // Hapus Sub CPMK dan perbarui penomoran
    function removeSubCPMK(button, cpmkId) {
        button.parentElement.parentElement.remove();
        updateAllSubCPMKNumbers();
    }

    // Update all Sub CPMK numbers sequentially
    function updateAllSubCPMKNumbers() {
        let newCounter = 1;
        document.querySelectorAll('.sub-cpmk-wrapper').forEach(wrapper => {
            wrapper.querySelectorAll('.row').forEach(row => {
                const cpmkId = wrapper.id.replace('subCpmkWrapper', '');
                const subCpmkLabel = row.querySelector('.sub-cpmk-label strong');
                const inputField = row.querySelector('input');
                
                subCpmkLabel.textContent = `Sub CPMK ${newCounter}`;
                inputField.setAttribute('placeholder', `Narasi Sub CPMK ${newCounter}`);
                inputField.setAttribute('name', `cpmk[${cpmkId}][sub][${newCounter}]`);
                newCounter++;
            });
        });
        globalSubCpmkCounter = newCounter;
    }

    // Hapus CPMK beserta Sub CPMK-nya
    function removeCPMK(button) {
        const cpmkRow = button.closest('tr');
        const cpmkId = cpmkRow.dataset.cpmk;
        
        cpmkRow.nextElementSibling.remove();
        cpmkRow.remove();
        
        updateCPMKNumbers();
        updateAllSubCPMKNumbers();
        cpmkCounter = document.querySelectorAll('.cpmk-row').length + 1;
    }

    // Update CPMK numbers
    function updateCPMKNumbers() {
        const cpmkRows = document.querySelectorAll('.cpmk-row');
        cpmkRows.forEach((row, index) => {
            const cpmkNumber = index + 1;
            const cpmkLabel = row.querySelector('td strong');
            const cpmkInput = row.querySelector('input');
            
            cpmkLabel.textContent = `CPMK ${cpmkNumber}`;
            cpmkInput.setAttribute('placeholder', `Narasi CPMK ${cpmkNumber}`);
            cpmkInput.setAttribute('name', `cpmk[${cpmkNumber}][narasi]`);
            row.dataset.cpmk = cpmkNumber;
            
            // Update wrapper ID
            const nextRow = row.nextElementSibling;
            const wrapper = nextRow.querySelector('.sub-cpmk-wrapper');
            wrapper.id = `subCpmkWrapper${cpmkNumber}`;
            
            // Update add button onclick
            const addButton = nextRow.querySelector('.btn-primary');
            addButton.setAttribute('onclick', `addSubCPMK(${cpmkNumber})`);
        });
    }

    // Load CPMK data from session when page loads
    document.addEventListener('DOMContentLoaded', function() {
        fetch('<?= base_url('portofolio-form/getCPMKFromSession') ?>')
            .then(response => response.json())
            .then(data => {
                if (data && Object.keys(data).length > 0) {
                    loadCPMKFromSession(data);
                } else {
                    addCPMK();
                }
            });
    });

    function loadCPMKFromSession(data) {
        const tbody = document.getElementById('cpmkTableBody');
        tbody.innerHTML = '';
        let subCpmkCounter = 1;
        
        Object.entries(data).forEach(([cpmkNumber, cpmkData]) => {
            const newRow = `
            <tr class="table-light cpmk-row" data-cpmk="${cpmkNumber}">
                <td class="align-middle"><strong>CPMK ${cpmkNumber}</strong></td>
                <td><input type="text" class="form-control" placeholder="Narasi CPMK ${cpmkNumber}" 
                    name="cpmk[${cpmkNumber}][narasi]" value="${cpmkData.narasi || ''}"></td>
                <td><button class="btn btn-sm btn-danger" onclick="removeCPMK(this)">Hapus CPMK</button></td>
            </tr>
            <tr>
                <td colspan="3">
                    <button class="btn btn-sm btn-primary mb-3" onclick="addSubCPMK(${cpmkNumber})">Tambah Sub CPMK</button>
                    <div class="sub-cpmk-wrapper" id="subCpmkWrapper${cpmkNumber}">
                    </div>
                </td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', newRow);

            if (cpmkData.sub) {
                Object.entries(cpmkData.sub).forEach(([_, subNarasi]) => {
                    const wrapper = document.getElementById(`subCpmkWrapper${cpmkNumber}`);
                    const subRow = `
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-auto sub-cpmk-label">
                            <strong>
                                Sub CPMK 
                                <input type="text" class="form-control" name="no_cpmk[${cpmkNumber}][sub][${subCpmkCounter}]" value="${subCpmkCounter}">
                            </strong>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Narasi Sub CPMK ${subCpmkCounter}" 
                                name="cpmk[${cpmkNumber}][sub][${subCpmkCounter}]" value="${subNarasi}">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-danger" onclick="removeSubCPMK(this, ${cpmkNumber})">Hapus</button>
                        </div>
                    </div>`;
                    wrapper.insertAdjacentHTML('beforeend', subRow);
                    subCpmkCounter++;
                });
            }
        });
        
        cpmkCounter = Object.keys(data).length + 1;
        globalSubCpmkCounter = subCpmkCounter;
    }

    // Modify the form submission to save to session
    document.querySelector('a[href*="pemetaan"]').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Collect form data
        const formData = new FormData();
        const cpmkData = {};
        
        document.querySelectorAll('.cpmk-row').forEach(row => {
            const cpmkNumber = row.dataset.cpmk;
            const narasi = row.querySelector('input[name^="cpmk["]').value;
            cpmkData[cpmkNumber] = { narasi: narasi, sub: {} };
            
            // Collect sub CPMK data
            const subWrapper = document.getElementById(`subCpmkWrapper${cpmkNumber}`);
            const subInputs = subWrapper.querySelectorAll('input');
            subInputs.forEach(input => {
                const subNumber = input.name.match(/\[sub\]\[(\d+)\]/)[1];
                cpmkData[cpmkNumber].sub[subNumber] = input.value;
            });
        });
        
        formData.append('cpmk', JSON.stringify(cpmkData));
        
        // Save to session
        fetch('<?= base_url('portofolio-form/saveCPMKToSession') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= base_url('portofolio-form/pemetaan') ?>';
            }
        });
    });
</script>

<?= $this->include('backend/partials/footer') ?>