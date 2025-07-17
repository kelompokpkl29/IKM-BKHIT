<?= $this->extend('layout/main_template') ?>

<?= $this->section('title') ?>Beranda<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <header class="bg-primary text-white text-center py-5" style="padding-top: 10rem !important; padding-bottom: 8rem !important;">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Selamat Datang di Portal Indeks Kepuasan Masyarakat</h1>
            <p class="lead mb-4">Suara Anda penting bagi peningkatan kualitas layanan publik kami.</p>
            <a href="<?= base_url('kuesioner') ?>" class="btn btn-light btn-lg px-4 me-2">Isi Kuesioner Sekarang</a>
            <a href="#" class="btn btn-outline-light btn-lg px-4">Pelajari Lebih Lanjut</a>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="card p-4">
                        <div class="card-body">
                            <h3 class="card-title mb-3">Mudah & Cepat</h3>
                            <p class="card-text">Isi kuesioner kapan saja, di mana saja dengan mudah melalui perangkat Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card p-4">
                        <div class="card-body">
                            <h3 class="card-title mb-3">Anonim & Aman</h3>
                            <p class="card-text">Privasi Anda terjamin. Semua jawaban dikumpulkan secara anonim.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card p-4">
                        <div class="card-body">
                            <h3 class="card-title mb-3">Berpengaruh</h3>
                            <p class="card-text">Kontribusi Anda membantu kami meningkatkan kualitas layanan secara berkelanjutan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-3">Tentang IKM</h2>
                    <p class="lead">Indeks Kepuasan Masyarakat (IKM) adalah ukuran tingkat kepuasan masyarakat terhadap layanan publik yang diberikan oleh instansi pemerintah. Hasil IKM menjadi acuan penting untuk evaluasi dan perbaikan.</p>
                    <p>Kami berkomitmen untuk terus meningkatkan kualitas layanan berdasarkan masukan berharga dari Anda.</p>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x400?text=Illustrasi+IKM" class="img-fluid rounded shadow-lg" alt="IKM Illustration">
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>