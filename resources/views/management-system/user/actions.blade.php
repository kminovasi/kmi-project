<div class="btn-group">
    <a href="{{ route('management-system.user.edit', $user->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit"></i>
    </a>
    <button class="btn btn-sm btn-danger delete-user" data-id="{{ $user->id }}">
        <i class="fas fa-trash"></i>
    </button>
    <a href="{{ route('management-system.user.show', $user->id) }}" class="btn btn-sm btn-info">
        <i class="fas fa-eye"></i>
    </a>
</div>
