<div>


    <table class="table table-borderless table-striped bg-white rounded text-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Judul</th>
                <th>Author</th>
                <th>Tanggal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $index => $post)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $post->title }}</td>
                <td>{{ $post->user->username }}</td>
                <td>{{ $post->created_at }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('post.show', $post->slug) }}">View</a></li>
                            <li><a class="dropdown-item" href="{{ route('post.edit', $post->id) }}">Edit</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $post->id }}">
                                    Hapus
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>

            <div class="modal fade" id="deleteModal-{{ $post->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $post->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel-{{ $post->id }}">Konfirmasi Penghapusan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus post <strong>{{ $post->title }}</strong>?
                            Tindakan ini tidak dapat dibatalkan.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-danger" wire:click="deletePost({{ $post->id }})" data-bs-dismiss="modal">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>


            @endforeach
        </tbody>
    </table>
    @if (is_object($posts) && method_exists($posts, 'hasPages') && $posts->hasPages())
    <div class="mt-4">
        {{ $posts->links() }}
    </div>
    @endif
</div>

