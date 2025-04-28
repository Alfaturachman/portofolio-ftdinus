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
                        <!-- Step navigation - keep the same as in rancangan-asesmen -->
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

                        <div class="step-line active"></div>

                        <!-- Rancangan Assesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-file-text"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Assesmen</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Rancangan Soal (new step) -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-clipboard-text"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Soal</small>
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

    <!-- Rancangan Soal -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-5">
                        <h4 class="fw-bolder mb-3">Rancangan Soal</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk menentukan pemetaan soal terhadap CPMK sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="soalMappingForm">
                    <?php
                        // Initialize the soal mapping data from session or use default values
                        $soalMappingData = session()->get('soal_mapping_data') ?? [
                            'tugas' => [['soal_no' => 1, 'cpmk_mappings' => []]],
                            'uts' => [['soal_no' => 1, 'cpmk_mappings' => []]],
                            'uas' => [['soal_no' => 1, 'cpmk_mappings' => []]]
                        ];

                        $assessmentData = session()->get('assessment_data') ?? [];
                        $mappingData = session()->get('mapping_data');
                        $cpmkData = session()->get('cpmk_data');

                        // Get unique CPMK numbers from mapping data
                        $uniqueCpmkNumbers = [];
                        if ($mappingData && $cpmkData) {
                            foreach ($mappingData as $cplNo => $cplMapping) {
                                foreach ($cplMapping as $cpmkNo => $subCpmks) {
                                    if (!in_array($cpmkNo, $uniqueCpmkNumbers)) {
                                        $uniqueCpmkNumbers[] = $cpmkNo;
                                    }
                                }
                            }
                            // Sort CPMK numbers
                            sort($uniqueCpmkNumbers);
                        }

                        // Check which sections to display
                        $showTugas = false;
                        $showUTS = false;
                        $showUAS = false;

                        foreach ($assessmentData as $cpmkNo => $types) {
                            if (isset($types['tugas']) && $types['tugas']) $showTugas = true;
                            if (isset($types['uts']) && $types['uts']) $showUTS = true;
                            if (isset($types['uas']) && $types['uas']) $showUAS = true;
                        }
                        ?>

                        <?php if ($showTugas): ?>
                        <!-- Tugas Section -->
                        <div id="tugas-section" class="mb-5">
                            <h5 class="fw-bolder mb-3">1. Tugas</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="tugas-table">
                                    <thead class="text-white" style="background-color: #0f4c92;">
                                        <tr class="align-middle text-center">
                                            <th rowspan="2" style="vertical-align: middle;">Soal No</th>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                            <th colspan="1">CPMK <?= $cpmkNo ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Sort the tugas soal by soal_no
                                        usort($soalMappingData['tugas'], function($a, $b) {
                                            return $a['soal_no'] <=> $b['soal_no'];
                                        });
                                        
                                        foreach ($soalMappingData['tugas'] as $soal): 
                                        ?>
                                        <tr data-soal-no="<?= $soal['soal_no'] ?>">
                                            <td class="align-middle text-center">
                                                <strong>Soal no <?= $soal['soal_no'] ?></strong>
                                            </td>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                    class="soal-checkbox"
                                                    name="soal_mapping[tugas][<?= $soal['soal_no'] ?>][<?= $cpmkNo ?>]"
                                                    <?= isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo] ? 'checked' : '' ?>>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm add-soal-btn" data-type="tugas">
                                <i class="ti ti-plus"></i> Tambah Soal
                            </button>
                        </div>
                        <?php endif; ?>

                        <?php if ($showUTS): ?>
                        <!-- UTS Section -->
                        <div id="uts-section" class="mb-5">
                            <h5 class="fw-bolder mb-3">2. Ujian Tengah Semester</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="uts-table">
                                    <thead class="text-white" style="background-color: #0f4c92;">
                                        <tr class="align-middle text-center">
                                            <th rowspan="2" style="vertical-align: middle;">Soal No</th>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                            <th colspan="1">CPMK <?= $cpmkNo ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Sort the uts soal by soal_no
                                        usort($soalMappingData['uts'], function($a, $b) {
                                            return $a['soal_no'] <=> $b['soal_no'];
                                        });
                                        
                                        foreach ($soalMappingData['uts'] as $soal): 
                                        ?>
                                        <tr data-soal-no="<?= $soal['soal_no'] ?>">
                                            <td class="align-middle text-center">
                                                <strong>Soal no <?= $soal['soal_no'] ?></strong>
                                            </td>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                    class="soal-checkbox"
                                                    name="soal_mapping[uts][<?= $soal['soal_no'] ?>][<?= $cpmkNo ?>]"
                                                    <?= isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo] ? 'checked' : '' ?>>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm add-soal-btn" data-type="uts">
                                <i class="ti ti-plus"></i> Tambah Soal
                            </button>
                        </div>
                        <?php endif; ?>

                        <?php if ($showUAS): ?>
                        <!-- UAS Section -->
                        <div id="uas-section" class="mb-5">
                            <h5 class="fw-bolder mb-3">3. Ujian Akhir Semester</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="uas-table">
                                    <thead class="text-white" style="background-color: #0f4c92;">
                                        <tr class="align-middle text-center">
                                            <th rowspan="2" style="vertical-align: middle;">Soal No</th>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                            <th colspan="1">CPMK <?= $cpmkNo ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Sort the uas soal by soal_no
                                        usort($soalMappingData['uas'], function($a, $b) {
                                            return $a['soal_no'] <=> $b['soal_no'];
                                        });
                                        
                                        foreach ($soalMappingData['uas'] as $soal): 
                                        ?>
                                        <tr data-soal-no="<?= $soal['soal_no'] ?>">
                                            <td class="align-middle text-center">
                                                <strong>Soal no <?= $soal['soal_no'] ?></strong>
                                            </td>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                    class="soal-checkbox"
                                                    name="soal_mapping[uas][<?= $soal['soal_no'] ?>][<?= $cpmkNo ?>]"
                                                    <?= isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo] ? 'checked' : '' ?>>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm add-soal-btn" data-type="uas">
                                <i class="ti ti-plus"></i> Tambah Soal
                            </button>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/rancangan-asesmen') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="submit" id="submitBtn" class="btn btn-primary">
                                    Selanjutnya <i class="ti ti-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('backend/partials/footer') ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reference to the form
        const form = document.getElementById('soalMappingForm');
        
        // Add click handlers to all "Tambah Soal" buttons
        const addSoalButtons = document.querySelectorAll('.add-soal-btn');
        addSoalButtons.forEach(button => {
            button.addEventListener('click', function() {
                const assessmentType = this.getAttribute('data-type'); // 'tugas', 'uts', or 'uas'
                addNewSoalRow(assessmentType);
            });
        });
        
        // Function to add a new question row
        function addNewSoalRow(assessmentType) {
            // Get the table for the specific assessment type
            const table = document.getElementById(`${assessmentType}-table`);
            if (!table) return;
            
            const tbody = table.querySelector('tbody');
            
            // Find the highest soal number currently in the table
            let maxSoalNo = 0;
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const soalNo = parseInt(row.getAttribute('data-soal-no'), 10);
                if (soalNo > maxSoalNo) {
                    maxSoalNo = soalNo;
                }
            });
            
            // Create new soal number (increment from max)
            const newSoalNo = maxSoalNo + 1;
            
            // Create a new row
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-soal-no', newSoalNo);
            
            // Create soal number cell
            let newRowHtml = `
                <td class="align-middle text-center">
                    <strong>Soal no ${newSoalNo}</strong>
                </td>
            `;
            
            // Get all CPMK columns from the header
            const cpmkHeaders = table.querySelectorAll('thead th[colspan="1"]');
            
            // Add checkbox cells for each CPMK
            cpmkHeaders.forEach(header => {
                const cpmkText = header.textContent.trim();
                const cpmkNo = cpmkText.replace('CPMK ', '');
                
                newRowHtml += `
                    <td class="text-center">
                        <input type="checkbox" 
                            class="soal-checkbox"
                            name="soal_mapping[${assessmentType}][${newSoalNo}][${cpmkNo}]">
                    </td>
                `;
            });
            
            newRow.innerHTML = newRowHtml;
            tbody.appendChild(newRow);
        }
        
        // Form submission handler
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect soal mapping data
            const soalMappingData = collectSoalMappingData();
            
            // Send the data via fetch API
            saveSoalMappingToSession(soalMappingData);
        });
        
        // Function to collect soal mapping data from the form
        function collectSoalMappingData() {
            const soalMappingData = {
                'tugas': [],
                'uts': [],
                'uas': []
            };
            
            // Process each assessment type
            ['tugas', 'uts', 'uas'].forEach(type => {
                const table = document.getElementById(`${type}-table`);
                if (!table) return;
                
                // Get all rows in the table
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const soalNo = parseInt(row.getAttribute('data-soal-no'), 10);
                    const cpmkMappings = {};
                    
                    // Get all checkboxes in the row
                    const checkboxes = row.querySelectorAll('.soal-checkbox');
                    checkboxes.forEach(checkbox => {
                        const name = checkbox.getAttribute('name');
                        const matches = name.match(/soal_mapping\[(\w+)\]\[(\d+)\]\[(\d+)\]/);
                        
                        if (matches) {
                            const [, , , cpmkNo] = matches;
                            cpmkMappings[cpmkNo] = checkbox.checked;
                        }
                    });
                    
                    soalMappingData[type].push({
                        'soal_no': soalNo,
                        'cpmk_mappings': cpmkMappings
                    });
                });
            });
            
            return soalMappingData;
        }
        
        // Function to save soal mapping data to session via AJAX
        function saveSoalMappingToSession(soalMappingData) {
            fetch('<?= base_url('portofolio-form/saveSoalMapping') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    soal_mapping: soalMappingData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Berhasil menyimpan data pemetaan soal:', data.message);
                    // Redirect to the next page after successful save
                    window.location.href = '<?= base_url('portofolio-form/rekap-nilai') ?>';
                } else {
                    alert('Gagal menyimpan data pemetaan soal: ' + data.message);
                    console.error('Gagal menyimpan data pemetaan soal:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data pemetaan soal.');
            });
        }
    });
</script>