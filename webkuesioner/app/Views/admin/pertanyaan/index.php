<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Pertanyaan: Demo Kuesioner<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Pertanyaan untuk Kuesioner: "Demo Kuesioner (ID: <?= isset($kuesioner_id) ? $kuesioner_id : '?' ?>)"</h1>
<a href="<?= base_url('admin/kuesioner') ?>" class="btn btn-secondary mb-3">Kembali ke Kuesioner</a>
<a href="<?= base_url('admin/pertanyaan/create/' . (isset($kuesioner_id) ? $kuesioner_id : 1)) ?>" class="btn btn-primary mb-3">Tambah Pertanyaan Baru</a>

<?php
// Dummy data untuk pertanyaan
$pertanyaan_admin_dummy = [
    ['id' => 1, 'urutan' => 1, 'teks' => 'Bagaimana tingkat kemudahan dalam mengakses layanan kami?', 'jenis' => 'skala', 'opsi' => [
        ['teks' => 'Sangat Mudah', 'nilai' => 5], ['teks' => 'Mudah', 'nilai' => 4], ['teks' => 'Cukup Mudah', 'nilai' => 3],
    ]],
    ['id' => 2, 'urutan' => 2, 'teks' => 'Bagaimana kecepatan respon petugas dalam melayani kebutuhan Anda?', 'jenis' => 'skala', 'opsi' => [
        ['teks' => 'Sangat Cepat', 'nilai' => 5], ['teks' => 'Cepat', 'nilai' => 4],
    ]],
    ['id' => 3, 'urutan' => 3, 'teks' => 'Apakah Anda puas dengan keramahan petugas kami?', 'jenis' => 'pilihan_ganda', 'opsi' => [
        ['teks' => 'Sangat Puas'], ['teks' => 'Puas'],
    ]],
    ['id' => 4, 'urutan' => 4, 'teks' => 'Sebutkan saran atau masukan Anda:', 'jenis' => 'isian', 'opsi' => []],
];
?>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Urutan</th>
                        <th>Teks Pertanyaan</th>
                        <th>Jenis Jawaban</th>
                        <th>Opsi Jawaban</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pertanyaan_admin_dummy)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pertanyaan untuk kuesioner ini (demo).</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($pertanyaan_admin_dummy as $item) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['urutan'] ?></td>
                                <td><?= $item['teks'] ?></td>
                                <td><?= $item['jenis'] ?></td>
                                <td>
                                    <?php if ($item['jenis'] !== 'isian' && !empty($item['opsi'])): ?>
                                        <ul class="list-unstyled mb-0">
                                            <?php foreach ($item['opsi'] as $opsi): ?>
                                                <li><?= $opsi['teks'] ?> <?= isset($opsi['nilai']) && $opsi['nilai'] !== null ? '(' . $opsi['nilai'] . ')' : '' ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php elseif ($item['jenis'] === 'isian'): ?>
                                        <span class="text-muted">Isian Teks</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/pertanyaan/edit/' . $item['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="<?= base_url('admin/pertanyaan/delete/' . $item['id']) ?>" class="btn btn-danger btn-sm" onclick="alert('Hapus Pertanyaan ini? (Demo)')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>