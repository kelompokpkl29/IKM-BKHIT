<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Manajemen Kuesioner<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Manajemen Kuesioner</h1>
<a href="<?= base_url('admin/kuesioner/create') ?>" class="btn btn-primary mb-3">Tambah Kuesioner Baru</a>

<?php
// Dummy data untuk daftar kuesioner admin
$kuesioner_admin_dummy = [
    ['id' => 1, 'nama' => 'Kuesioner Pelayanan Administrasi', 'deskripsi' => 'Survei kepuasan terhadap proses dan kecepatan pelayanan administrasi.', 'active' => true],
    ['id' => 2, 'nama' => 'Kuesioner Fasilitas Umum', 'deskripsi' => 'Survei tingkat kepuasan terhadap ketersediaan dan kebersihan fasilitas umum.', 'active' => true],
    ['id' => 3, 'nama' => 'Kuesioner Layanan Online', 'deskripsi' => 'Survei kepuasan penggunaan platform layanan online.', 'active' => false],
    ['id' => 4, 'nama' => 'Kuesioner Kualitas Informasi', 'deskripsi' => 'Survei kepuasan terhadap kejelasan dan akurasi informasi yang diberikan.', 'active' => true],
];
?>

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
                    <?php if (empty($kuesioner_admin_dummy)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data kuesioner (demo).</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($kuesioner_admin_dummy as $item) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['nama'] ?></td>
                                <td><?= $item['deskripsi'] ?></td>
                                <td>
                                    <?php if ($item['active']) : ?>
                                        <span class="badge bg-success text-white">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary text-white">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/pertanyaan/' . $item['id']) ?>" class="btn btn-info btn-sm">Pertanyaan</a>
                                    <a href="<?= base_url('admin/kuesioner/edit/' . $item['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="<?= base_url('admin/kuesioner/delete/' . $item['id']) ?>" class="btn btn-danger btn-sm" onclick="alert('Hapus Kuesioner ini? (Demo)')">Hapus</a>
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