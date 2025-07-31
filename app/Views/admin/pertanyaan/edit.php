<?= $this->extend('layout/admin_template') ?>

<?= $this->section('title') ?>Edit Pertanyaan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mt-4">Edit Pertanyaan untuk Kuesioner: "<?= esc($kuesioner['nama_kuesioner']) ?>"</h1>
<a href="<?= base_url('admin/pertanyaan/' . $kuesioner['id']) ?>" class="btn btn-secondary mb-3">Kembali ke Daftar Pertanyaan</a>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Edit Pertanyaan</h6>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php $validation = session()->getFlashdata('validation'); ?>

        <form action="<?= base_url('admin/pertanyaan/update/' . $pertanyaan['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="kuesioner_id" value="<?= esc($kuesioner['id']) ?>">

            <div class="mb-3">
                <label for="teks_pertanyaan" class="form-label">Teks Pertanyaan:</label>
                <textarea class="form-control <?= $validation && $validation->hasError('teks_pertanyaan') ? 'is-invalid' : '' ?>" id="teks_pertanyaan" name="teks_pertanyaan" rows="3" required><?= old('teks_pertanyaan', $pertanyaan['teks_pertanyaan']) ?></textarea>
                <?php if ($validation && $validation->hasError('teks_pertanyaan')) : ?>
                    <div class="invalid-feedback">
                        <?= $validation->getError('teks_pertanyaan') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="jenis_jawaban" class="form-label">Jenis Jawaban:</label>
                <select class="form-select <?= $validation && $validation->hasError('jenis_jawaban') ? 'is-invalid' : '' ?>" id="jenis_jawaban" name="jenis_jawaban" required>
                    <option value="">Pilih Jenis Jawaban</option>
                    <option value="skala" <?= old('jenis_jawaban', $pertanyaan['jenis_jawaban']) == 'skala' ? 'selected' : '' ?>>Skala (misal: 1-5)</option>
                    <option value="pilihan_ganda" <?= old('jenis_jawaban', $pertanyaan['jenis_jawaban']) == 'pilihan_ganda' ? 'selected' : '' ?>>Pilihan Ganda</option>
                    <option value="isian" <?= old('jenis_jawaban', $pertanyaan['jenis_jawaban']) == 'isian' ? 'selected' : '' ?>>Isian Singkat/Panjang</option>
                </select>
                <?php if ($validation && $validation->hasError('jenis_jawaban')) : ?>
                    <div class="invalid-feedback">
                        <?= $validation->getError('jenis_jawaban') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div id="opsi_jawaban_container" class="mb-3" style="display: none;">
                <label class="form-label">Opsi Jawaban:</label>
                <div id="opsi_list">
                    <?php
                    // Ambil opsi dari database atau dari old input jika ada error validasi
                    $current_opsi_teks = old('opsi_teks', array_column($opsiJawaban, 'opsi_teks'));
                    $current_opsi_nilai = old('opsi_nilai', array_column($opsiJawaban, 'nilai'));

                    if (!empty($current_opsi_teks)) :
                        foreach ($current_opsi_teks as $key => $teks) :
                    ?>
                            <div class="input-group mb-2 opsi-item">
                                <input type="text" name="opsi_teks[]" class="form-control <?= $validation && $validation->hasError('opsi_teks.' . $key) ? 'is-invalid' : '' ?>" placeholder="Teks Opsi" value="<?= esc($teks) ?>" required>
                                <input type="number" name="opsi_nilai[]" class="form-control opsi-nilai-input <?= $validation && $validation->hasError('opsi_nilai.' . $key) ? 'is-invalid' : '' ?>" placeholder="Nilai (1-5)" min="1" max="5" value="<?= esc($current_opsi_nilai[$key] ?? '') ?>" style="display: none;">
                                <button type="button" class="btn btn-danger remove-opsi">Hapus</button>
                                <?php if ($validation && $validation->hasError('opsi_teks.' . $key)) : ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $validation->getError('opsi_teks.' . $key) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($validation && $validation->hasError('opsi_nilai.' . $key)) : ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $validation->getError('opsi_nilai.' . $key) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php
                        endforeach;
                    else :
                        // Tambahkan minimal satu opsi default jika belum ada dan bukan isian
                        if (old('jenis_jawaban', $pertanyaan['jenis_jawaban']) !== 'isian') :
                        ?>
                            <div class="input-group mb-2 opsi-item">
                                <input type="text" name="opsi_teks[]" class="form-control" placeholder="Teks Opsi" required>
                                <input type="number" name="opsi_nilai[]" class="form-control opsi-nilai-input" placeholder="Nilai (1-5)" min="1" max="5" style="display: none;">
                                <button type="button" class="btn btn-danger remove-opsi">Hapus</button>
                            </div>
                    <?php
                        endif;
                    endif;
                    ?>
                </div>
                <button type="button" id="add_opsi" class="btn btn-info btn-sm">Tambah Opsi</button>
            </div>

            <div class="mb-3">
                <label for="urutan" class="form-label">Urutan Pertanyaan:</label>
                <input type="number" name="urutan" id="urutan" class="form-control <?= $validation && $validation->hasError('urutan') ? 'is-invalid' : '' ?>" value="<?= old('urutan', $pertanyaan['urutan']) ?>" min="0" required>
                <?php if ($validation && $validation->hasError('urutan')) : ?>
                    <div class="invalid-feedback">
                        <?= $validation->getError('urutan') ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisJawabanSelect = document.getElementById('jenis_jawaban');
        const opsiJawabanContainer = document.getElementById('opsi_jawaban_container');
        const opsiList = document.getElementById('opsi_list');
        const addOpsiButton = document.getElementById('add_opsi');

        function toggleOpsiInputs() {
            const selectedJenis = jenisJawabanSelect.value;
            if (selectedJenis === 'skala' || selectedJenis === 'pilihan_ganda') {
                opsiJawabanContainer.style.display = 'block';
                // Tampilkan input nilai hanya jika jenisnya 'skala'
                document.querySelectorAll('.opsi-nilai-input').forEach(input => {
                    input.style.display = selectedJenis === 'skala' ? 'block' : 'none';
                    // Tambahkan/hapus atribut 'required' berdasarkan jenis
                    if (selectedJenis === 'skala') {
                        input.setAttribute('required', 'required');
                        input.setAttribute('min', '1');
                        input.setAttribute('max', '5');
                    } else {
                        input.removeAttribute('required');
                        input.removeAttribute('min');
                        input.removeAttribute('max');
                    }
                });
                // Pastikan ada minimal satu opsi teks jika container ditampilkan dan bukan isian
                if (opsiList.children.length === 0 && selectedJenis !== 'isian') {
                    addOpsi(); // Tambahkan opsi default jika kosong dan bukan isian
                }
            } else {
                opsiJawabanContainer.style.display = 'none';
                // Hapus semua opsi saat beralih ke isian
                opsiList.innerHTML = '';
            }
        }

        function addOpsi() {
            const newOpsiItem = document.createElement('div');
            newOpsiItem.classList.add('input-group', 'mb-2', 'opsi-item');
            newOpsiItem.innerHTML = `
                <input type="text" name="opsi_teks[]" class="form-control" placeholder="Teks Opsi" required>
                <input type="number" name="opsi_nilai[]" class="form-control opsi-nilai-input" placeholder="Nilai (1-5)" min="1" max="5">
                <button type="button" class="btn btn-danger remove-opsi">Hapus</button>
            `;
            opsiList.appendChild(newOpsiItem);
            // Panggil lagi toggleOpsiInputs untuk mengupdate tampilan input nilai yang baru ditambahkan
            toggleOpsiInputs();
        }

        // Event listener untuk perubahan jenis jawaban
        jenisJawabanSelect.addEventListener('change', toggleOpsiInputs);

        // Event listener untuk tombol "Tambah Opsi"
        addOpsiButton.addEventListener('click', addOpsi);

        // Event delegation untuk tombol "Hapus" pada opsi
        opsiList.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-opsi')) {
                // Pastikan tidak menghapus semua opsi jika jenisnya pilihan ganda/skala
                const selectedJenis = jenisJawabanSelect.value;
                if ((selectedJenis === 'skala' || selectedJenis === 'pilihan_ganda') && opsiList.children.length === 1) {
                    alert('Tidak bisa menghapus opsi terakhir untuk jenis ' + selectedJenis + '.');
                } else {
                    event.target.closest('.opsi-item').remove();
                }
            }
        });

        // Panggil saat halaman pertama kali dimuat untuk menyesuaikan tampilan berdasarkan old data atau data dari database
        toggleOpsiInputs();
    });
</script>
<?= $this->endSection() ?>