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
                        <h5 class="fw-bolder mb-0">Nilai Soal Mahasiswa</h5>
                    </div>
                    
                    <!-- Step navigation -->
                    <div class="d-flex justify-content-between align-items-baseline">
                        <!-- Step circles and lines - same as in rancangan-soal -->
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

                        <!-- Rancangan Soal -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-clipboard-text"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Rancangan Soal</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Pelaksanaan Perkuliahan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-school"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Pelaksanaan Perkuliahan</small>
                        </div>

                        <div class="step-line active"></div>

                        <!-- Hasil Asesmen -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
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

    <!-- Rekap Nilai Content -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-5">
                        <h4 class="fw-bolder mb-3">Nilai Soal Mahasiswa</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan pilih kelas dan isi nilai mahasiswa sesuai dengan soal yang telah dirancang
                        </div>
                    </div>

                    <!-- Kelas Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="kelas_matkul" class="form-label">Kelas Mata Kuliah</label>
                            <select id="kelas_matkul" class="form-select" aria-label="Pilih Kelas">
                                <option value="" hidden>-- Pilih Kelas --</option>
                                <?php
                                // Get mata kuliah kelas from matkul_diampu with kode_matkul from session
                                $infoMatkul = session()->get('info_matkul');
                                $kodeMatkul = $infoMatkul['kode_mk'] ?? '';
                                
                                // Get selected kelas from session if exists
                                $selectedKelasId = session()->get('selected_kelas_id') ?? '';
                                
                                $db = \Config\Database::connect();
                                $kelasList = $db->table('matkul_diampu')
                                    ->select('id, matkul, kelp_matkul, semester, tahun')
                                    ->where('kode_matkul', $kodeMatkul)
                                    ->get()
                                    ->getResultArray();
                                
                                foreach ($kelasList as $kelas):
                                    $selected = ($kelas['id'] == $selectedKelasId) ? 'selected' : '';
                                ?>
                                <option value="<?= $kelas['id'] ?>" <?= $selected ?>>
                                    <?= $kelas['matkul'] ?> - <?= $kelas['kelp_matkul'] ?> (<?= $kelas['semester'] ?> <?= $kelas['tahun'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Loading indicator -->
                    <div id="loading" class="text-center my-4 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data mahasiswa...</p>
                    </div>

                    <!-- Empty state when no class is selected -->
                    <div id="empty-state" class="text-center py-5">
                        <i class="ti ti-users-group opacity-25" style="font-size: 64px;"></i>
                        <p class="mt-3">Silahkan pilih kelas untuk menampilkan data mahasiswa</p>
                    </div>

                    <form id="rekapNilaiForm" class="d-none">
                        <?php
                        // Get the assessment types that are being used
                        $assessmentData = session()->get('assessment_data') ?? [];
                        $soalMappingData = session()->get('soal_mapping_data') ?? [
                            'tugas' => [],
                            'uts' => [],
                            'uas' => []
                        ];
                        
                        // Get CPMK data and mapping
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
                        
                        // Get existing saved data
                        $savedNilaiData = session()->get('nilai_soal_data') ?? null;
                        ?>
                        
                        <!-- Hidden inputs for JavaScript -->
                        <input type="hidden" id="soalMappingData" value='<?= json_encode($soalMappingData) ?>'>
                        <input type="hidden" id="uniqueCpmkNumbers" value='<?= json_encode($uniqueCpmkNumbers) ?>'>
                        <input type="hidden" id="savedNilaiData" value='<?= json_encode($savedNilaiData) ?>'>
                        <input type="hidden" id="selectedKelasId" value='<?= $selectedKelasId ?>'>

                        <!-- Tugas Section -->
                        <?php if ($showTugas && !empty($soalMappingData['tugas'])): ?>
                        <div id="tugas-nilai-section" class="mb-5">
                            <h5 class="fw-bolder mb-3">1. Nilai Soal Tugas</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="tugas-nilai-table">
                                    <thead class="text-white" style="background-color: #0f4c92;">
                                        <tr>
                                            <th rowspan="2" class="align-middle text-center">No</th>
                                            <th rowspan="2" class="align-middle text-center">Nama Mahasiswa</th>
                                            <th rowspan="2" class="align-middle text-center">NIM</th>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                            <?php
                                                // Count how many soal are mapped to this CPMK
                                                $soalCount = 0;
                                                foreach ($soalMappingData['tugas'] as $soal) {
                                                    if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]) {
                                                        $soalCount++;
                                                    }
                                                }
                                                if ($soalCount > 0):
                                            ?>
                                            <th colspan="<?= $soalCount ?>" class="text-center">CPMK <?= $cpmkNo ?></th>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                        <tr>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                                <?php foreach ($soalMappingData['tugas'] as $soal): ?>
                                                    <?php if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]): ?>
                                                    <th class="text-center">Soal no <?= $soal['soal_no'] ?></th>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody id="tugas-nilai-body">
                                        <!-- Mahasiswa will be populated by JavaScript -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-center"><strong>Rata-rata</strong></td>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                                <?php
                                                // Count how many soal are mapped to this CPMK
                                                $soalCount = 0;
                                                $firstSoal = null;
                                                foreach ($soalMappingData['tugas'] as $soal) {
                                                    if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]) {
                                                        $soalCount++;
                                                        if ($firstSoal === null) {
                                                            $firstSoal = $soal['soal_no'];
                                                        }
                                                    }
                                                }
                                                
                                                // If we have soal for this CPMK
                                                if ($soalCount > 0):
                                                    // For the first soal, show the CPMK rata-rata with colspan
                                                    // For other soal, don't render a cell (it's covered by colspan)
                                                    foreach ($soalMappingData['tugas'] as $soal):
                                                        if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]):
                                                            if ($soal['soal_no'] === $firstSoal):
                                                                // Get average from session if exists
                                                                $avgValue = '0.00';
                                                                if ($savedNilaiData && isset($savedNilaiData['cpmk_average']['tugas'][$cpmkNo])) {
                                                                    $avgValue = $savedNilaiData['cpmk_average']['tugas'][$cpmkNo];
                                                                }
                                                ?>
                                                <td class="text-center rata-rata" id="rata-tugas-<?= $cpmkNo ?>-<?= $soal['soal_no'] ?>" colspan="<?= $soalCount ?>" data-average="<?= $avgValue ?>">
                                                    <?= $avgValue ?>
                                                </td>
                                                <?php 
                                                            endif;
                                                        endif;
                                                    endforeach;
                                                endif;
                                                ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- UTS Section -->
                        <?php if ($showUTS && !empty($soalMappingData['uts'])): ?>
                        <div id="uts-nilai-section" class="mb-5">
                            <h5 class="fw-bolder mb-3">2. Nilai Soal Ujian Tengah Semester</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="uts-nilai-table">
                                    <thead class="text-white" style="background-color: #0f4c92;">
                                        <tr>
                                            <th rowspan="2" class="align-middle text-center">No</th>
                                            <th rowspan="2" class="align-middle text-center">Nama Mahasiswa</th>
                                            <th rowspan="2" class="align-middle text-center">NIM</th>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                            <?php
                                                // Count how many soal are mapped to this CPMK
                                                $soalCount = 0;
                                                foreach ($soalMappingData['uts'] as $soal) {
                                                    if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]) {
                                                        $soalCount++;
                                                    }
                                                }
                                                if ($soalCount > 0):
                                            ?>
                                            <th colspan="<?= $soalCount ?>" class="text-center">CPMK <?= $cpmkNo ?></th>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                        <tr>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                                <?php foreach ($soalMappingData['uts'] as $soal): ?>
                                                    <?php if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]): ?>
                                                    <th class="text-center">Soal no <?= $soal['soal_no'] ?></th>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody id="uts-nilai-body">
                                        <!-- Mahasiswa will be populated by JavaScript -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-center"><strong>Rata-rata</strong></td>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                                <?php
                                                // Count how many soal are mapped to this CPMK
                                                $soalCount = 0;
                                                $firstSoal = null;
                                                foreach ($soalMappingData['uts'] as $soal) {
                                                    if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]) {
                                                        $soalCount++;
                                                        if ($firstSoal === null) {
                                                            $firstSoal = $soal['soal_no'];
                                                        }
                                                    }
                                                }
                                                
                                                // If we have soal for this CPMK
                                                if ($soalCount > 0):
                                                    // For the first soal, show the CPMK rata-rata with colspan
                                                    // For other soal, don't render a cell (it's covered by colspan)
                                                    foreach ($soalMappingData['uts'] as $soal):
                                                        if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]):
                                                            if ($soal['soal_no'] === $firstSoal):
                                                                // Get average from session if exists
                                                                $avgValue = '0.00';
                                                                if ($savedNilaiData && isset($savedNilaiData['cpmk_average']['uts'][$cpmkNo])) {
                                                                    $avgValue = $savedNilaiData['cpmk_average']['uts'][$cpmkNo];
                                                                }
                                                ?>
                                                <td class="text-center rata-rata" id="rata-uts-<?= $cpmkNo ?>-<?= $soal['soal_no'] ?>" colspan="<?= $soalCount ?>" data-average="<?= $avgValue ?>">
                                                    <?= $avgValue ?>
                                                </td>
                                                <?php 
                                                            endif;
                                                        endif;
                                                    endforeach;
                                                endif;
                                                ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- UAS Section -->
                        <?php if ($showUAS && !empty($soalMappingData['uas'])): ?>
                        <div id="uas-nilai-section" class="mb-5">
                            <h5 class="fw-bolder mb-3">3. Nilai Soal Ujian Akhir Semester</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="uas-nilai-table">
                                    <thead class="text-white" style="background-color: #0f4c92;">
                                        <tr>
                                            <th rowspan="2" class="align-middle text-center">No</th>
                                            <th rowspan="2" class="align-middle text-center">Nama Mahasiswa</th>
                                            <th rowspan="2" class="align-middle text-center">NIM</th>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                            <?php
                                                // Count how many soal are mapped to this CPMK
                                                $soalCount = 0;
                                                foreach ($soalMappingData['uas'] as $soal) {
                                                    if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]) {
                                                        $soalCount++;
                                                    }
                                                }
                                                if ($soalCount > 0):
                                            ?>
                                            <th colspan="<?= $soalCount ?>" class="text-center">CPMK <?= $cpmkNo ?></th>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                        <tr>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                                <?php foreach ($soalMappingData['uas'] as $soal): ?>
                                                    <?php if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]): ?>
                                                    <th class="text-center">Soal no <?= $soal['soal_no'] ?></th>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody id="uas-nilai-body">
                                        <!-- Mahasiswa will be populated by JavaScript -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-center"><strong>Rata-rata</strong></td>
                                            <?php foreach ($uniqueCpmkNumbers as $cpmkNo): ?>
                                                <?php
                                                // Count how many soal are mapped to this CPMK
                                                $soalCount = 0;
                                                $firstSoal = null;
                                                foreach ($soalMappingData['uas'] as $soal) {
                                                    if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]) {
                                                        $soalCount++;
                                                        if ($firstSoal === null) {
                                                            $firstSoal = $soal['soal_no'];
                                                        }
                                                    }
                                                }
                                                
                                                // If we have soal for this CPMK
                                                if ($soalCount > 0):
                                                    // For the first soal, show the CPMK rata-rata with colspan
                                                    // For other soal, don't render a cell (it's covered by colspan)
                                                    foreach ($soalMappingData['uas'] as $soal):
                                                        if (isset($soal['cpmk_mappings'][$cpmkNo]) && $soal['cpmk_mappings'][$cpmkNo]):
                                                            if ($soal['soal_no'] === $firstSoal):
                                                                // Get average from session if exists
                                                                $avgValue = '0.00';
                                                                if ($savedNilaiData && isset($savedNilaiData['cpmk_average']['uas'][$cpmkNo])) {
                                                                    $avgValue = $savedNilaiData['cpmk_average']['uas'][$cpmkNo];
                                                                }
                                                ?>
                                                <td class="text-center rata-rata" id="rata-uas-<?= $cpmkNo ?>-<?= $soal['soal_no'] ?>" colspan="<?= $soalCount ?>" data-average="<?= $avgValue ?>">
                                                    <?= $avgValue ?>
                                                </td>
                                                <?php 
                                                            endif;
                                                        endif;
                                                    endforeach;
                                                endif;
                                                ?>
                                            <?php endforeach; ?>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/rancangan-soal') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="submit" id="submitBtn" class="btn btn-primary">
                                    Simpan & Lanjutkan <i class="ti ti-arrow-right"></i>
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
        const kelasSelect = document.getElementById('kelas_matkul');
        const rekapForm = document.getElementById('rekapNilaiForm');
        const emptyState = document.getElementById('empty-state');
        const loadingIndicator = document.getElementById('loading');
        const selectedKelasId = document.getElementById('selectedKelasId')?.value || '';
        const savedNilaiData = document.getElementById('savedNilaiData')?.value ? JSON.parse(document.getElementById('savedNilaiData').value) : null;
        
        // Initialize baseUrl for API calls
        const baseUrl = window.location.origin;
        
        // Define soalMappingJSON globally
        const soalMappingJSON = JSON.parse(document.getElementById('soalMappingData').value);
        const uniqueCpmkJSON = JSON.parse(document.getElementById('uniqueCpmkNumbers').value);
        
        // Ubah options untuk select input nilai di tabel
        const gradeOptions = [
            '<option value="" selected hidden>Nilai Soal</option>'
        ];

        // Menghasilkan nilai dari 1.00 hingga 4.00 dengan interval 0.25
        for (let i = 4.00; i >= 1.00; i -= 1) {
            // Format nilai agar selalu menampilkan 2 digit desimal
            const formattedValue = i.toFixed(2);
            gradeOptions.push(`<option value="${formattedValue}">${formattedValue}</option>`);
        }

        // Gabungkan menjadi satu string HTML
        const gradeOptionsHTML = gradeOptions.join('');
        
        // Auto-load data if a class is already selected
        if (selectedKelasId) {
            loadMahasiswaData(selectedKelasId);
        }
        
        // Handle class selection
        kelasSelect.addEventListener('change', function() {
            const kelasId = this.value;
            
            if (!kelasId) {
                rekapForm.classList.add('d-none');
                emptyState.classList.remove('d-none');
                return;
            }
            
            loadMahasiswaData(kelasId);
        });
        
        // Function to load mahasiswa data
        function loadMahasiswaData(kelasId) {
            // Show loading indicator
            emptyState.classList.add('d-none');
            loadingIndicator.classList.remove('d-none');
            
            // Fetch mahasiswa data for the selected class
            fetch(`${baseUrl}/portofolio-form/getMahasiswaByKelas/${kelasId}`)
                .then(response => response.json())
                .then(data => {
                    // Hide loading indicator
                    loadingIndicator.classList.add('d-none');
                    
                    if (data.success) {
                        // Show the form
                        rekapForm.classList.remove('d-none');
                        
                        // Populate mahasiswa data for each table
                        populateMahasiswaTable('tugas', data.mahasiswa);
                        populateMahasiswaTable('uts', data.mahasiswa);
                        populateMahasiswaTable('uas', data.mahasiswa);
                        
                        // Initialize average calculation
                        setupAverageCalculation();
                        
                        // If we have existing data from the response, populate it
                        if (data.existingData && data.existingData.grades) {
                            populateExistingData(data.existingData.grades);
                        } 
                        // Or use data from session if available
                        else if (savedNilaiData && savedNilaiData.grades) {
                            populateExistingData(savedNilaiData.grades);
                        }
                        
                        // Calculate all averages after populating data
                        calculateAllAverages();
                    } else {
                        // Show error message
                        emptyState.classList.remove('d-none');
                        emptyState.innerHTML = `
                            <i class="ti ti-alert-triangle text-warning" style="font-size: 64px;"></i>
                            <p class="mt-3">${data.message || 'Gagal memuat data mahasiswa'}</p>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    loadingIndicator.classList.add('d-none');
                    emptyState.classList.remove('d-none');
                    emptyState.innerHTML = `
                        <i class="ti ti-alert-triangle text-warning" style="font-size: 64px;"></i>
                        <p class="mt-3">Terjadi kesalahan saat memuat data. Silakan coba lagi.</p>
                    `;
                });
        }
        
        // Function to populate mahasiswa table
        function populateMahasiswaTable(type, mahasiswaData) {
            const tableBody = document.getElementById(`${type}-nilai-body`);
            if (!tableBody) return;
            
            // Clear existing content
            tableBody.innerHTML = '';
            
            // Generate table rows for each mahasiswa
            mahasiswaData.forEach((mahasiswa, index) => {
                const row = document.createElement('tr');
                
                // Add No, Nama, and NIM columns
                row.innerHTML = `
                    <td class="text-center">${index + 1}</td>
                    <td>${mahasiswa.nama}</td>
                    <td class="text-center">${mahasiswa.nim}</td>
                `;
                
                // Add grade select boxes for each CPMK and soal
                uniqueCpmkJSON.forEach(cpmkNo => {
                    soalMappingJSON[type].forEach(soal => {
                        if (soal.cpmk_mappings[cpmkNo]) {
                            // Create select box for this soal and CPMK
                            const selectCell = document.createElement('td');
                            selectCell.classList.add('text-center');
                            
                            const select = document.createElement('select');
                            select.classList.add('form-select', 'grade-select');
                            select.setAttribute('name', `nilai[${type}][${mahasiswa.nim}][${cpmkNo}][${soal.soal_no}]`);
                            select.setAttribute('data-type', type);
                            select.setAttribute('data-cpmk', cpmkNo);
                            select.setAttribute('data-soal', soal.soal_no);
                            select.setAttribute('data-nim', mahasiswa.nim);
                            select.innerHTML = gradeOptionsHTML;
                            
                            selectCell.appendChild(select);
                            row.appendChild(selectCell);
                        }
                    });
                });
                
                tableBody.appendChild(row);
            });
        }
        
        // Function to populate existing data if available
        function populateExistingData(gradesData) {
            // Loop through each assessment type
            for (const type in gradesData) {
                // Loop through each student
                for (const nim in gradesData[type]) {
                    // Loop through each CPMK
                    for (const cpmkNo in gradesData[type][nim]) {
                        // Loop through each soal
                        for (const soalNo in gradesData[type][nim][cpmkNo]) {
                            // Find the select element
                            const select = document.querySelector(`select[name="nilai[${type}][${nim}][${cpmkNo}][${soalNo}]"]`);
                            if (select) {
                                // Set the value
                                select.value = gradesData[type][nim][cpmkNo][soalNo];
                            }
                        }
                    }
                }
            }
        }
        
        // Calculate all averages for all types and CPMKs
        function calculateAllAverages() {
            const assessmentTypes = ['tugas', 'uts', 'uas'];
            
            assessmentTypes.forEach(type => {
                uniqueCpmkJSON.forEach(cpmkNo => {
                    calculateCPMKAverage(type, cpmkNo);
                });
            });
        }
        
        // Setup average calculation for each column
        function setupAverageCalculation() {
            // Add event listeners to all grade selects
            const gradeSelects = document.querySelectorAll('.grade-select');
            gradeSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const type = this.getAttribute('data-type');
                    const cpmkNo = this.getAttribute('data-cpmk');
                    
                    // Calculate averages for this CPMK
                    calculateCPMKAverage(type, cpmkNo);
                });
            });
        }
        
        // Function to calculate average for a specific CPMK across all soal
        function calculateCPMKAverage(type, cpmkNo) {
            // Get all soal numbers that map to this CPMK
            const soalNumbers = [];
            soalMappingJSON[type].forEach(soal => {
                if (soal.cpmk_mappings[cpmkNo]) {
                    soalNumbers.push(soal.soal_no);
                }
            });
            
            if (soalNumbers.length === 0) return;
            
            // First calculate average for each soal
            const soalAverages = {};
            soalNumbers.forEach(soalNo => {
                // Get all select elements for this soal
                const selects = document.querySelectorAll(`select[data-type="${type}"][data-cpmk="${cpmkNo}"][data-soal="${soalNo}"]`);
                
                // Initialize variables for calculation
                let sum = 0;
                let count = 0;
                
                // Sum up all selected values
                selects.forEach(select => {
                    const value = select.value;
                    if (value) {
                        sum += parseFloat(value);
                        count++;
                    }
                });
                
                // Calculate average for this soal
                const average = count > 0 ? (sum / count) : 0;
                soalAverages[soalNo] = average;
            });
            
            // Now calculate the average of all soal averages for this CPMK
            let totalSoalAverage = 0;
            let validSoalCount = 0;
            
            for (const soalNo in soalAverages) {
                if (soalAverages[soalNo] > 0) {
                    totalSoalAverage += soalAverages[soalNo];
                    validSoalCount++;
                }
            }
            
            // Calculate CPMK average
            const cpmkAverage = validSoalCount > 0 ? (totalSoalAverage / validSoalCount).toFixed(2) : '0.00';
            
            // Update all rata-rata cells for this CPMK with the same value
            const firstSoalNo = soalNumbers.length > 0 ? soalNumbers[0] : null;
            if (firstSoalNo) {
                const rataRataCell = document.getElementById(`rata-${type}-${cpmkNo}-${firstSoalNo}`);
                if (rataRataCell) {
                    rataRataCell.textContent = cpmkAverage;
                    // Store the average value as a data attribute for form submission
                    rataRataCell.setAttribute('data-average', cpmkAverage);
                }
            }
        }
        
        // Form submission
        const form = document.getElementById('rekapNilaiForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get selected kelas ID
            const kelasId = kelasSelect.value;
            if (!kelasId) {
                alert('Silakan pilih kelas terlebih dahulu');
                return;
            }
            
            // Collect form data
            const formData = new FormData(form);
            formData.append('kelas_id', kelasId);
            
            // Add CPMK averages to form data
            const assessmentTypes = ['tugas', 'uts', 'uas'];
            
            assessmentTypes.forEach(type => {
                uniqueCpmkJSON.forEach(cpmkNo => {
                    // Find the first soal for this CPMK
                    let firstSoalNo = null;
                    
                    for (const soal of soalMappingJSON[type]) {
                        if (soal.cpmk_mappings[cpmkNo]) {
                            firstSoalNo = soal.soal_no;
                            break;
                        }
                    }
                    
                    if (firstSoalNo) {
                        const rataRataCell = document.getElementById(`rata-${type}-${cpmkNo}-${firstSoalNo}`);
                        if (rataRataCell) {
                            const average = rataRataCell.getAttribute('data-average') || '0';
                            formData.append(`cpmk_average[${type}][${cpmkNo}]`, average);
                        }
                    }
                });
            });
            
            // Disable submit button to prevent double submission
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            
            // Send data via fetch API
            fetch(`${baseUrl}/portofolio-form/saveNilaiSoal`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to the next page
                    window.location.href = `${baseUrl}/portofolio-form/pelaksanaan-perkuliahan`;
                } else {
                    alert(data.message || 'Gagal menyimpan data nilai');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Simpan & Lanjutkan <i class="ti ti-arrow-right"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan & Lanjutkan <i class="ti ti-arrow-right"></i>';
            });
        });
    });
</script>