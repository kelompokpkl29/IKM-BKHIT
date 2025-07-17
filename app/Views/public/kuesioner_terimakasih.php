<?= $this->extend('layout/main_template') ?>

<?= $this->section('title') ?>Terima Kasih!<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="container thankyou-container">
        <div class="card p-5 shadow-lg">
            <div class="card-body">
                <h1 class="card-title text-success mb-3">Terima Kasih!</h1>
                <p class="card-text lead">Kuesioner Anda telah berhasil kami terima.</p>
                <p class="card-text">Partisipasi Anda sangat berharga bagi kami dalam meningkatkan kualitas layanan.</p>
                <a href="<?= base_url('/') ?>" class="btn btn-primary mt-4">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>