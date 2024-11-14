<div>
    <div class="dropdown-center">
        <button class="btn btn-icon btn-sm btn-transparent-dark dropdown-toggle relative" type="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-regular fa-bell"></i>
            <span class="badge badge-warning navbar-badge text-warning text-bold absolute"
                @if ($notifications->isEmpty()) style="display: none;" @endif>
                {{ $notifications->count() }}
            </span>
        </button>

        <ul class="dropdown-menu shadow animated--fade-in-up py-2 px-2">
            <div>
                <h6 class="dropdown-header text-primary">Notifikasi</h6>
            </div>
            <div class="dropdown-divider"></div>
            @if ($notifications && $notifications->isNotEmpty())
                @foreach ($notifications as $notification)
                    <div class="dropdown-item d-flex justify-content-between align-items-center">
                        <div>
                            <small>{{ $notification->data['message'] }}</small>
                            <br>
                            <span class="text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>

                        <button wire:click="destroy({{ json_encode($notification->id) }})"
                            class="btn btn-transparent btn-sm" wire:loading.attr="disabled">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                @endforeach

                <div class="dropdown-divider"></div>
                <li>
                    <a class="dropdown-item text-center text-muted" href="#" wire:click="destroyAll"
                        wire:loading.attr="disabled">
                        <span class="fw-bolder text-danger"><small>Hapus Semua</small></span>
                    </a>
                </li>
            @else
                <div class="dropdown-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fs-6">Kosong</h6>
                    </div>
                </div>
            @endif
        </ul>
    </div>

</div>
