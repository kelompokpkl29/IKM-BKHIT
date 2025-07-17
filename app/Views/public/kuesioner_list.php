<?= $this->extend('layout/main_template') ?>

<?= $this->section('title') ?>Daftar Kuesioner<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container py-5" style="margin-top: 80px;">
        <h1 class="text-center mb-4">Daftar Kuesioner Tersedia</h1>
        <p class="lead text-center mb-5">Pilih kuesioner yang ingin Anda isi. Kontribusi Anda sangat berarti.</p>

        <div class="row">
            <?php
            // Dummy data untuk daftar kuesioner
            $kuesioner_list_dummy = [
                ['id' => 1, 'nama' => 'Kuesioner Pelayanan Administrasi', 'deskripsi' => 'Survei kepuasan terhadap proses dan kecepatan pelayanan administrasi.', 'active' => true],
                ['id' => 2, 'nama' => 'Kuesioner Fasilitas Umum', 'deskripsi' => 'Survei tingkat kepuasan terhadap ketersediaan dan kebersihan fasilitas umum.', 'active' => true],
                ['id' => 3, 'nama' => 'Kuesioner Kualitas Informasi', 'deskripsi' => 'Survei kepuasan terhadap kejelasan dan akurasi informasi yang diberikan.', 'active' => true],
                ['id' => 4, 'nama' => 'Kuesioner Layanan Online', 'deskripsi' => 'Survei kepuasan penggunaan platform layanan online.', 'active' => false],
            ];
            ?>

            <?php if (empty($kuesioner_list_dummy)): ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info" role="alert">
                        Tidak ada kuesioner aktif saat ini.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($kuesioner_list_dummy as $item): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary"><?= $item['nama'] ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?= $item['deskripsi'] ?></p>
                                <p class="card-text"><small class="text-<?= $item['active'] ? 'success' : 'danger' ?>">Status: <?= $item['active'] ? 'Aktif' : 'Tidak Aktif' ?></small></p>
                                <?php if ($item['active']): ?>
                                    <a href="<?= base_url('kuesioner/isi/' . $item['id']) ?>" class="btn btn-primary mt-2">Isi Kuesioner</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary mt-2 disabled">Tidak Aktif</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>