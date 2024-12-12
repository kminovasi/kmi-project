<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Str;

class PostTable extends Component
{
    use WithPagination;
    use WithFileUploads;

    public function render()
    {
        $posts = Post::with('user')->paginate(10);

        return view('livewire.post-table', [
            'posts' => $posts,
        ]);
    }
}
