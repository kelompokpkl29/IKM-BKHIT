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
            <form action="<?= base_url('kuesioner/submit') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="kuesioner_id" value="<?= isset($kuesioner_id) ? $kuesioner_id : 1 ?>">
                <?php foreach ($pertanyaan_dummy as $key => $p): ?>
                    <div class="pertanyaan-item">
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
                                <?php foreach ($p['opsi'] as $o): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban_opsi_<?= $p['id'] ?>" id="opsi_<?= $o['id'] ?>" value="<?= $o['id'] ?>" required>
                                        <label class="form-check-label" for="opsi_<?= $o['id'] ?>">
                                            <?= $o['teks'] ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php elseif ($p['jenis'] === 'isian'): ?>
                            <textarea class="form-control" name="jawaban_isian_<?= $p['id'] ?>" rows="3" placeholder="Tulis jawaban Anda di sini..."></textarea>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Kirim Kuesioner</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?= $this->endSection() ?>