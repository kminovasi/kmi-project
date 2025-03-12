<div class="p-2">
    <a href="{{ route('paper.register.team') }}"
        class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.register.team') ? 'active-link' : '' }}">Registrasi</a>
    <a href="{{ route('paper.index') }}"
        class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.index') ? 'active-link' : '' }}">Makalah
        Inovasi</a>

    @if (Auth::user()->role == 'Juri' ||
            Auth::user()->role == 'BOD' ||
            Auth::user()->role == 'Admin' ||
            Auth::user()->role == 'Superadmin' ||
            $is_judge)
        <a href="{{ route('assessment.on_desk') }}"
            class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.on_desk') ? 'active-link' : '' }}">Penilaian</a>
    @endif

    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
        <a href="{{ route('paper.event') }}"
            class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.event') ? 'active-link' : '' }}">Event Group</a>
    @endif
</div>