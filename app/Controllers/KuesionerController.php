<?php
namespace App\Controllers;
class KuesionerController extends BaseController
{
    public function index()
    {
        return view('public/kuesioner_list');
    }

    public function isi($id)
    {
        $data['kuesioner_id'] = $id; // Pass ID untuk form action
        return view('public/kuesioner_form', $data);
    }

    public function submit()
    {
        // Langsung redirect ke halaman terima kasih
        // (HANYA UNTUK DEMO TANPA VALIDASI/FILTER)
        return redirect()->to(base_url('kuesioner/terimakasih'))->with('success', 'Terima kasih telah mengisi kuesioner!');
    }

    public function terimakasih()
    {
        return view('public/kuesioner_terimakasih');
    }
}