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

    #cpmkChart {
        max-width: 650px;
        height: 400px;
        max-height: 400px;
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

                        <div class="step-line active"></div>

                        <!-- Evaluasi Perkuliahan -->
                        <div class="d-flex flex-column align-items-center text-center px-2">
                            <div class="step-circle active">
                                <i class="ti ti-chart-bar"></i>
                            </div>
                            <small class="d-block mt-2 step-label">Evaluasi Perkuliahan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluasi Perkuliahan -->
    <div class="row" data-step="topik">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Evaluasi Perkuliahan</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk mengisi evaluasi perkuliahan di bawah sebelum melanjutkan!
                        </div>
                    </div>

                    <div class="d-flex justify-content-center pt-3">
                        <canvas id="cpmkChart" width="400" height="200"></canvas>
                    </div>

                    <form id="topicForm" action="<?= base_url('portofolio-form/saveEvaluasiPerkuliahan') ?>" method="post">
                        <?php if (!empty($pdfUrl)): ?>
                            <div class="mb-3" style="height: 600px; border: 1px solid #ccc; margin-top: 20px;">
                                <iframe src="<?= esc($pdfUrl) ?>" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                        <?php endif; ?>

                        <div class="form-group mb-3">
                            <label for="evaluasi" class="form-label">Evaluasi Perkuliahan</label>
                            <textarea class="form-control" id="evaluasi" name="evaluasi" rows="3" placeholder="Masukkan evaluasi perkuliahan"><?= esc($evaluasi_perkuliahan) ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <a class="btn btn-secondary" href="<?= base_url('portofolio-form/hasil-asesmen') ?>">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Simpan <i class="ti ti-download"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalMessage">
                <!-- Pesan akan dimasukkan di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->getFlashdata('success')): ?>
            var modalMessage = document.getElementById('modalMessage');
            modalMessage.innerHTML = "<?= session()->getFlashdata('success') ?>";
            var messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
            messageModal.show();
        <?php endif; ?>
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.getElementById("topicForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Mencegah reload halaman

        let formData = new FormData(this);

        fetch("<?= base_url('portofolio-form/saveEvaluasiPerkuliahan') ?>", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Buat form tersembunyi untuk submit ke save-portofolio
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = data.redirect;

                    // Tambahkan CSRF token jika diperlukan
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '<?= csrf_token() ?>'; // Nama token CSRF
                    csrfToken.value = '<?= csrf_hash() ?>'; // Nilai token CSRF
                    form.appendChild(csrfToken);

                    document.body.appendChild(form);
                    form.submit(); // Kirim form secara otomatis
                } else {
                    alert(data.message);
                    console.error('Failed to save assessment data:', data.message);
                }
            })
            .catch(error => console.error("Error:", error));
    });
</script>

<script>
    const data = {
        labels: ['CPMK 1', 'CPMK 2', 'CPMK 3', 'CPMK 4'],
        datasets: [{
            label: 'Nilai CPMK',
            data: [3.5, 2.8, 3.2, 2.5],
            backgroundColor: 'rgba(15, 76, 146, 0.1)',
            borderColor: 'rgba(15, 76, 146, 1)',
            borderWidth: 1
        }]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4,
                    ticks: {
                        stepSize: 0.5
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Nilai Rerata CPMK',
                    font: {
                        size: 18
                    },
                    padding: {
                        top: 10,
                        bottom: 20
                    }
                }
            }
        },
        plugins: [{
            id: 'midLine',
            afterDraw: (chart) => {
                const {
                    ctx,
                    chartArea: {
                        top,
                        bottom,
                        left,
                        right
                    },
                    scales: {
                        y
                    }
                } = chart;
                const yValue = y.getPixelForValue(2); // Posisi garis pada y=2

                ctx.save();
                ctx.beginPath();
                ctx.moveTo(left, yValue);
                ctx.lineTo(right, yValue);
                ctx.strokeStyle = 'red';
                ctx.lineWidth = 2;
                ctx.setLineDash([5, 5]); // Garis putus-putus
                ctx.stroke();
                ctx.restore();
            }
        }]
    };

    const ctx = document.getElementById('cpmkChart').getContext('2d');
    const cpmkChart = new Chart(ctx, config);
</script>

<?= $this->include('backend/partials/footer') ?>