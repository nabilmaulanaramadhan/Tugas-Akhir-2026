<?php

namespace App\Http\Controllers;
class PageController extends Controller
{
    public function dashboard() { return view('pages.dashboard'); }
    public function karyawan() { return view('pages.karyawan'); }
    public function project() { return view('pages.project'); }
    public function task() { return view('pages.task'); }
    public function kelolaPoin() { return view('pages.kelola-poin'); }
    public function kelolaGaji() { return view('pages.kelola-gaji'); }
}

