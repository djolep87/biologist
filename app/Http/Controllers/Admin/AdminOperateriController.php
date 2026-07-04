<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminOperateriController extends Controller
{
    public function index(): View
    {
        return view('admin.operateri.index');
    }
}
