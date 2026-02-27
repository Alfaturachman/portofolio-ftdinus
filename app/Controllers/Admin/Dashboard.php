<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
class Dashboard extends Controller
{
    public function index(){
        return view('admin/dashboard');
    }
}