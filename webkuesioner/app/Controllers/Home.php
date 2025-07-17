<?php

namespace App\Controllers; // Pastikan namespace ini benar

class Home extends BaseController // Pastikan nama kelas ini benar
{
    public function index() // Pastikan metode ini ada dan public
    {
        return view('public/landing_page');
    }
}