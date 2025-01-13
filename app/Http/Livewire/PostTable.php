<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Str;

class PostTable extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $posts = [];
    protected $userId;

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

    public function mount()
    {
        $this->userId = auth()->user()->id;
        $this->posts = Post::with('user')->where('user_id', $this->userId)->paginate(10);
    }

    public function render()
    {

        return view('livewire.post-table', [
            'posts' => $this->posts
        ]);
    }
}
