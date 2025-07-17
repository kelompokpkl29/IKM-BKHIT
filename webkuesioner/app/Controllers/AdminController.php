<?php
namespace App\Controllers;
class AdminController extends BaseController
{
    // Semua fungsi di sini hanya akan memuat view yang relevan untuk demo tampilan
    // (HANYA UNTUK DEMO TANPA VALIDASI/FILTER)
    public function dashboard() { return view('admin/dashboard'); }
    public function kuesioner() { return view('admin/kuesioner/index'); }
    public function createKuesioner() { return view('admin/kuesioner/create'); }
    public function storeKuesioner() {
        return redirect()->to(base_url('admin/kuesioner'))->with('success', 'Kuesioner berhasil ditambahkan!');
    }
    public function editKuesioner($id) {
        $data['kuesioner_id'] = $id;
        return view('admin/kuesioner/edit', $data);
    }
    public function updateKuesioner($id) {
        return redirect()->to(base_url('admin/kuesioner'))->with('success', 'Kuesioner berhasil diperbarui!');
    }
    public function deleteKuesioner($id) {
        return redirect()->to(base_url('admin/kuesioner'))->with('success', 'Kuesioner berhasil dihapus!');
    }
    public function pertanyaan($kuesionerId) {
        $data['kuesioner_id'] = $kuesionerId;
        return view('admin/pertanyaan/index', $data);
    }
    public function createPertanyaan($kuesionerId) {
        $data['kuesioner_id'] = $kuesionerId;
        return view('admin/pertanyaan/create', $data);
    }
    public function storePertanyaan() {
        return redirect()->to(base_url('admin/pertanyaan/1'))->with('success', 'Pertanyaan berhasil ditambahkan!');
    }
    public function editPertanyaan($id) {
        $data['pertanyaan_id'] = $id;
        return view('admin/pertanyaan/edit', $data);
    }
    public function updatePertanyaan($id) {
        return redirect()->to(base_url('admin/pertanyaan/1'))->with('success', 'Pertanyaan berhasil diperbarui!');
    }
    public function deletePertanyaan($id) {
        return redirect()->to(base_url('admin/pertanyaan/1'))->with('success', 'Pertanyaan berhasil dihapus!');
    }
    public function profile() { return view('admin/profile/index'); }
    public function updateProfile() {
        return redirect()->to(base_url('admin/profile'))->with('success', 'Profil berhasil diperbarui!');
    }
    public function hasil() { return view('admin/hasil/index'); }
}