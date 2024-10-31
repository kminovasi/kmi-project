<header class="marginForDashboard page-header">
    <div class="container-xl px-4">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="d-flex align-items-center">
                    <div>
                        <h2 class="my-2">
                            @if (Auth::user()->role == 'User')
                                Dashboard Innovator
                            @elseif(Auth::user()->role == 'Admin')
                                Dashboard Pengelola Inovasi
                            @elseif(Auth::user()->role == 'Superadmin')
                                Dashboard Superadmin
                            @elseif(Auth::user()->role == 'BOD')
                                Dashboard BOD
                            @elseif(Auth::user()->role == 'Juri')
                                Dashboard Juri
                            @endif
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-6 justify-content-end">
                @php
                    $formattedDateTime = now()->isoFormat('dddd · D MMMM YYYY') . ' · ' . now()->format('H:i');
                @endphp
                <div class="page-header-subtitle mt-1 d-flex align-items-center">
                    <i class="bi bi-calendar-date me-2"></i>
                    <span>{{ $formattedDateTime }}</span>
                </div>
            </div>
        </div>
    </div>
</header>
