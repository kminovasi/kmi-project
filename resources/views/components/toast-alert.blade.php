<div class="toast-container position-fixed top-0 end-0 p-3">
    @if ($message)
        <div class="toast text-bg-{{ $type }} border-0" role="alert" aria-live="assertive" aria-atomic="true"
            data-bs-autohide="{{ $type === 'danger' ? 'false' : 'true' }}">
            <div class="toast-header text-bg-{{ $type }}">
                <strong class="me-auto">{{ ucfirst($type) }}</strong>
                <small>Just now</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ $message }}
            </div>
        </div>
    @endif
</div>
