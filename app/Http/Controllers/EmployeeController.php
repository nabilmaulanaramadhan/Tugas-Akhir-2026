<?php

namespace App\Http\Controllers;
class EmployeeController extends Controller {
    public function dashboard() { return view('employee.dashboard'); }
    public function absensi() { return view('employee.absensi'); }
    public function task() { return view('employee.task'); }
    public function poin() { return view('employee.poin'); }
    public function gaji() { return view('employee.gaji'); }
}



