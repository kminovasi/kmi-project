<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\Post;
use Illuminate\Support\Str;
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

    public function edit($id)
    {
        $post = Post::find($id);
        return view('admin.post.post-edit', compact('post'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'cover_image' => 'nullable|image|max:10240',
        ]);

        // Generate slug dari judul
        $slug = Str::slug($validatedData['title']);

        // Periksa apakah slug sudah ada
        $existingPost = Post::where('slug', $slug)->first();
        if ($existingPost) {
            return back()->with('error', 'Judul sudah ada, gunakan yang lain')->withInput();
        }

        // Simpan gambar jika ada
        if ($request->hasFile('cover_image')) {
            $validatedData['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        }

        // Tambahkan slug ke data yang divalidasi
        $validatedData['slug'] = $slug;
        $validatedData['user_id'] = auth()->id();

        // Simpan data ke database
        Post::create($validatedData);

        return redirect()->route('post.index')->with('success', 'Postingan berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        // Ambil post berdasarkan ID
        $post = Post::findOrFail($id);

        // Buat slug dari judul
        $slug = Str::slug($validatedData['title']);

        // Periksa apakah slug sudah digunakan oleh post lain
        if (Post::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            return back()->withErrors(['title' => 'Judul sudah digunakan. Gunakan judul lain.']);
        }

        // Jika ada gambar baru, hapus gambar lama
        if ($request->hasFile('cover_image')) {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            // Simpan gambar baru
            $validatedData['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        } else {
            // Jika tidak ada gambar baru, tetap gunakan gambar lama
            $validatedData['cover_image'] = $post->cover_image;
        }

        // Tambahkan slug ke data yang divalidasi
        $validatedData['slug'] = $slug;

        // Update data di database
        $post->update($validatedData);

        return redirect()->route('post.index')->with('success', 'Post berhasil diperbarui.');
    }

    public function show($slug){
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('homepage.post.show-post', compact('post'));
    }

    public function list(){
        $posts = Post::with('user')->paginate(10);
        return view('homepage.post.list-post', compact('posts'));
    }
}