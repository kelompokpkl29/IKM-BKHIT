<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Hasil IKM<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Hasil Indeks Kepuasan Masyarakat</h1>
<p class="lead">Lihat rekapitulasi data dan analisis hasil kuesioner.</p>

<?php
// Dummy data untuk halaman hasil
$totalRespondenHasilDummy = 850;
$ikmRataRataHasilDummy = 3.75;
$persentasePuasHasilDummy = 88;

$kuesionerListHasilDummy = [
    ['id' => 1, 'nama' => 'Kuesioner Pelayanan Administrasi'],
    ['id' => 2, 'nama' => 'Kuesioner Fasilitas Umum'],
    ['id' => 3, 'nama' => 'Kuesioner Kualitas Informasi'],
];
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
                    <?php foreach ($kuesionerListHasilDummy as $kuesioner): ?>
                        <option value="<?= $kuesioner['id'] ?>"><?= $kuesioner['nama'] ?></option>
                    <?php endforeach; ?>
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
                <p class="display-5 text-primary"><?= $totalRespondenHasilDummy ?></p>
            </div>
            <div class="col-md-4">
                <h3>Nilai IKM Rata-rata</h3>
                <p class="display-5 text-success"><?= number_format($ikmRataRataHasilDummy, 2) ?> <small>/ 4.0</small></p>
            </div>
            <div class="col-md-4">
                <h3>Persentase Puas</h3>
                <p class="display-5 text-info"><?= $persentasePuasHasilDummy ?>%</p>
            </div>
        </div>
        <hr>
        <h5 class="mb-3">Detail Hasil Per Pertanyaan (Contoh)</h5>
        <ul class="list-group mb-3">
            <li class="list-group-item">
                <strong>Bagaimana tingkat kemudahan dalam mengakses layanan kami?</strong>
                <ul class="list-group list-group-flush mt-2">
                    <li class="list-group-item">Sangat Mudah: 300 (35%)</li>
                    <li class="list-group-item">Mudah: 400 (47%)</li>
                    <li class="list-group-item">Cukup Mudah: 100 (12%)</li>
                    <li class="list-group-item">Sulit: 30 (4%)</li>
                    <li class="list-group-item">Sangat Sulit: 20 (2%)</li>
                    <li class="list-group-item">Rata-rata Nilai: 3.9</li>
                </ul>
            </li>
            <li class="list-group-item mt-3">
                <strong>Sebutkan saran atau masukan Anda untuk peningkatan layanan kami:</strong>
                <p class="text-muted small mt-2">
                    "Perlu ditingkatkan kecepatan dalam pengurusan dokumen." (12 Mei 2025) <br>
                    "Petugas sangat ramah dan membantu." (10 Mei 2025) <br>
                    "Website sering error saat jam sibuk." (08 Mei 2025)
                </p>
                <button class="btn btn-sm btn-outline-info">Lihat Semua Saran</button>
            </li>
        </ul>
        <button class="btn btn-success mt-3">Export Data ke CSV</button>
    </div>
</div>
<?= $this->endSection() ?>