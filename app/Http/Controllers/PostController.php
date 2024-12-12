<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return view('admin.post.post-index');
    }

    public function create()
    {
        return view('admin.post.post-create');
    }

    public function store(Request $request)
    {
        //
    }
}
