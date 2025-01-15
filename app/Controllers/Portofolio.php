<?php

namespace App\Controllers;

class Portofolio extends BaseController
{
    public function index(): string
    {
        return view('backend/portofolio/view');
    }
}
