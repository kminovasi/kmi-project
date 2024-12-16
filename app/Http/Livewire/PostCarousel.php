<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;

class PostCarousel extends Component
{
    public $posts;

    public function mount()
    {
        $this->posts = Post::take(6)->get();
    }

    public function render()
    {
        return view('livewire.post-carousel', [
            'posts' => $this->posts,
        ]);
    }
}
