<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Edit Kuesioner<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Edit Kuesioner</h1>
<a href="<?= base_url('admin/kuesioner') ?>" class="btn btn-secondary mb-3">Kembali ke Daftar Kuesioner</a>

<?php
// Dummy data untuk edit kuesioner
$edited_kuesioner_name_dummy = "Kuesioner Pelayanan Administrasi (Edit Demo)";
$edited_kuesioner_desc_dummy = "Deskripsi yang sudah diperbarui untuk kuesioner ini.";
$edited_kuesioner_active_dummy = true;
?>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= base_url('admin/kuesioner/update/' . (isset($kuesioner_id) ? $kuesioner_id : 1)) ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="nama_kuesioner" class="form-label">Nama Kuesioner <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_kuesioner" name="nama_kuesioner" value="<?= $edited_kuesioner_name_dummy ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= $edited_kuesioner_desc_dummy ?></textarea>
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= $edited_kuesioner_active_dummy ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">Aktif</label>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>