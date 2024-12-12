<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Str;

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
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        // Generate slug dari judul
        $slug = Str::slug($validatedData['title']);

        // Periksa apakah slug sudah ada
        $existingPost = Post::where('slug', $slug)->first();
        if ($existingPost) {
            return back()->withErrors(['title' => 'Judul sudah digunakan. Gunakan judul lain.'])->withInput();
        }

        // Simpan gambar jika ada
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('posts', 'public');
        }

        // Tambahkan slug ke data yang divalidasi
        $validatedData['slug'] = $slug;
        $validatedData['user_id'] = auth()->id();

        // Simpan data ke database
        Post::create($validatedData);

        return redirect()->route('post.index')->with('success', 'Postingan berhasil disimpan!');
    }
}
