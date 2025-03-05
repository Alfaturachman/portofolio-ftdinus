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

                        <div class="step-line active"></div>

                        <!-- Pemetaan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
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

    <!-- Cetak -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Pemetaan CPL - CPMK - Sub CPMK</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mencetak file CPMK & Sub CPMK di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="rpsForm" action="<?= base_url('form/submit') ?>" method="post" enctype="multipart/form-data">
                        <?php if (!empty($pdfUrl)): ?>
                            <div class="mb-3" style="height: 600px; border: 1px solid #ccc; margin-top: 20px;">
                                <iframe src="<?= esc($pdfUrl) ?>" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                        <?php else: ?>
                        <?php endif; ?>
                        <!-- Inside the table-responsive div -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="text-white" style="background-color: #0f4c92;">
                                    <tr class="align-middle text-center">
                                        <th style="width: 20%" rowspan="2">CPL</th>
                                        <th style="width: 30%" rowspan="2">CPMK</th>
                                        <?php
                                        $cpmkData = session()->get('cpmk_data');
                                        // Get an array of all unique sub CPMK numbers
                                        $subCpmkNumbers = [];
                                        if (isset($cpmkData['cpmk'])) {
                                            foreach ($cpmkData['cpmk'] as $cpmk) {
                                                if (isset($cpmk['sub'])) {
                                                    foreach ($cpmk['sub'] as $subNo => $subData) {
                                                        if (!in_array($subNo, $subCpmkNumbers)) {
                                                            $subCpmkNumbers[] = $subNo;
                                                        }
                                                    }
                                                }
                                            }
                                            sort($subCpmkNumbers); // Sort the numbers in ascending order
                                        }
                                        $totalSubCpmk = count($subCpmkNumbers);
                                        ?>
                                        <th colspan="<?= $totalSubCpmk ?>">Sub CPMK</th>
                                    </tr>
                                    <tr class="text-center">
                                        <?php foreach ($subCpmkNumbers as $subNo): ?>
                                            <th><?= $subNo ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cplPiData = session()->get('cpl_pi_data');
                                    $cpmkData = session()->get('cpmk_data');
                                    $mappingData = session()->get('mapping_data') ?? [];

                                    if (empty($cplPiData) || empty($cpmkData['cpmk'])):
                                    ?>
                                        <tr>
                                            <td colspan="<?= $totalSubCpmk + 2 ?>" class="text-center">Tidak ada data pemetaan yang tersedia.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($cplPiData as $cplNo => $cplData): ?>
                                            <?php
                                            // Get CPMK data for this CPL from session
                                            $relatedCpmk = [];
                                            foreach ($cpmkData['cpmk'] as $cpmkNo => $cpmkInfo) {
                                                if (isset($cpmkInfo['selectedCpl']) && $cpmkInfo['selectedCpl'] == $cplNo) {
                                                    $relatedCpmk[$cpmkNo] = $cpmkInfo;
                                                }
                                            }

                                            $rowspan = max(count($relatedCpmk), 1);
                                            $isFirstRow = true;
                                            ?>

                                            <?php if (empty($relatedCpmk)): ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <strong>CPL <?= $cplNo ?></strong><br>
                                                        <?= esc($cplData['narasi'] ?? '') ?>
                                                    </td>
                                                    <td>-</td>
                                                    <?php foreach ($subCpmkNumbers as $subNo): ?>
                                                        <td class="text-center">-</td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($relatedCpmk as $cpmkNo => $cpmkInfo): ?>
                                                    <tr>
                                                        <?php if ($isFirstRow): ?>
                                                            <td rowspan="<?= $rowspan ?>" class="align-middle">
                                                                <strong>CPL <?= $cplNo ?></strong>
                                                            </td>
                                                        <?php endif; ?>

                                                        <td class="align-middle">
                                                            <strong>CPMK <?= $cpmkNo ?></strong>
                                                        </td>

                                                        <?php foreach ($subCpmkNumbers as $subNo): ?>
                                                            <td class="text-center align-middle">
                                                                <?php
                                                                $isChecked = false;
                                                                // Check if mapping exists in session data
                                                                if (isset($mappingData->$cplNo->$cpmkNo->$subNo)) {
                                                                    $isChecked = $mappingData->$cplNo->$cpmkNo->$subNo == 1;
                                                                }
                                                                ?>
                                                                <input type="checkbox"
                                                                    class="mapping-checkbox"
                                                                    name="mapping[<?= $cplNo ?>][<?= $cpmkNo ?>][<?= $subNo ?>]"
                                                                    <?= $isChecked ? 'checked' : '' ?>>
                                                            </td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                    <?php $isFirstRow = false; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Replace the anchor tag with a submit button -->
                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/cpmk-subcpmk') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                Selanjutnya <i class="ti ti-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript to handle checkbox changes -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('rpsForm');
        const checkboxes = document.querySelectorAll('.mapping-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const mappingData = collectMappingData();
                saveMappingToSession(mappingData);
            });
        });

        function collectMappingData() {
            const mappingData = {};

            checkboxes.forEach(checkbox => {
                const name = checkbox.getAttribute('name');
                const matches = name.match(/mapping\[(\d+)\]\[(\d+)\]\[(\d+)\]/);

                if (matches) {
                    const [, cpl, cpmk, subCpmk] = matches;

                    if (!mappingData[cpl]) mappingData[cpl] = {};
                    if (!mappingData[cpl][cpmk]) mappingData[cpl][cpmk] = {};

                    mappingData[cpl][cpmk][subCpmk] = checkbox.checked ? 1 : 0;
                }
            });

            return mappingData;
        }

        function saveMappingToSession(mappingData) {
            fetch('<?= base_url('portofolio-form/saveMappingToSession') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        mapping: mappingData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Pemetaan berhasil disimpan.');
                    } else {
                        console.error('Gagal menyimpan:', data.message);
                        alert(`Gagal menyimpan data pemetaan: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data pemetaan.');
                });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const mappingData = collectMappingData();

            fetch('<?= base_url('portofolio-form/saveMappingToSession') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        mapping: mappingData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '<?= base_url('portofolio-form/rancangan-asesmen') ?>';
                    } else {
                        alert(`Gagal menyimpan data pemetaan: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data pemetaan.');
                });
        });
    });
</script>

<?= $this->include('backend/partials/footer') ?>