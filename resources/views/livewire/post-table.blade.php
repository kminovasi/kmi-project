<div>


    <table class="table table-borderless table-striped bg-white rounded text-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Judul</th>
                <th>Author</th>
                <th>Tgl</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $index => $post)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $post->title }}</td>
                    <td>{{ $post->name }}</td>
                    <td></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Aksi
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">View</a></li>
                                <li><a class="dropdown-item" href="#">Edit</a></li>
                                <li>
                                    {{-- <a class="dropdown-item text-danger" href="#"
                                       wire:click.prevent="deletePost({{ $post->id }})">
                                        Hapus
                                    </a> --}}
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($posts->hasPages())
    <div class="mt-4">
        {{ $posts->links() }}
    </div>
@endif
</div>
