<?= $this->extend('layout/main_template') ?>

<?= $this->section('title') ?>Daftar Kuesioner<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container py-5" style="margin-top: 80px;">
        <h1 class="text-center mb-4">Daftar Kuesioner Tersedia</h1>
        <p class="lead text-center mb-5">Pilih kuesioner yang ingin Anda isi. Kontribusi Anda sangat berarti.</p>

        <div class="row">
            <?php if (empty($kuesioner)): ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info" role="alert">
                        Tidak ada kuesioner aktif saat ini.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($kuesioner as $item): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary"><?= esc($item['nama_kuesioner']) ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?= esc($item['deskripsi']) ?></p>
                                <p class="card-text"><small class="text-<?= $item['is_active'] ? 'success' : 'danger' ?>">Status: <?= $item['is_active'] ? 'Aktif' : 'Tidak Aktif' ?></small></p>
                                <?php if ($item['is_active']): ?>
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