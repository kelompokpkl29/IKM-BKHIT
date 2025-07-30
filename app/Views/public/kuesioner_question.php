<?= $this->extend('layout/main_template') ?>

<?= $this->section('title') ?>Isi Kuesioner: <?= esc($kuesioner['nama_kuesioner']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container kuesioner-container" style="margin-top: 100px;">
        <div class="text-center kuesioner-header">
            <h2>Kuesioner: <?= esc($kuesioner['nama_kuesioner']) ?></h2>
            <p class="lead"><?= esc($kuesioner['deskripsi']) ?></p>
            <hr>
        </div>

        <?php if (empty($currentQuestion)): ?>
            <div class="alert alert-info text-center" role="alert">
                Maaf, pertanyaan tidak ditemukan atau kuesioner sudah selesai.
            </div>
        <?php else: ?>
            <?php
            // Tentukan apakah ini pertanyaan terakhir
            $isLastQuestion = ($questionNumber == $totalQuestions);
            ?>
            <form action="<?= $isLastQuestion ? base_url('kuesioner/submit_final') : base_url('kuesioner/process_answer') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="kuesioner_id" value="<?= esc($kuesioner['id']) ?>">
                <input type="hidden" name="question_id" value="<?= esc($currentQuestion['id']) ?>">
                <input type="hidden" name="question_number" value="<?= esc($questionNumber) ?>">

                <div class="text-center mb-4">
                    <?php for ($i = 1; $i <= $totalQuestions; $i++): ?>
                        <span class="badge rounded-pill <?= $i == $questionNumber ? 'bg-primary' : 'bg-secondary' ?>" style="width: 15px; height: 15px; display: inline-block; margin: 0 3px;"></span>
                    <?php endfor; ?>
                </div>

                <div class="pertanyaan-item">
                    <label>
                        <?= esc($questionNumber) ?>. <?= esc($currentQuestion['teks_pertanyaan']) ?>
                        <?php if ($currentQuestion['jenis_jawaban'] !== 'isian'): ?>
                            <span class="text-danger">*</span>
                        <?php endif; ?>
                    </label>
                    <?php if ($currentQuestion['jenis_jawaban'] === 'skala' || $currentQuestion['jenis_jawaban'] === 'pilihan_ganda'): ?>
                        <?php if (empty($currentQuestion['opsi'])): ?>
                            <div class="alert alert-warning mt-2">Belum ada opsi jawaban untuk pertanyaan ini.</div>
                        <?php else: ?>
                            <div class="opsi-container mt-2">
                                <?php foreach ($currentQuestion['opsi'] as $o): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban_opsi_<?= esc($currentQuestion['id']) ?>" id="opsi_<?= esc($o['id']) ?>" value="<?= esc($o['id']) ?>" required
                                            <?= (isset($partialAnswers[$currentQuestion['id']]) && $partialAnswers[$currentQuestion['id']]['opsi_jawaban_id'] == $o['id']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="opsi_<?= esc($o['id']) ?>">
                                            <?= esc($o['opsi_teks']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif ($currentQuestion['jenis_jawaban'] === 'isian'): ?>
                        <textarea class="form-control" name="jawaban_isian_<?= esc($currentQuestion['id']) ?>" rows="3" placeholder="Tulis jawaban Anda di sini..."><?= (isset($partialAnswers[$currentQuestion['id']]) && $partialAnswers[$currentQuestion['id']]['jawaban_teks'] !== null) ? esc($partialAnswers[$currentQuestion['id']]['jawaban_teks']) : '' ?></textarea>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                    <button type="submit" formaction="<?= base_url('kuesioner/previous_question') ?>" class="btn btn-outline-secondary btn-lg" <?= $questionNumber == 1 ? 'disabled' : '' ?>>Kembali</button>
                    <button type="submit" class="btn btn-primary btn-lg"><?= $isLastQuestion ? 'Kirim Kuesioner' : 'Lanjut' ?></button>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?= $this->endSection() ?>