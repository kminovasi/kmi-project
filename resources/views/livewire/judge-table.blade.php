<div>
    @if (Auth::user()->role == 'Superadmin')
        <div class="mb-4 p-0">
            <div class="flex row">
                <div class="col-md-6">
                    <input type="text" wire:model.debounce.300ms="search" name="search" id="search"
                        class="form-control form-control-sm" placeholder="Cari nama juri...">
                </div>

                <!-- Filter berdasarkan company code  -->
                <div class="col-md-3 mb-1">
                    @livewire('company-select', ['selectedCompany' => $company])
                </div>

                <!-- Filter berdasarkan Event -->
                <div class="col-md-3 mb-1">
                    @livewire('event-select', ['selectedEvent' => $event])
                </div>
            </div>
        </div>
    @endif

    <div class="table-responsive min-vh-100">
        <table class="table table-borderless table-hover text-sm rounded bg-white">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Perusahaan</th>
                    <th>Event</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

                @if ($judges->count() > 0)
                    @foreach ($judges as $index => $j)
                        <tr>
                            <td>{{ ($currentPage - 1) * $perPage + $index + 1 }}</td>
                            <td>{{ $j->name }}</td>
                            <td>{{ $j->company_name }}</td>
                            <td>{{ $j->event->event_name }} {{ $j->event->year }}</td>
                            <td>
                                @if ($j->status == 'active')
                                    <span class="badge bg-success">{{ $j->status }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $j->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i data-feather="more-horizontal"></i>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" type="button"
                                                href="{{ route('management-system.juri-edit', [$j->id, $j->name]) }}">
                                                <i class="fas fa-edit dropdown-item-icon"></i> Edit</a>
                                        </li>
                                        <li>
                                            {{-- <form action="{{ route('management-system.juri-updateStatus', $j->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button class="dropdown-item" type="submit">
                                                    <i class="fas fa-refresh dropdown-item-icon"></i>Update
                                                    Status</button>
                                            </form> --}}
                                            <a class="dropdown-item" wire:click="updateStatus({{ $j->id }})">
                                                <i class="fas fa-refresh dropdown-item-icon"></i>Update Status</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ asset('storage/surat-juri/' . $j->letter_path) }}"
                                                target="_blank">
                                                <i class="fas fa-file-text dropdown-item-icon"></i>Lihat Surat</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $loop->iteration }}">
                                                <i class="fas fa-trash dropdown-item-icon"></i> Hapus
                                            </a>
                                        </li>
                                    </ul>

                                </div>

                                {{-- Modal Delete --}}
                                <div class="modal fade" id="deleteModal{{ $loop->iteration }}" tabindex="-1"
                                    aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content ">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="deleteModalLabel">Konfirmasi Hapus
                                                    Data
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah yakin data ini akan dihapus ?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <form action="{{ route('management-system.juri-delete', $j->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Ya,
                                                        Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data</td>
                    </tr>
                @endif

            </tbody>
        </table>

        @if ($judges->hasPages())
            <div class="mt-4">
                {{ $judges->links() }}
            </div>
        @endif
    </div>
</div>
