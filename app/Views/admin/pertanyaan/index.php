<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Pertanyaan: <?= esc($kuesioner['nama_kuesioner']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Pertanyaan untuk Kuesioner: "<?= esc($kuesioner['nama_kuesioner']) ?>"</h1>
<a href="<?= base_url('admin/kuesioner') ?>" class="btn btn-secondary mb-3">Kembali ke Kuesioner</a>
<a href="<?= base_url('admin/pertanyaan/create/' . $kuesioner['id']) ?>" class="btn btn-primary mb-3">Tambah Pertanyaan Baru</a>

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
                    <?php if (empty($pertanyaan)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pertanyaan untuk kuesioner ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($pertanyaan as $item) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($item['urutan']) ?></td>
                                <td><?= esc($item['teks_pertanyaan']) ?></td>
                                <td><?= esc($item['jenis_jawaban']) ?></td>
                                <td>
                                    <?php if ($item['jenis_jawaban'] !== 'isian' && !empty($item['opsi'])): ?>
                                        <ul class="list-unstyled mb-0">
                                            <?php foreach ($item['opsi'] as $opsi): ?>
                                                <li><?= esc($opsi['opsi_teks']) ?> <?= isset($opsi['nilai']) && $opsi['nilai'] !== null ? '(' . esc($opsi['nilai']) . ')' : '' ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php elseif ($item['jenis_jawaban'] === 'isian'): ?>
                                        <span class="text-muted">Isian Teks</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/pertanyaan/edit/' . $item['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="<?= base_url('admin/pertanyaan/delete/' . $item['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini? Ini akan menghapus semua jawaban terkait!')">Hapus</a>
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