<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CvController extends Controller
{
    function index () {

        $userId = Auth::user();



        return view('auth.admin.dokumentasi.cv.index');
    }
}
