<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminGio1Controller extends Controller
{
    public function index(): View
    {
        return view('admin.gio1.index');
    }
}
