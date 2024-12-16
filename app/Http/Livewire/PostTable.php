<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Storage;
use Str;

class PostTable extends Component
{
    use WithPagination;
    use WithFileUploads;

    public function deletePost($id)
    {
        $post = Post::find($id);


        // Hapus gambar jika ada
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        session()->flash('success', 'Post berhasil dihapus.');
    }

    public function render()
    {
        $posts = Post::with('user')->paginate(10);

        return view('livewire.post-table', [
            'posts' => $posts,
        ]);
    }
}
