<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Tambah Pertanyaan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Tambah Pertanyaan untuk "Demo Kuesioner (ID: <?= isset($kuesioner_id) ? $kuesioner_id : '?' ?>)"</h1>
<a href="<?= base_url('admin/pertanyaan/' . (isset($kuesioner_id) ? $kuesioner_id : 1)) ?>" class="btn btn-secondary mb-3">Kembali ke Daftar Pertanyaan</a>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= base_url('admin/pertanyaan/store') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="kuesioner_id" value="<?= isset($kuesioner_id) ? $kuesioner_id : 1 ?>">

            <div class="mb-3">
                <label for="teks_pertanyaan" class="form-label">Teks Pertanyaan <span class="text-danger">*</span></label>
                <textarea class="form-control" id="teks_pertanyaan" name="teks_pertanyaan" rows="3" required>Pertanyaan baru saya (Demo)</textarea>
            </div>
            <div class="mb-3">
                <label for="jenis_jawaban" class="form-label">Jenis Jawaban <span class="text-danger">*</span></label>
                <select class="form-select" id="jenis_jawaban" name="jenis_jawaban" required>
                    <option value="">Pilih Jenis Jawaban</option>
                    <option value="skala" selected>Skala (e.g., 1-5)</option>
                    <option value="pilihan_ganda">Pilihan Ganda</option>
                    <option value="isian">Isian Teks</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="urutan" class="form-label">Urutan <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="urutan" name="urutan" value="5" required min="1">
            </div>

            <div id="opsi_jawaban_section">
                <h5 class="mb-3">Opsi Jawaban</h5>
                <div id="opsi_list">
                    <div class="input-group mb-2 opsi-item">
                        <input type="text" class="form-control" name="opsi_teks[]" placeholder="Opsi Teks" value="Sangat Setuju (Demo)" required>
                        <input type="number" class="form-control" name="opsi_nilai[]" placeholder="Nilai (optional)" value="5">
                        <button type="button" class="btn btn-danger remove-opsi">Hapus</button>
                    </div>
                    <div class="input-group mb-2 opsi-item">
                        <input type="text" class="form-control" name="opsi_teks[]" placeholder="Opsi Teks" value="Setuju (Demo)" required>
                        <input type="number" class="form-control" name="opsi_nilai[]" placeholder="Nilai (optional)" value="4">
                        <button type="button" class="btn btn-danger remove-opsi">Hapus</button>
                    </div>
                </div>
                <button type="button" class="btn btn-info btn-sm mt-2" id="add_opsi_btn">Tambah Opsi</button>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Simpan Pertanyaan</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisJawabanSelect = document.getElementById('jenis_jawaban');
    const opsiJawabanSection = document.getElementById('opsi_jawaban_section');
    const opsiList = document.getElementById('opsi_list');
    const addOpsiBtn = document.getElementById('add_opsi_btn');

    function toggleOpsiSection() {
        if (jenisJawabanSelect.value === 'skala' || jenisJawabanSelect.value === 'pilihan_ganda') {
            opsiJawabanSection.style.display = 'block';
            opsiList.querySelectorAll('input[name="opsi_teks[]"]').forEach(input => input.setAttribute('required', 'required'));
            if (opsiList.children.length === 0) { // Pastikan ada setidaknya satu opsi jika section aktif
                addOpsiItem();
            }
        } else {
            opsiJawabanSection.style.display = 'none';
            opsiList.querySelectorAll('input[name="opsi_teks[]"]').forEach(input => input.removeAttribute('required'));
        }
    }

    function addOpsiItem(teks = '', nilai = '') {
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2', 'opsi-item');
        div.innerHTML = `
            <input type="text" class="form-control" name="opsi_teks[]" placeholder="Opsi Teks" value="${teks}" required>
            <input type="number" class="form-control" name="opsi_nilai[]" placeholder="Nilai (optional)" value="${nilai}">
            <button type="button" class="btn btn-danger remove-opsi">Hapus</button>
        `;
        opsiList.appendChild(div);
    }

    jenisJawabanSelect.addEventListener('change', toggleOpsiSection);
    addOpsiBtn.addEventListener('click', () => addOpsiItem());
    opsiList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-opsi')) {
            e.target.closest('.opsi-item').remove();
            if (opsiList.children.length === 0 && (jenisJawabanSelect.value === 'skala' || jenisJawabanSelect.value === 'pilihan_ganda')) {
                 addOpsiItem();
            }
        }
    });

    // Panggil saat halaman dimuat untuk inisialisasi tampilan
    toggleOpsiSection();
});
</script>
<?= $this->endSection() ?>