<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function admin_class_report(){
        return view('admin.class-report');
    }
}
