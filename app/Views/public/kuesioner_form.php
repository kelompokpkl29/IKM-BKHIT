<?= $this->extend('layout/main_template') ?>

<?= $this->section('title') ?>Isi Kuesioner Demo<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container kuesioner-container" style="margin-top: 100px;">
        <div class="text-center kuesioner-header">
            <?php
            // Dummy data untuk kuesioner yang diisi
            $kuesioner_name_dummy = "Kuesioner Pelayanan Administrasi (Demo)";
            $kuesioner_desc_dummy = "Mohon luangkan waktu Anda untuk mengisi kuesioner ini. Pendapat Anda sangat berarti bagi kami.";

            // Dummy data untuk pertanyaan
            $pertanyaan_dummy = [
                ['id' => 1, 'teks' => 'Bagaimana tingkat kemudahan dalam mengakses layanan kami?', 'jenis' => 'skala', 'opsi' => [
                    ['id' => 101, 'teks' => 'Sangat Mudah', 'nilai' => 5],
                    ['id' => 102, 'teks' => 'Mudah', 'nilai' => 4],
                    ['id' => 103, 'teks' => 'Cukup Mudah', 'nilai' => 3],
                    ['id' => 104, 'teks' => 'Sulit', 'nilai' => 2],
                    ['id' => 105, 'teks' => 'Sangat Sulit', 'nilai' => 1],
                ]],
                ['id' => 2, 'teks' => 'Bagaimana kecepatan respon petugas dalam melayani kebutuhan Anda?', 'jenis' => 'skala', 'opsi' => [
                    ['id' => 201, 'teks' => 'Sangat Cepat', 'nilai' => 5],
                    ['id' => 202, 'teks' => 'Cepat', 'nilai' => 4],
                    ['id' => 203, 'teks' => 'Cukup Cepat', 'nilai' => 3],
                    ['id' => 204, 'teks' => 'Lambat', 'nilai' => 2],
                    ['id' => 205, 'teks' => 'Sangat Lambat', 'nilai' => 1],
                ]],
                ['id' => 3, 'teks' => 'Apakah Anda puas dengan keramahan petugas kami?', 'jenis' => 'pilihan_ganda', 'opsi' => [
                    ['id' => 301, 'teks' => 'Sangat Puas', 'nilai' => null],
                    ['id' => 302, 'teks' => 'Puas', 'nilai' => null],
                    ['id' => 303, 'teks' => 'Cukup Puas', 'nilai' => null],
                    ['id' => 304, 'teks' => 'Kurang Puas', 'nilai' => null],
                    ['id' => 305, 'teks' => 'Tidak Puas', 'nilai' => null],
                ]],
                ['id' => 4, 'teks' => 'Sebutkan saran atau masukan Anda untuk peningkatan layanan kami:', 'jenis' => 'isian', 'opsi' => []],
            ];
            ?>

            <h2>Kuesioner: <?= $kuesioner_name_dummy ?></h2>
            <p class="lead"><?= $kuesioner_desc_dummy ?></p>
            <hr>
        </div>

        <?php if (empty($pertanyaan_dummy)): ?>
            <div class="alert alert-info text-center" role="alert">
                Maaf, belum ada pertanyaan untuk kuesioner ini (demo).
            </div>
        <?php else: ?>
            <form id="kuesionerForm" action="<?= base_url('kuesioner/submit') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="kuesioner_id" value="<?= isset($kuesioner_id) ? $kuesioner_id : 1 ?>">
                <?php foreach ($pertanyaan_dummy as $key => $p): ?>
                    <div class="pertanyaan-item" id="pertanyaan-<?= $key ?>" style="<?= $key === 0 ? '' : 'display: none;' ?>">
                        <input type="hidden" name="pertanyaan_id[]" value="<?= $p['id'] ?>">
                        <label>
                            <?= ($key + 1) ?>. <?= $p['teks'] ?>
                            <?php if ($p['jenis'] !== 'isian'): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        <?php if ($p['jenis'] === 'skala' || $p['jenis'] === 'pilihan_ganda'): ?>
                            <?php if (empty($p['opsi'])): ?>
                                <div class="alert alert-warning mt-2">Belum ada opsi jawaban untuk pertanyaan ini (demo).</div>
                            <?php else: ?>
                                <div class="opsi-container mt-2">
                                    <?php foreach ($p['opsi'] as $o): ?>
                                        <div class="form-check">
                                            <input class="form-check-input question-radio" type="radio" name="jawaban_opsi_<?= $p['id'] ?>" id="opsi_<?= $o['id'] ?>" value="<?= $o['id'] ?>" required data-current-question="<?= $key ?>">
                                            <label class="form-check-label" for="opsi_<?= $o['id'] ?>">
                                                <?= $o['teks'] ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($p['jenis'] === 'isian'): ?>
                            <textarea class="form-control" name="jawaban_isian_<?= $p['id'] ?>" rows="3" placeholder="Tulis jawaban Anda di sini..."></textarea>
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
            const totalQuestions = <?= count($pertanyaan_dummy) ?>;
            const pertanyaanItems = document.querySelectorAll('.pertanyaan-item');
            const submitButtonContainer = document.getElementById('submitButtonContainer');

            // Fungsi untuk menampilkan pertanyaan berikutnya
            function showNextQuestion(currentQuestionIndex) {
                // Sembunyikan pertanyaan saat ini
                pertanyaanItems[currentQuestionIndex].style.display = 'none';

                const nextQuestionIndex = currentQuestionIndex + 1;

                if (nextQuestionIndex < totalQuestions) {
                    // Tampilkan pertanyaan berikutnya
                    pertanyaanItems[nextQuestionIndex].style.display = 'block';
                    // Scroll ke atas halaman untuk pertanyaan baru
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    // Jika semua pertanyaan sudah dijawab, tampilkan tombol submit
                    submitButtonContainer.style.display = 'block';
                    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }); // Scroll ke bawah untuk tombol submit
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
                    // Opsional: Anda bisa menambahkan validasi di sini sebelum melanjutkan
                    if (textarea && textarea.value.trim() === '') {
                        alert('Mohon isi jawaban Anda sebelum melanjutkan.');
                        return;
                    }
                    showNextQuestion(currentQuestionIndex);
                });
            });
        });
    </script>
<?= $this->endSection() ?>