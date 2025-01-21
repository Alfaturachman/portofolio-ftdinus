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

                        <!-- Info Matkul -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-bookmark"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Info Matkul</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Topik -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active data-step=" topik">
                                <i class="ti ti-analyze"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Topik</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- CPL & PI -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-chart-line"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPL & PI</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- CPMK & Sub CPMK -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-book"></i>
                            </div>
                            <small class="d-block mt-2 step-label">CPMK & Sub</small>
                        </div>

                        <div class="step-line"></div>

                        <!-- Cetak -->
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
                                <i class="ti ti-checklist"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Assesmen</small>
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

                    <table class="table table-bordered">
                        <thead class="text-white" style="background-color: #0f4c92;">
                            <tr>
                                <th style="width: 30%">CPMK</th>
                                <th style="width: 70%">Narasi</th>
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

    // Tambahkan CPMK baru
    function addCPMK() {
        const tbody = document.getElementById('cpmkTableBody');
        const newRow = `
                <tr class="table-light cpmk-row" data-cpmk="${cpmkCounter}">
                    <td><strong>CPMK ${cpmkCounter}</strong></td>
                    <td><input type="text" class="form-control" placeholder="Narasi CPMK ${cpmkCounter}" name="cpmk[${cpmkCounter}][narasi]"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button class="btn btn-sm btn-primary mb-3" onclick="addSubCPMK(${cpmkCounter})">Tambah Sub CPMK</button>
                        <div class="sub-cpmk-wrapper" id="subCpmkWrapper${cpmkCounter}">
                            <!-- Sub CPMK rows will be appended here -->
                        </div>
                    </td>
                </tr>`;
        tbody.insertAdjacentHTML('beforeend', newRow);
        cpmkCounter++;
    }

    // Tambahkan Sub CPMK pada CPMK tertentu
    function addSubCPMK(cpmkId) {
        const wrapper = document.getElementById(`subCpmkWrapper${cpmkId}`);
        const subCpmkCount = wrapper.childElementCount + 1;
        const subRow = `
                <div class="row g-2 align-items-center mb-2">
                    <div class="col-auto sub-cpmk-label">
                        <strong>Sub CPMK ${subCpmkCount}</strong>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Narasi Sub CPMK ${subCpmkCount}" name="cpmk[${cpmkId}][sub][${subCpmkCount}]">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-danger" onclick="removeSubCPMK(this, ${cpmkId})">Hapus</button>
                    </div>
                </div>`;
        wrapper.insertAdjacentHTML('beforeend', subRow);
    }

    // Hapus Sub CPMK dan perbarui penomoran
    function removeSubCPMK(button, cpmkId) {
        const wrapper = document.getElementById(`subCpmkWrapper${cpmkId}`);
        button.parentElement.parentElement.remove(); // Hapus elemen Sub CPMK

        // Perbarui penomoran Sub CPMK
        const subRows = wrapper.querySelectorAll('.row');
        subRows.forEach((row, index) => {
            const subCpmkLabel = row.querySelector('.sub-cpmk-label strong');
            const inputField = row.querySelector('input');
            const subIndex = index + 1;

            // Update label dan placeholder
            subCpmkLabel.textContent = `Sub CPMK ${subIndex}`;
            inputField.setAttribute('placeholder', `Narasi Sub CPMK ${subIndex}`);
            inputField.setAttribute('name', `cpmk[${cpmkId}][sub][${subIndex}]`);
        });
    }
</script>

<?= $this->include('backend/partials/footer') ?>