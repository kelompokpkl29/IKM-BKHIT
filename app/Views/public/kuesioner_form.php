<?= $this->extend('layout/main_template') ?>

<?= $this->section('title') ?>Isi Kuesioner: <?= esc($kuesioner['nama_kuesioner']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container kuesioner-container" style="margin-top: 100px;">
        <div class="text-center kuesioner-header">
            <h2>Kuesioner: <?= esc($kuesioner['nama_kuesioner']) ?></h2>
            <p class="lead"><?= esc($kuesioner['deskripsi']) ?></p>
            <hr>
        </div>

        <?php if (empty($pertanyaan)): ?>
            <div class="alert alert-info text-center" role="alert">
                Maaf, belum ada pertanyaan untuk kuesioner ini.
            </div>
        <?php else: ?>
            <form id="kuesionerForm" action="<?= base_url('kuesioner/submit') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="kuesioner_id" value="<?= esc($kuesioner['id']) ?>">
                <?php foreach ($pertanyaan as $key => $p): ?>
                    <div class="pertanyaan-item" id="pertanyaan-<?= $key ?>" style="<?= $key === 0 ? '' : 'display: none;' ?>">
                        <input type="hidden" name="pertanyaan_id[]" value="<?= esc($p['id']) ?>">
                        <label>
                            <?= ($key + 1) ?>. <?= esc($p['teks_pertanyaan']) ?>
                            <?php if ($p['jenis_jawaban'] !== 'isian'): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        <?php if ($p['jenis_jawaban'] === 'skala' || $p['jenis_jawaban'] === 'pilihan_ganda'): ?>
                            <?php if (empty($p['opsi'])): ?>
                                <div class="alert alert-warning mt-2">Belum ada opsi jawaban untuk pertanyaan ini.</div>
                            <?php else: ?>
                                <div class="opsi-container mt-2">
                                    <?php foreach ($p['opsi'] as $o): ?>
                                        <div class="form-check">
                                            <input class="form-check-input question-radio" type="radio" name="jawaban_opsi_<?= esc($p['id']) ?>" id="opsi_<?= esc($o['id']) ?>" value="<?= esc($o['id']) ?>" required data-current-question="<?= $key ?>">
                                            <label class="form-check-label" for="opsi_<?= esc($o['id']) ?>">
                                                <?= esc($o['opsi_teks']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($p['jenis_jawaban'] === 'isian'): ?>
                            <textarea class="form-control" name="jawaban_isian_<?= esc($p['id']) ?>" rows="3" placeholder="Tulis jawaban Anda di sini..."></textarea>
                            <div class="d-grid gap-2 mt-3">
                                <button type="button" class="btn btn-primary btn-lg lanjut-button" data-current-question="<?= $key ?>">Lanjut</button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="d-grid gap-2 mt-4" id="submitButtonContainer" style="display: none;">
                    <button type="submit" class="btn btn-success btn-lg">Kirim Kuesioner</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalQuestions = document.querySelectorAll('.pertanyaan-item').length;
            const pertanyaanItems = document.querySelectorAll('.pertanyaan-item');
            const submitButtonContainer = document.getElementById('submitButtonContainer');

            // Fungsi untuk menampilkan pertanyaan berikutnya
            function showNextQuestion(currentQuestionIndex) {
                // Sembunyikan pertanyaan saat ini
                if (pertanyaanItems[currentQuestionIndex]) {
                    pertanyaanItems[currentQuestionIndex].style.display = 'none';
                }

                const nextQuestionIndex = currentQuestionIndex + 1;

                if (nextQuestionIndex < totalQuestions) {
                    // Tampilkan pertanyaan berikutnya
                    if (pertanyaanItems[nextQuestionIndex]) {
                        pertanyaanItems[nextQuestionIndex].style.display = 'block';
                        // Scroll ke atas halaman untuk pertanyaan baru
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                } else {
                    // Jika semua pertanyaan sudah dijawab, tampilkan tombol submit
                    if (submitButtonContainer) {
                        submitButtonContainer.style.display = 'block';
                        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); // Scroll ke bawah untuk tombol submit
                    }
                }
            }

            // Event listener untuk radio button
            document.querySelectorAll('.question-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const currentQuestionIndex = parseInt(this.dataset.currentQuestion);
                    showNextQuestion(currentQuestionIndex);
                });
            });

            // Event listener untuk tombol "Lanjut" pada pertanyaan isian
            document.querySelectorAll('.lanjut-button').forEach(button => {
                button.addEventListener('click', function() {
                    const currentQuestionIndex = parseInt(this.dataset.currentQuestion);
                    const textarea = pertanyaanItems[currentQuestionIndex].querySelector('textarea');
                    // Cek apakah isian kosong sebelum melanjutkan
                    if (textarea && textarea.value.trim() === '') {
                        alert('Mohon isi jawaban Anda sebelum melanjutkan.');
                        return; // Jangan lanjutkan jika kosong
                    }
                    showNextQuestion(currentQuestionIndex);
                });
            });

            // Inisialisasi tampilan kuesioner saat dimuat
            if (totalQuestions > 0) {
                // Sembunyikan semua kecuali pertanyaan pertama
                for (let i = 0; i < totalQuestions; i++) {
                    if (pertanyaanItems[i]) {
                        pertanyaanItems[i].style.display = 'none';
                    }
                }
                if (pertanyaanItems[0]) {
                    pertanyaanItems[0].style.display = 'block';
                }
            }
            // Pastikan tombol submit tersembunyi di awal
            if (submitButtonContainer) {
                submitButtonContainer.style.display = 'none';
            }
        });
    </script>
<?= $this->endSection() ?>