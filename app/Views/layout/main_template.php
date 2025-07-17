<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> | IKM Survey</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">IKM Survey</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= (url_is('/') || url_is('home')) ? 'active' : '' ?>" href="<?= base_url('/') ?>">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (url_is('kuesioner') || url_is('kuesioner/*')) ? 'active' : '' ?>" href="<?= base_url('kuesioner') ?>">Isi Kuesioner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('login') ? 'active' : '' ?>" href="<?= base_url('login') ?>">Login Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="container mt-5 pt-5">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="container mt-5 pt-5">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('validation')) : ?>
        <div class="container mt-5 pt-5">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('validation')->listErrors() ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 IKM Survey. All rights reserved.</p>
        </div>
    </footer>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>