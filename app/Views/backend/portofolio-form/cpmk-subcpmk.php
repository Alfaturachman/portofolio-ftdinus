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
                            <?php if (isset($idPorto)): ?>
                                <a class="btn btn-secondary" href="<?= base_url('portofolio-form/cpl-pi-edit/' . $idPorto) ?>">
                                    <i class="ti ti-arrow-left"></i> Kembali
                                </a>
                                <a class="btn btn-primary" href="<?= base_url('portofolio-form/pemetaan-edit/' . $idPorto) ?>">
                                    Selanjutnya <i class="ti ti-arrow-right"></i>
                                </a>
                            <?php else: ?>
                                <a class="btn btn-secondary" href="<?= base_url('portofolio-form/cpl-pi') ?>">
                                    <i class="ti ti-arrow-left"></i> Kembali
                                </a>
                                <a class="btn btn-primary" href="<?= base_url('portofolio-form/pemetaan') ?>">
                                    Selanjutnya <i class="ti ti-arrow-right"></i>
                                </a>
                            <?php endif; ?>
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

    function addCPMK() {
        const tbody = document.getElementById('cpmkTableBody');
        const newRow = `
        <tr class="table-light cpmk-row" data-cpmk="${cpmkCounter}">
            <td class="align-middle">
                <strong>CPMK ${cpmkCounter}</strong>
                <select class="form-select mt-2" name="cpmk[${cpmkCounter}][selectedCpl]" required>
                    <option value="" hidden>Pilih CPL</option>
                    <?php
                    if (isset($cplPiData)):
                        foreach ($cplPiData as $cplNo => $cplData):
                    ?>
                        <option value="<?= $cplNo ?>">CPL <?= $cplNo ?> - <?= substr($cplData['cpl_indo'], 0, 100) ?>...</option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" placeholder="Narasi CPMK ${cpmkCounter}" 
                    name="cpmk[${cpmkCounter}][narasi]" required>
            </td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="removeCPMK(this)">Hapus CPMK</button>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="sub-cpmk-wrapper" id="subCpmkWrapper${cpmkCounter}">
                </div>
                <button class="btn btn-sm btn-primary mb-3" onclick="addSubCPMK(${cpmkCounter})">
                    Tambah Sub CPMK
                </button>
            </td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', newRow);
        cpmkCounter = document.querySelectorAll('.cpmk-row').length + 1;
    }

    // Function to add new Sub CPMK (modified)
    function addSubCPMK(cpmkId) {
        const wrapper = document.getElementById(`subCpmkWrapper${cpmkId}`);
        const subRow = `
        <div class="row g-2 align-items-center mb-2">
            <div class="col-auto sub-cpmk-label">
                <strong class="d-flex align-items-center gap-2">
                    Sub CPMK 
                    <input type="text" class="form-control" style="width: 70px;" 
                        name="no_cpmk[${cpmkId}][sub][${globalSubCpmkCounter}]" value="${globalSubCpmkCounter}">
                </strong>
            </div>
            <div class="col">
                <input type="text" class="form-control" placeholder="Narasi Sub CPMK ${globalSubCpmkCounter}" 
                    name="cpmk[${cpmkId}][sub][${globalSubCpmkCounter}]">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-danger" onclick="removeSubCPMK(this, ${cpmkId})">Hapus</button>
            </div>
        </div>`;
        wrapper.insertAdjacentHTML('beforeend', subRow);
        globalSubCpmkCounter++;
    }

    // Hapus Sub CPMK dan perbarui penomoran
    function removeSubCPMK(button, cpmkId) {
        button.parentElement.parentElement.remove();
        updateAllSubCPMKNumbers();
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

    // Update all Sub CPMK numbers sequentially
    function updateAllSubCPMKNumbers() {
        let newCounter = 1;
        document.querySelectorAll('.sub-cpmk-wrapper').forEach(wrapper => {
            wrapper.querySelectorAll('.row').forEach(row => {
                const cpmkId = wrapper.id.replace('subCpmkWrapper', '');
                const numberInput = row.querySelector('input[name^="no_cpmk"]');
                const narasiInput = row.querySelector('input[name^="cpmk"]');

                // Update the number input value
                numberInput.value = newCounter;
                numberInput.setAttribute('name', `no_cpmk[${cpmkId}][sub][${newCounter}]`);

                // Update the narasi input attributes
                narasiInput.setAttribute('placeholder', `Narasi Sub CPMK ${newCounter}`);
                narasiInput.setAttribute('name', `cpmk[${cpmkId}][sub][${newCounter}]`);

                newCounter++;
            });
        });
        globalSubCpmkCounter = newCounter;
    }

    // Modifikasi event listener untuk tombol Selanjutnya
    document.querySelector('a[href*="pemetaan"]').addEventListener('click', function(e) {
        e.preventDefault();

        // Collect all CPMK and sub-CPMK data
        const cpmkData = {};
        const cplData = {}; // Untuk menyimpan data CPL

        document.querySelectorAll('.cpmk-row').forEach((row, index) => {
            const cpmkNumber = index + 1; // Gunakan index + 1 sebagai nomor CPMK
            const narasi = row.querySelector('input[name^="cpmk["]').value;
            const selectedCpl = row.querySelector('select[name^="cpmk["]').value;
            const selectElement = row.querySelector('select[name^="cpmk["]');

            cpmkData[cpmkNumber] = {
                narasi: narasi,
                selectedCpl: selectedCpl,
                no_cpmk: cpmkNumber, // Tambahkan field no_cpmk
                sub: {}
            };

            // Collect CPL data from selected option
            if (selectedCpl && selectElement) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                if (selectedOption && !cplData[selectedCpl]) {
                    // Extract full text from option
                    let optionText = selectedOption.textContent.trim();

                    // Remove "CPL X - " prefix
                    const dashIndex = optionText.indexOf(' - ');
                    if (dashIndex !== -1) {
                        optionText = optionText.substring(dashIndex + 3);
                    }

                    // Remove trailing "..."
                    optionText = optionText.replace(/\.\.\.$/g, '');

                    cplData[selectedCpl] = {
                        narasi: optionText
                    };
                }
            }

            // Get sub-CPMK data for this CPMK
            const subWrapper = document.getElementById(`subCpmkWrapper${row.dataset.cpmk}`);
            if (subWrapper) {
                subWrapper.querySelectorAll('.row').forEach(subRow => {
                    const subNumber = subRow.querySelector('input[name^="no_cpmk["]').value;
                    const subNarasi = subRow.querySelector('input[name^="cpmk["][name*="sub"]').value;

                    if (subNumber && subNarasi) {
                        cpmkData[cpmkNumber].sub[subNumber] = subNarasi;
                    }
                });
            }
        });

        console.log('CPMK Data:', cpmkData);
        console.log('CPL Data:', cplData);

        // Send data to server
        fetch('<?= base_url('portofolio-form/saveCPMKToSession') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    cpmk: cpmkData,
                    cpl: cplData,
                    globalSubCpmkCounter: globalSubCpmkCounter
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Data berhasil disimpan:', data.message);
                    <?php if (isset($idPorto)): ?>
                        window.location.href = '<?= base_url('portofolio-form/pemetaan-edit/' . $idPorto) ?>';
                    <?php else: ?>
                        window.location.href = '<?= base_url('portofolio-form/pemetaan') ?>';
                    <?php endif; ?>
                } else {
                    alert('Gagal menyimpan data CPMK. Silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data.');
            });
    });

    // Function to load CPMK data from session
    function loadCPMKFromSession(data) {
        const tbody = document.getElementById('cpmkTableBody');
        tbody.innerHTML = '';

        if (!data || !data.cpmk || Object.keys(data.cpmk).length === 0) {
            addCPMK();
            return;
        }

        const cpmkData = data.cpmk;
        globalSubCpmkCounter = data.globalSubCpmkCounter || 1;

        Object.entries(cpmkData).forEach(([cpmkNumber, cpmkInfo]) => {
            // Add CPMK row
            const newRow = `
            <tr class="table-light cpmk-row" data-cpmk="${cpmkNumber}">
                <td class="align-middle">
                    <strong>CPMK ${cpmkNumber}</strong>
                    <select class="form-select mt-2" name="cpmk[${cpmkNumber}][selectedCpl]" required>
                        <option value="">Pilih CPL</option>
                        <?php
                        if (isset($cplPiData)):
                            foreach ($cplPiData as $cplNo => $cplData):
                        ?>
                            <option value="<?= $cplNo ?>" ${cpmkInfo.selectedCpl == '<?= $cplNo ?>' ? 'selected' : ''}>
                                CPL <?= $cplNo ?> - <?= substr($cplData['cpl_indo'], 0, 100) ?>...
                            </option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" 
                        name="cpmk[${cpmkNumber}][narasi]" 
                        value="${cpmkInfo.narasi || ''}" required>
                </td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="removeCPMK(this)">Hapus CPMK</button>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="sub-cpmk-wrapper" id="subCpmkWrapper${cpmkNumber}"></div>
                    <button class="btn btn-sm btn-primary mb-3" onclick="addSubCPMK(${cpmkNumber})">
                        Tambah Sub CPMK
                    </button>
                </td>
            </tr>`;

            tbody.insertAdjacentHTML('beforeend', newRow);

            // Add Sub CPMK rows if they exist
            if (cpmkInfo.sub && Object.keys(cpmkInfo.sub).length > 0) {
                const wrapper = document.getElementById(`subCpmkWrapper${cpmkNumber}`);
                Object.entries(cpmkInfo.sub).forEach(([subNumber, subNarasi]) => {
                    const subRow = `
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-auto sub-cpmk-label">
                            <strong class="d-flex align-items-center gap-2">
                                Sub CPMK 
                                <input type="text" class="form-control" style="width: 70px;" 
                                    name="no_cpmk[${cpmkNumber}][sub][${subNumber}]" value="${subNumber}">
                            </strong>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Narasi Sub CPMK ${subNumber}" 
                                name="cpmk[${cpmkNumber}][sub][${subNumber}]" value="${subNarasi}">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-danger" onclick="removeSubCPMK(this, ${cpmkNumber})">Hapus</button>
                        </div>
                    </div>`;
                    wrapper.insertAdjacentHTML('beforeend', subRow);
                });
            }
        });

        cpmkCounter = Object.keys(cpmkData).length + 1;
    }
</script>

<?= $this->include('backend/partials/footer') ?>