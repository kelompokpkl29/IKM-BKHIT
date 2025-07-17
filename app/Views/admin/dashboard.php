<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Dashboard Admin</h1>
<p class="lead">Selamat datang, Admin (Demo)!</p>

<?php
// Dummy data untuk dashboard
$totalKuesionerDummy = 12;
$activeKuesionerDummy = 8;
$totalRespondenDummy = 2345;
$ikmAverageDummy = 3.92; // Skala 1-5
$recentKuesionerDummy = [
    ['nama' => 'Kuesioner Pelayanan Publik', 'active' => true],
    ['nama' => 'Kuesioner Kepuasan Internal', 'active' => false],
    ['nama' => 'Kuesioner Produk Baru', 'active' => true],
    ['nama' => 'Kuesioner Pengalaman Pengguna Website', 'active' => true],
    ['nama' => 'Kuesioner Kecepatan Respon Layanan', 'active' => true],
];
?>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Jumlah Kuesioner</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $totalKuesionerDummy ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Responden</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $totalRespondenDummy ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            IKM Rata-rata</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($ikmAverageDummy, 2) ?> / 5.0
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Kuesioner Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $activeKuesionerDummy ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Kuesioner Terbaru</h6>
            </div>
            <div class="card-body">
                <?php if (empty($recentKuesionerDummy)): ?>
                    <p class="text-muted">Belum ada kuesioner yang ditambahkan.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($recentKuesionerDummy as $k): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= $k['nama'] ?>
                                <span class="badge bg-<?= $k['active'] ? 'success' : 'secondary' ?>"><?= $k['active'] ? 'Aktif' : 'Tidak Aktif' ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Statistik Ringkas</h6>
            </div>
            <div class="card-body">
                <p>Grafik dan visualisasi data akan ditampilkan di sini (demo).</p>
                <div style="height: 200px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; color: #6c757d; border-radius: 5px;">
                    (Placeholder Grafik)
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>