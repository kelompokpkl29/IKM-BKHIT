<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Edit Profil Admin<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Edit Profil Admin</h1>
<?php
// Dummy data untuk profil admin
$admin_username_dummy = "admin";
$admin_email_dummy = "admin@example.com";
?>
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold">Informasi Profil</h6>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/profile/update') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="username" name="username" value="<?= $admin_username_dummy ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $admin_email_dummy ?>" required>
            </div>
            <hr>
            <h5>Ubah Password (Kosongkan jika tidak ingin mengubah)</h5>
            <div class="mb-3">
                <label for="old_password" class="form-label">Password Lama</label>
                <input type="password" class="form-control" id="old_password" name="old_password">
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
            </div>
            <div class="mb-3">
                <label for="confirm_new_password" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>