<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Hasil IKM<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Hasil Indeks Kepuasan Masyarakat</h1>
<p class="lead">Lihat rekapitulasi data dan analisis hasil kuesioner.</p>

<?php
// Data dari controller
$kuesionerList = $kuesionerList ?? [];
$totalRespondenHasil = $totalRespondenHasil ?? 0;
$ikmRataRataHasil = $ikmRataRataHasil ?? 0.0;
$persentasePuasHasil = $persentasePuasHasil ?? 0.0;
$detailHasilPertanyaan = $detailHasilPertanyaan ?? [];
?>

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold">Filter Hasil</h6>
    </div>
    <div class="card-body">
        <form class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="selectKuesioner" class="form-label">Pilih Kuesioner:</label>
                <select class="form-select" id="selectKuesioner" name="kuesioner_id">
                    <option value="">Semua Kuesioner</option>
                    <?php if (!empty($kuesionerList)): ?>
                        <?php foreach ($kuesionerList as $kuesioner): ?>
                            <option value="<?= esc($kuesioner['id']) ?>"><?= esc($kuesioner['nama_kuesioner']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="startDate" class="form-label">Tanggal Mulai:</label>
                <input type="date" class="form-control" id="startDate" name="start_date">
            </div>
            <div class="col-md-3">
                <label for="endDate" class="form-label">Tanggal Akhir:</label>
                <input type="date" class="form-control" id="endDate" name="end_date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4 mt-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold">Ringkasan Hasil</h6>
    </div>
    <div class="card-body">
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <h3>Total Responden</h3>
                <p class="display-5 text-primary"><?= esc($totalRespondenHasil) ?></p>
            </div>
            <div class="col-md-4">
                <h3>Nilai IKM Rata-rata</h3>
                <p class="display-5 text-success"><?= number_format(esc($ikmRataRataHasil), 2) ?> <small>/ 5.0</small></p>
            </div>
            <div class="col-md-4">
                <h3>Persentase Puas</h3>
                <p class="display-5 text-info"><?= esc($persentasePuasHasil) ?>%</p>
            </div>
        </div>
        <hr>
        <h5 class="mb-3">Detail Hasil Per Pertanyaan</h5>
        <?php if (empty($detailHasilPertanyaan)): ?>
            <p class="text-muted">Tidak ada data pertanyaan untuk kuesioner aktif yang ditampilkan.</p>
        <?php else: ?>
            <ul class="list-group mb-3">
                <?php foreach ($detailHasilPertanyaan as $detail): ?>
                    <li class="list-group-item">
                        <strong><?= esc($detail['teks_pertanyaan']) ?></strong>
                        <?php if ($detail['jenis_jawaban'] !== 'isian'): ?>
                            <ul class="list-group list-group-flush mt-2">
                                <?php foreach ($detail['statistik'] as $stat): ?>
                                    <?php if (isset($stat['opsi_teks'])): ?>
                                        <li class="list-group-item"><?= esc($stat['opsi_teks']) ?>: <?= esc($stat['count']) ?> (<?= esc($stat['percentage']) ?>%)</li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if (isset($detail['statistik']['rata_rata_nilai'])): ?>
                                    <li class="list-group-item font-weight-bold">Rata-rata Nilai: <?= number_format(esc($detail['statistik']['rata_rata_nilai']), 2) ?></li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <?php if (empty($detail['saran'])): ?>
                                <p class="text-muted small mt-2">Belum ada saran untuk pertanyaan ini.</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush mt-2">
                                    <?php foreach ($detail['saran'] as $saran): ?>
                                        <li class="list-group-item"><?= esc($saran['teks']) ?> <span class="text-muted" style="font-size: 0.8em;">(<?= esc($saran['timestamp']) ?>)</span></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a href="<?= base_url('admin/exportPdfHasil') ?>" class="btn btn-success mt-3" target="_blank">Export Data ke PDF</a>
    </div>
</div>
<?= $this->endSection() ?>