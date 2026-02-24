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

                        <div class="step-line"></div>

                        <!-- CPMK & Sub CPMK -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle">
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

    <!-- CPL & IKCP -->
    <div class="row">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Capaian Pembelajaran Lulusan (CPL) & Performa Index (PI)</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi CPL dan indikator kinerja di bawah sebelum melanjutkan!
                        </div>
                    </div>
                    
                    <!-- Kurikulum Filter -->
                    <?php if (!empty($kurikulumList) && count($kurikulumList) > 1): ?>
                    <div class="mb-4">
                        <div class="alert alert-info d-flex align-items-start" role="alert">
                            <i class="ti ti-info-circle me-2 fs-4"></i>
                            <div>
                                <strong>Informasi Kurikulum:</strong>
                                <p class="mb-0 mt-1">
                                    Mata kuliah ini memiliki <strong><?= count($kurikulumList) ?> kurikulum</strong> yang tersedia. 
                                    Silakan pilih kurikulum yang ingin ditampilkan untuk melihat CPL dan PI yang sesuai.
                                </p>
                            </div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label for="kurikulumFilter" class="form-label fw-bold">Filter Kurikulum</label>
                                <select class="form-select" id="kurikulumFilter" name="kurikulum">
                                    <?php foreach ($kurikulumList as $kurikulum): ?>
                                        <option value="<?= esc($kurikulum) ?>" 
                                            <?= ($kurikulum === $selectedKurikulum) ? 'selected' : '' ?>>
                                            <?= esc($kurikulum) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">
                                    <i class="ti ti-info-circle"></i> 
                                    Kurikulum aktif: <strong><?= esc($selectedKurikulum) ?></strong>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php elseif (!empty($kurikulumList)): ?>
                    <div class="mb-4">
                        <div class="alert alert-success d-flex align-items-start" role="alert">
                            <i class="ti ti-check me-2 fs-4"></i>
                            <div>
                                <strong>Kurikulum:</strong>
                                <p class="mb-0 mt-1">
                                    Mata kuliah ini menggunakan <strong><?= esc($kurikulumList[0]) ?></strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <form id="topicForm" action="<?= base_url('form/submit') ?>" method="post">
                        <?php if (!empty($pdfUrl)): ?>
                            <div class="mb-3" style="height: 600px; border: 1px solid #ccc; margin-top: 20px;">
                                <iframe src="<?= esc($pdfUrl) ?>" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                        <?php else: ?>
                        <?php endif; ?>
                        <table class="table table-bordered">
                            <thead class="text-white" style="background-color: #0f4c92;">
                                <tr>
                                    <th style="width: 30%" colspan="2">Capaian Pembelajaran Lulusan</th>
                                    <th style="width: 60%">Performa Index</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($cplPiData)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data CPL dan PI untuk mata kuliah ini.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($cplPiData as $cplNo => $cplData): ?>
                                        <?php $rowCount = max(count($cplData['pi_list']), 1); ?>
                                        <tr>
                                            <td rowspan="<?= $rowCount ?>" style="white-space: nowrap;"><strong>CPL <?= $cplNo ?></strong></td>
                                            <td rowspan="<?= $rowCount ?>"><?= esc($cplData['cpl_indo']) ?></td>
                                            <?php if (!empty($cplData['pi_list'])): ?>
                                                <td><?= esc($cplData['pi_list'][0]) ?></td>
                                            <?php else: ?>
                                                <td>-</td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php for ($i = 1; $i < count($cplData['pi_list']); $i++): ?>
                                            <tr>
                                                <td><?= esc($cplData['pi_list'][$i]) ?></td>
                                            </tr>
                                        <?php endfor; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between pt-3">
                            <?php if (isset($idPorto)): ?>
                                <a class="btn btn-secondary" href="<?= base_url('portofolio-form/info-matkul-edit/' . $idPorto) ?>">
                                    <i class="ti ti-arrow-left"></i> Kembali
                                </a>
                                <a class="btn btn-primary" href="<?= base_url('portofolio-form/cpmk-subcpmk-edit/' . $idPorto) ?>">
                                    Selanjutnya <i class="ti ti-arrow-right"></i>
                                </a>
                            <?php else: ?>
                                <a class="btn btn-secondary" href="<?= base_url('portofolio-form/info-matkul') ?>">
                                    <i class="ti ti-arrow-left"></i> Kembali
                                </a>
                                <a class="btn btn-primary" href="<?= base_url('portofolio-form/cpmk-subcpmk') ?>">
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
    document.addEventListener('DOMContentLoaded', function() {
        const kurikulumFilter = document.getElementById('kurikulumFilter');
        const tableBody = document.querySelector('table.table-bordered tbody');
        
        if (kurikulumFilter) {
            kurikulumFilter.addEventListener('change', function() {
                const selectedKurikulum = this.value;
                
                // Show loading state
                const originalContent = tableBody.innerHTML;
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0">Memuat data CPL & PI untuk kurikulum ${selectedKurikulum}...</p>
                        </td>
                    </tr>
                `;
                
                // Fetch data via AJAX
                fetch('<?= base_url('portofolio-form/api/get-cpl-pi') ?>?kode_matkul=<?= $kodeMatkul ?? '' ?>&kurikulum=' + encodeURIComponent(selectedKurikulum))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            renderCplPiTable(data.cplPiData);
                            
                            // Update URL without reload
                            const url = new URL(window.location);
                            url.searchParams.set('kurikulum', selectedKurikulum);
                            window.history.pushState({}, '', url);
                        } else {
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="3" class="text-center text-danger">
                                        <i class="ti ti-alert-circle fs-1"></i>
                                        <p class="mt-2">Gagal memuat data CPL & PI</p>
                                    </td>
                                </tr>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center text-danger">
                                    <i class="ti ti-alert-circle fs-1"></i>
                                    <p class="mt-2">Terjadi kesalahan saat memuat data</p>
                                </td>
                            </tr>
                        `;
                    });
            });
        }
    });
    
    function renderCplPiTable(cplPiData) {
        const tableBody = document.querySelector('table.table-bordered tbody');
        let html = '';
        
        const cplEntries = Object.entries(cplPiData);
        
        if (cplEntries.length === 0) {
            html = `
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data CPL dan PI untuk kurikulum ini.</td>
                </tr>
            `;
        } else {
            cplEntries.forEach(([cplNo, cplData]) => {
                const rowCount = Math.max(cplData.pi_list.length, 1);
                
                // First row with CPL and first PI
                html += `<tr>`;
                html += `<td rowspan="${rowCount}" style="white-space: nowrap;"><strong>CPL ${cplNo}</strong></td>`;
                html += `<td rowspan="${rowCount}">${cplData.cpl_indo}</td>`;
                
                if (cplData.pi_list && cplData.pi_list.length > 0) {
                    html += `<td>${cplData.pi_list[0]}</td>`;
                } else {
                    html += `<td>-</td>`;
                }
                html += `</tr>`;
                
                // Remaining PI rows
                for (let i = 1; i < cplData.pi_list.length; i++) {
                    html += `<tr><td>${cplData.pi_list[i]}</td></tr>`;
                }
            });
        }
        
        tableBody.innerHTML = html;
    }
</script>

<?= $this->include('backend/partials/footer') ?>