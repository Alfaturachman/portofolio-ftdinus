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

                        <div class="step-line active"></div>

                        <!-- Rancangan Assesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
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

    <!-- Rancangan Asesmen -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-5">
                        <h4 class="fw-bolder mb-3">Rancangan Asesmen</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi rancangan asesmen di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <form id="rpsForm" enctype="multipart/form-data">
                        <h5 class="fw-bolder mb-3">Rancangan Jadwal Assesmen</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead class="text-white" style="background-color: #0f4c92;">
                                    <tr class="align-middle text-center">
                                        <th>CPMK</th>
                                        <th>Sub CPMK</th>
                                        <th>TUGAS</th>
                                        <th>UTS</th>
                                        <th>UAS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $mappingData = session()->get('mapping_data');
                                    $cpmkData = session()->get('cpmk_data');
                                    $assessmentData = session()->get('assessment_data') ?? [];

                                    // Convert assessment data to array if it's not already an array
                                    if (is_object($assessmentData)) {
                                        $assessmentData = json_decode(json_encode($assessmentData), true);
                                    }

                                    if ($mappingData && $cpmkData):
                                        $currentCpmk = null;
                                        $rowspanCount = [];

                                        // Calculate rowspan for each CPMK
                                        foreach ($mappingData as $cplNo => $cplMapping) {
                                            foreach ($cplMapping as $cpmkNo => $subCpmks) {
                                                if (!isset($rowspanCount[$cpmkNo])) {
                                                    $rowspanCount[$cpmkNo] = 0;
                                                }
                                                foreach ($subCpmks as $subNo => $isChecked) {
                                                    if ($isChecked) {
                                                        $rowspanCount[$cpmkNo]++;
                                                    }
                                                }
                                            }
                                        }

                                        // Display the table rows
                                        foreach ($mappingData as $cplNo => $cplMapping):
                                            foreach ($cplMapping as $cpmkNo => $subCpmks):
                                                $firstRow = true;
                                                foreach ($subCpmks as $subNo => $isChecked):
                                                    if ($isChecked):
                                    ?>
                                                        <tr>
                                                            <?php if ($firstRow && $rowspanCount[$cpmkNo] > 0): ?>
                                                                <td class="align-middle" rowspan="<?= $rowspanCount[$cpmkNo] ?>">
                                                                    <strong>CPMK <?= $cpmkNo ?></strong>
                                                                </td>
                                                            <?php endif; ?>
                                                            <td class="align-middle">
                                                                <strong>Sub CPMK <?= $subNo ?></strong>
                                                            </td>
                                                            <!-- Modified checkbox input fields in the table -->
                                                            <td class="text-center">
                                                                <input type="checkbox"
                                                                    class="assessment-checkbox"
                                                                    name="assessment[<?= $cpmkNo ?>][<?= $subNo ?>][tugas]"
                                                                    <?= isset($assessmentData[$cpmkNo][$subNo]['tugas']) && $assessmentData[$cpmkNo][$subNo]['tugas'] ? 'checked' : '' ?>>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox"
                                                                    class="assessment-checkbox"
                                                                    name="assessment[<?= $cpmkNo ?>][<?= $subNo ?>][uts]"
                                                                    <?= isset($assessmentData[$cpmkNo][$subNo]['uts']) && $assessmentData[$cpmkNo][$subNo]['uts'] ? 'checked' : '' ?>>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox"
                                                                    class="assessment-checkbox"
                                                                    name="assessment[<?= $cpmkNo ?>][<?= $subNo ?>][uas]"
                                                                    <?= isset($assessmentData[$cpmkNo][$subNo]['uas']) && $assessmentData[$cpmkNo][$subNo]['uas'] ? 'checked' : '' ?>>
                                                            </td>
                                                        </tr>
                                        <?php
                                                        $firstRow = false;
                                                    endif;
                                                endforeach;
                                            endforeach;
                                        endforeach;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data pemetaan yang tersedia.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Tugas -->
                        <div id="tugas-section" class="mb-4">
                            <h5 class="fw-bolder mb-3">1. Tugas</h5>
                            <div class="form-group">
                                <label for="soal_tugas" class="form-label">Soal</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="soal_tugas" name="soal_tugas" accept="application/pdf">
                                    <?php if (isset(session()->get('assessment_files')['soal_tugas'])): ?>
                                        <span class="input-group-text bg-success text-white">
                                            <i class="ti ti-check"></i> File Terunggah
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset(session()->get('assessment_files')['soal_tugas'])): ?>
                                    <div class="mt-2">
                                        <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('assessment_files')['soal_tugas']['name']) ?></span>
                                        <p class="text-muted small">Ukuran: <?= round(session()->get('assessment_files')['soal_tugas']['size'] / 1024, 2) ?> KB</p>
                                    </div>
                                <?php endif; ?>
                                <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>

                                <label for="rubrik_tugas" class="form-label mt-2">Rubrik</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="rubrik_tugas" name="rubrik_tugas" accept="application/pdf">
                                    <?php if (isset(session()->get('assessment_files')['rubrik_tugas'])): ?>
                                        <span class="input-group-text bg-success text-white">
                                            <i class="ti ti-check"></i> File Terunggah
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset(session()->get('assessment_files')['rubrik_tugas'])): ?>
                                    <div class="mt-2">
                                        <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('assessment_files')['rubrik_tugas']['name']) ?></span>
                                        <p class="text-muted small">Ukuran: <?= round(session()->get('assessment_files')['rubrik_tugas']['size'] / 1024, 2) ?> KB</p>
                                    </div>
                                <?php endif; ?>
                                <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                            </div>
                        </div>

                        <!-- Ujian Tengah Semester -->
                        <div id="uts-section" class="mb-4">
                            <h5 class="fw-bolder mb-3">2. Ujian Tengah Semester</h5>
                            <div class="form-group">
                                <label for="soal_uts" class="form-label">Soal</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="soal_uts" name="soal_uts" accept="application/pdf">
                                    <?php if (isset(session()->get('assessment_files')['soal_uts'])): ?>
                                        <span class="input-group-text bg-success text-white">
                                            <i class="ti ti-check"></i> File Terunggah
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset(session()->get('assessment_files')['soal_uts'])): ?>
                                    <div class="mt-2">
                                        <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('assessment_files')['soal_uts']['name']) ?></span>
                                        <p class="text-muted small">Ukuran: <?= round(session()->get('assessment_files')['soal_uts']['size'] / 1024, 2) ?> KB</p>
                                    </div>
                                <?php endif; ?>
                                <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>

                                <label for="rubrik_uts" class="form-label mt-2">Rubrik</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="rubrik_uts" name="rubrik_uts" accept="application/pdf">
                                    <?php if (isset(session()->get('assessment_files')['rubrik_uts'])): ?>
                                        <span class="input-group-text bg-success text-white">
                                            <i class="ti ti-check"></i> File Terunggah
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset(session()->get('assessment_files')['rubrik_uts'])): ?>
                                    <div class="mt-2">
                                        <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('assessment_files')['rubrik_uts']['name']) ?></span>
                                        <p class="text-muted small">Ukuran: <?= round(session()->get('assessment_files')['rubrik_uts']['size'] / 1024, 2) ?> KB</p>
                                    </div>
                                <?php endif; ?>
                                <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                            </div>
                        </div>

                        <!-- Ujian Akhir Semester -->
                        <div id="uas-section" class="mb-4">
                            <h5 class="fw-bolder mb-3">3. Ujian Akhir Semester</h5>
                            <div class="form-group">
                                <label for="soal_uas" class="form-label">Soal</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="soal_uas" name="soal_uas" accept="application/pdf">
                                    <?php if (isset(session()->get('assessment_files')['soal_uas'])): ?>
                                        <span class="input-group-text bg-success text-white">
                                            <i class="ti ti-check"></i> File Terunggah
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset(session()->get('assessment_files')['soal_uas'])): ?>
                                    <div class="mt-2">
                                        <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('assessment_files')['soal_uas']['name']) ?></span>
                                        <p class="text-muted small">Ukuran: <?= round(session()->get('assessment_files')['soal_uas']['size'] / 1024, 2) ?> KB</p>
                                    </div>
                                <?php endif; ?>
                                <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>

                                <label for="rubrik_uas" class="form-label mt-2">Rubrik</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="rubrik_uas" name="rubrik_uas" accept="application/pdf">
                                    <?php if (isset(session()->get('assessment_files')['rubrik_uas'])): ?>
                                        <span class="input-group-text bg-success text-white">
                                            <i class="ti ti-check"></i> File Terunggah
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset(session()->get('assessment_files')['rubrik_uas'])): ?>
                                    <div class="mt-2">
                                        <span class="text-success"><i class="ti ti-file"></i> <?= esc(session()->get('assessment_files')['rubrik_uas']['name']) ?></span>
                                        <p class="text-muted small">Ukuran: <?= round(session()->get('assessment_files')['rubrik_uas']['size'] / 1024, 2) ?> KB</p>
                                    </div>
                                <?php endif; ?>
                                <p class="mt-2" style="color: #5a6a85!important;">*format file: PDF, ukuran maksimal 10MB</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/pemetaan') ?>">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('rpsForm');
        const checkboxes = document.querySelectorAll('.assessment-checkbox');
        
        // Get all file upload sections
        const taskSection = document.getElementById('tugas-section');
        const utsSection = document.getElementById('uts-section');
        const uasSection = document.getElementById('uas-section');
        
        // Function to update file upload sections visibility
        function updateFileUploadSections() {
            // Check if any task checkbox is checked
            const taskChecked = Array.from(document.querySelectorAll('input[name*="[tugas]"]'))
                .some(checkbox => checkbox.checked);
                
            // Check if any UTS checkbox is checked
            const utsChecked = Array.from(document.querySelectorAll('input[name*="[uts]"]'))
                .some(checkbox => checkbox.checked);
                
            // Check if any UAS checkbox is checked
            const uasChecked = Array.from(document.querySelectorAll('input[name*="[uas]"]'))
                .some(checkbox => checkbox.checked);
            
            // Show/hide sections based on checkbox status
            taskSection.style.display = taskChecked ? 'block' : 'none';
            utsSection.style.display = utsChecked ? 'block' : 'none';
            uasSection.style.display = uasChecked ? 'block' : 'none';
            
            // Update required attribute for file inputs
            if (taskSection) {
                const taskInputs = taskSection.querySelectorAll('input[type="file"]');
                taskInputs.forEach(input => {
                    // Only set required if the section is visible and no file is already uploaded
                    const fileAlreadyUploaded = input.nextElementSibling && 
                        input.nextElementSibling.classList.contains('bg-success');
                    input.required = taskChecked && !fileAlreadyUploaded;
                });
            }
            
            if (utsSection) {
                const utsInputs = utsSection.querySelectorAll('input[type="file"]');
                utsInputs.forEach(input => {
                    const fileAlreadyUploaded = input.nextElementSibling && 
                        input.nextElementSibling.classList.contains('bg-success');
                    input.required = utsChecked && !fileAlreadyUploaded;
                });
            }
            
            if (uasSection) {
                const uasInputs = uasSection.querySelectorAll('input[type="file"]');
                uasInputs.forEach(input => {
                    const fileAlreadyUploaded = input.nextElementSibling && 
                        input.nextElementSibling.classList.contains('bg-success');
                    input.required = uasChecked && !fileAlreadyUploaded;
                });
            }
        }

        // Save checkbox state when changed and update file sections
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const assessmentData = collectAssessmentData();
                saveAssessmentToSession(assessmentData);
                updateFileUploadSections(); // Update file sections when checkboxes change
            });
        });

        // Function to collect assessment data from checkboxes
        function collectAssessmentData() {
            const assessmentData = {};

            checkboxes.forEach(checkbox => {
                const name = checkbox.getAttribute('name');
                const matches = name.match(/assessment\[(\d+)\]\[(\d+)\]\[(\w+)\]/);

                if (matches) {
                    const [, cpmk, subCpmk, type] = matches;

                    if (!assessmentData[cpmk]) assessmentData[cpmk] = {};
                    if (!assessmentData[cpmk][subCpmk]) assessmentData[cpmk][subCpmk] = {};

                    assessmentData[cpmk][subCpmk][type] = checkbox.checked;
                }
            });

            return assessmentData;
        }

        // Function to save assessment data to session via AJAX
        function saveAssessmentToSession(assessmentData) {
            fetch('<?= base_url('portofolio-form/saveAssessmentToSession') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        assessment: assessmentData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Success to save assessment data:', data.message);
                    } else {
                        alert(data.message);
                        console.error('Failed to save assessment data:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Modify the form submit event
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Create FormData object for file uploads
            const formData = new FormData();

            // Add checkbox assessment data
            const assessmentData = collectAssessmentData();
            formData.append('assessment_data', JSON.stringify(assessmentData));

            // Add file data only for visible sections
            const taskChecked = Array.from(document.querySelectorAll('input[name*="[tugas]"]'))
                .some(checkbox => checkbox.checked);
            const utsChecked = Array.from(document.querySelectorAll('input[name*="[uts]"]'))
                .some(checkbox => checkbox.checked);
            const uasChecked = Array.from(document.querySelectorAll('input[name*="[uas]"]'))
                .some(checkbox => checkbox.checked);

            // Define file inputs based on visible sections
            let fileInputs = [];
            
            if (taskChecked) {
                fileInputs = [...fileInputs, 'soal_tugas', 'rubrik_tugas'];
            }
            if (utsChecked) {
                fileInputs = [...fileInputs, 'soal_uts', 'rubrik_uts'];
            }
            if (uasChecked) {
                fileInputs = [...fileInputs, 'soal_uas', 'rubrik_uas'];
            }

            // Add all files to formData
            let filesChanged = false;

            fileInputs.forEach(inputName => {
                const fileInput = document.getElementById(inputName);
                if (fileInput && fileInput.files.length > 0) {
                    formData.append(inputName, fileInput.files[0]);
                    filesChanged = true;
                }
            });

            // Add a flag to indicate if files were changed
            formData.append('files_changed', filesChanged);

            fetch('<?= base_url('portofolio-form/saveAssessmentWithFiles') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '<?= base_url('portofolio-form/pelaksanaan-perkuliahan') ?>';
                    } else {
                        alert('Gagal menyimpan data asesmen: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data asesmen.');
                });
        });
        
        // Call the update function on page load to set initial state
        updateFileUploadSections();
    });
</script>

<?= $this->include('backend/partials/footer') ?>