<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Manajemen Kuesioner<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Manajemen Kuesioner</h1>
<a href="<?= base_url('admin/kuesioner/create') ?>" class="btn btn-primary mb-3">Tambah Kuesioner Baru</a>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Kuesioner</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kuesioner)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data kuesioner.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($kuesioner as $item) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($item['nama_kuesioner']) ?></td>
                                <td><?= esc($item['deskripsi']) ?></td>
                                <td>
                                    <?php if ($item['is_active']) : ?>
                                        <span class="badge bg-success text-white">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary text-white">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/pertanyaan/' . $item['id']) ?>" class="btn btn-info btn-sm">Pertanyaan</a>
                                    <a href="<?= base_url('admin/kuesioner/edit/' . $item['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="<?= base_url('admin/kuesioner/delete/' . $item['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kuesioner ini? Tindakan ini akan menghapus semua pertanyaan dan jawaban terkait!')">Hapus</a>
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