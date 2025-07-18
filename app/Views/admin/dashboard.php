<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Dashboard Admin</h1>
<p class="lead">Selamat datang, Admin (<?php if(session()->get('logged_in')) echo session()->get('username'); else echo 'Guest'; ?>)!</p>

<?php
// Data dashboard diambil dari controller (DB)
$totalKuesioner = $totalKuesioner ?? 0;
$activeKuesioner = $activeKuesioner ?? 0;
$totalResponden = $totalResponden ?? 0;
$ikmAverage = $ikmAverage ?? 0.0;
$recentKuesioner = $recentKuesioner ?? [];
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
                            <?= esc($totalKuesioner) ?>
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
                            <?= esc($totalResponden) ?>
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
                            <?= number_format(esc($ikmAverage), 2) ?> / 5.0
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
                            <?= esc($activeKuesioner) ?>
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
                <?php if (empty($recentKuesioner)): ?>
                    <p class="text-muted">Belum ada kuesioner yang ditambahkan.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($recentKuesioner as $k): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= esc($k['nama_kuesioner']) ?>
                                <span class="badge bg-<?= $k['is_active'] ? 'success' : 'secondary' ?>"><?= esc($k['is_active'] ? 'Aktif' : 'Tidak Aktif') ?></span>
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
                <p>Grafik dan visualisasi data akan ditampilkan di sini.</p>
                <div style="height: 200px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; color: #6c757d; border-radius: 5px;">
                    (Placeholder Grafik)
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>