<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Tambah Kuesioner<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Tambah Kuesioner Baru</h1>
<a href="<?= base_url('admin/kuesioner') ?>" class="btn btn-secondary mb-3">Kembali ke Daftar Kuesioner</a>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Kuesioner</h6>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php $validation = session()->getFlashdata('validation'); ?>

        <form action="<?= base_url('admin/kuesioner/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="nama_kuesioner" class="form-label">Nama Kuesioner:</label>
                <input type="text" class="form-control <?= $validation && $validation->hasError('nama_kuesioner') ? 'is-invalid' : '' ?>" id="nama_kuesioner" name="nama_kuesioner" value="<?= old('nama_kuesioner') ?>" required>
                <?php if ($validation && $validation->hasError('nama_kuesioner')) : ?>
                    <div class="invalid-feedback">
                        <?= $validation->getError('nama_kuesioner') ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi:</label>
                <textarea class="form-control <?= $validation && $validation->hasError('deskripsi') ? 'is-invalid' : '' ?>" id="deskripsi" name="deskripsi" rows="3"><?= old('deskripsi') ?></textarea>
                <?php if ($validation && $validation->hasError('deskripsi')) : ?>
                    <div class="invalid-feedback">
                        <?= $validation->getError('deskripsi') ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= old('is_active', true) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">Aktif</label>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Kuesioner</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>