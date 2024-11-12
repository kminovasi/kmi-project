<div>
    <!-- Form Pencarian dan Filter -->
    <div class="flex row mb-4">
        <!-- Pencarian berdasarkan judul paper -->
        <div class="col-md-6">
            <input type="text" name="search" wire:model.debounce.300ms="search" class="form-control form-control-sm" placeholder="Cari judul paper..."
                value="{{ request('search') }}">
        </div>

        <!-- Filter berdasarkan company code  -->
        <div class="col-md-2">
            @livewire('company-select', ['selectedCompany' => $company])
        </div>

        <!-- Filter berdasarkan Event -->
        <div class="col-md-2">
            @livewire('event-select' , ['selectedEvent' => $event])
        </div>

        <!-- Filter berdasarkan tema -->
        <div class="col-md-2">
            @livewire('theme-select', ['selectedTheme' => $theme])
        </div>
    </div>

    <div class="table-responsive min-vh-100">
        {{-- table --}}
        <table class="table table-borderless table-hover text-sm rounded bg-white">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Team</th>
                    <th scope="col">Judul</th>
                    <th scope="col">Tema</th>
                    <th scope="col">Event</th>
                    <th scope="col">Real Financial</th>
                    <th scope="col">Benefit Potensial</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @if ($papers->count() > 0)
                    @foreach ($papers as $index => $paper)
                        <tr>
                            <td>{{ ($currentPage - 1) * $perPage + $index + 1 }}</td>
                            <td>{{ $paper->team_name }}</td>
                            <td>{{ $paper->innovation_title }}</td>
                            <td>{{ $paper->theme_name }}</td>
                            <td>{{ $paper->event_name }} {{ $paper->year }}</td>
                            <td>Rp.{{ number_format($paper->financial, 0, ',', '.') }}</td>
                            <td>>Rp.{{ number_format($paper->potential_benefit, 0, ',', '.') }}</td>
                            <td>
                                @if ($paper->is_best_of_the_best == false)
                                    {{ $paper->status }}
                                @else
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                        data-bs-title="Best of The Best">
                                        <i class="fas fa-trophy" aria-hidden="true"></i>
                                    </button>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i data-feather="more-horizontal"></i>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('evidence.detail', $paper->team_id) }}"
                                                class="dropdown-item">
                                                <i class="fas fa-info-circle dropdown-item-icon"></i> Detail
                                            </a>
                                        </li>
                                        <hr class="dropdown-divider">
                                        <li>
                                            {{-- <a href="{{ asset('storage/' . str_replace('f: ', '', $paper->full_paper)) }}"
                                                class="dropdown-item" download="{{ $paper->innovation_title }}.pdf">
                                                <i class="fas fa-download dropdown-item-icon"></i>  Download Paper
                                            </a> --}}
                                            <a href="{{ route('evidence.download-paper', $paper->id) }}"
                                                class="dropdown-item">
                                                <i class="fas fa-download dropdown-item-icon"></i>  Download Paper
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="9">Data tidak ditemukan</td>
                    </tr>
                @endif

            </tbody>
        </table>

        {{-- pagginate --}}
        <div class="mt-4">
            {{ $papers->links() }}
        </div>
    </div>

</div>
