<nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-white"
    id="sidenavAccordion">
    <!-- Sidenav Toggle Button-->
    <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle"><i
            data-feather="menu"></i></button>
    <!-- Navbar Brand-->
    <!-- * * Tip * * You can use text or an image for your navbar brand.-->
    <!-- * * * * * * When using an image, we recommend the SVG format.-->
    <!-- * * * * * * Dimensions: Maximum height: 32px, maximum width: 240px-->
    <a class="navbar-brand pe-3 ps-4 ps-lg-2" href="{{ route('dashboard') }}">SIG Innovation</a>
    <!-- Navbar Search Input-->
    <!-- * * Note: * * Visible only on and above the lg breakpoint-->
    @if (request()->routeIs('profile.index'))
        <form class="form-inline me-auto d-none d-lg-block me-3" action="{{ route('query.search') }}" method="POST">
            @csrf

            {{-- search bar --}}
            <div class="input-group input-group-joined input-group-solid d-flex align-items-center">
                {{-- <select class="form-select" type="text" placeholder="Search" aria-label="Search" name="query" id="search-input"></select> --}}
                <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="Search Here"><i
                    data-feather="search" class="mx-3"></i>
            </div>
        </form>
    @endif
    <!-- Navbar Items-->
    <ul class="navbar-nav align-items-center ms-auto">
        <!-- Documentation Dropdown-->
        {{-- <li class="nav-item dropdown no-caret d-none d-md-block me-3">
                    <a class="nav-link dropdown-toggle" id="navbarDropdownDocs" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="fw-500">Documentation</div>
                        <i class="fas fa-chevron-right dropdown-arrow"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0 me-sm-n15 me-lg-0 o-hidden animated--fade-in-up" aria-labelledby="navbarDropdownDocs">
                        <a class="dropdown-item py-3" href="https://docs.startbootstrap.com/sb-admin-pro" target="_blank">
                            <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="book"></i></div>
                            <div>
                                <div class="small text-gray-500">Documentation</div>
                                Usage instructions and reference
                            </div>
                        </a>
                        <div class="dropdown-divider m-0"></div>
                        <a class="dropdown-item py-3" href="https://docs.startbootstrap.com/sb-admin-pro/components" target="_blank">
                            <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="code"></i></div>
                            <div>
                                <div class="small text-gray-500">Components</div>
                                Code snippets and reference
                            </div>
                        </a>
                        <div class="dropdown-divider m-0"></div>
                        <a class="dropdown-item py-3" href="https://docs.startbootstrap.com/sb-admin-pro/changelog" target="_blank">
                            <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="file-text"></i></div>
                            <div>
                                <div class="small text-gray-500">Changelog</div>
                                Updates and changes
                            </div>
                        </a>
                    </div>
                </li> --}}
        @if (request()->routeIs('profile.index'))
            <!-- Navbar Search Dropdown-->
            <!-- * * Note: * * Visible only below the lg breakpoint-->
            <li class="nav-item dropdown no-caret me-3 d-lg-none">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="searchDropdown" href="#"
                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                        data-feather="search"></i></a>
                <!-- Dropdown - Search-->
                <div class="dropdown-menu dropdown-menu-end p-3 shadow animated--fade-in-up"
                    aria-labelledby="searchDropdown">
                    <form class="form-inline me-auto w-100" action="{{ route('query.search') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-joined input-group-solid">
                            <input class="js-example-placeholder-single js-states form-control" type="text"
                                placeholder="Search" aria-label="Search" name="query" id="search-input"></input>
                            <div class="input-group-text"><i data-feather="search"></i></div>
                        </div>
                    </form>

                </div>
            </li>
        @endif

        <!-- Notifications Dropdown menampilkan daftar notifikasi-->
        @php
            $notifications = auth()->user()->unreadNotifications;
        @endphp
        <li class="nav-item dropdown no-caret d-none d-sm-block me-3 dropdown-notifications">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle relative" id="navbarDropdownAlerts"
                href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i data-feather="bell" id="alert-icon"></i>
                @if ($notifications->count())
                    <span class="badge badge-warning navbar-badge text-warning text-bold absolute z-10">{{ $notifications->count() }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up"
                aria-labelledby="navbarDropdownAlerts">
                <h6 class="dropdown-header dropdown-notifications-header">
                    <i class="me-2" data-feather="bell"></i>
                    Alerts Center
                </h6>

                @foreach($notifications as $notification)
                <div class="d-flex justify-content-around mt-2">
                    <div class="flex flex-column">
                        <a href="{{ $notification->data['url'] }}" class="text-black">
                            {{ $notification->data['message'] }}
                        </a>
                        <br>
                        <span class="float-right text-muted text-sm text-right">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-hover-warning mt-1 text-bold font-weight-bold">x</button>
                    </form>
                </div>
                <div class="dropdown-divider"></div>
                @endforeach
                <a class="dropdown-item dropdown-notifications-footer" href="{{ route('notifications.index') }}">Lihat Semua Notifikasi</a>
            </div>
        </li>

        <!-- Messages Dropdown-->
        <li class="nav-item dropdown no-caret d-none d-sm-block me-3 dropdown-notifications">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownMessages"
                href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false"><i data-feather="mail"></i></a>
            <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up"
                aria-labelledby="navbarDropdownMessages">
                <h6 class="dropdown-header dropdown-notifications-header">
                    <i class="me-2" data-feather="mail"></i>
                    Message Center
                </h6>
            </div>
        </li>
        <!-- User Dropdown-->
        <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage"
                href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false"><img class="img-fluid"
                    src="{{ asset('template/dist/assets/img/illustrations/profiles/profile-1.png') }}" /></a>
            <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up"
                aria-labelledby="navbarDropdownUserImage">
                <h6 class="dropdown-header d-flex align-items-center">
                    <img class="dropdown-user-img"
                        src="{{ asset('template/dist/assets/img/illustrations/profiles/profile-1.png') }}" />
                    <div class="dropdown-user-details">
                        <div class="dropdown-user-details-name">{{ Auth::user()->name }}</div>
                        <div class="dropdown-user-details-email">{{ Auth::user()->email }}</div>
                    </div>
                </h6>
                <div class="dropdown-divider"></div>

                {{-- <a class="dropdown-item" href="">
                            <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                            Logout
                        </a> --}}
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="dropdown-item" type="submit" id="exit-button">
                        <div class="dropdown-item-icon"><i data-feather="log-out"></i></div> Keluar
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<!-- JavaScript to dynamically add alerts effects -->
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        function timeSince(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            let interval = seconds / 31536000;

            if (interval > 1) {
                return Math.floor(interval) + " years ago";
            }
            interval = seconds / 2592000;
            if (interval > 1) {
                return Math.floor(interval) + " months ago";
            }
            interval = seconds / 86400;
            if (interval > 1) {
                return Math.floor(interval) + " days ago";
            }
            interval = seconds / 3600;
            if (interval > 1) {
                return Math.floor(interval) + " hours ago";
            }
            interval = seconds / 60;
            if (interval > 1) {
                return Math.floor(interval) + " minutes ago";
            }
            return "Just now";
        }

        function createAlert(message, timestamp) {
            const newAlert = document.createElement('a');
            newAlert.className = 'dropdown-item dropdown-notifications-item';
            newAlert.href = '#';
            const date = new Date(timestamp);

            newAlert.innerHTML = `
                <div class="dropdown-notifications-item-icon bg-success">
                    <i data-feather="check"></i>
                </div>
                <div class="dropdown-notifications-item-content">
                    <div class="dropdown-notifications-item-content-details">${timeSince(date)}</div>
                    <div class="dropdown-notifications-item-content-text">
                        ${message}
                    </div>
                </div>
            `;

            const alertsContainer = document.getElementById('alertsContainer');
            alertsContainer.insertBefore(newAlert, alertsContainer.firstChild);
            feather.replace();
        }

        function loadAlerts() {
            const alerts = JSON.parse(sessionStorage.getItem('alerts')) || [];
            alerts.forEach(alert => createAlert(alert.message, alert.timestamp));
        }

        function saveAlert(message) {
            const alerts = JSON.parse(sessionStorage.getItem('alerts')) || [];
            const timestamp = new Date().toISOString();

            // Check if the alert is already present
            if (!alerts.some(alert => alert.message === message && alert.timestamp === timestamp)) {
                alerts.push({
                    message,
                    timestamp
                });
                sessionStorage.setItem('alerts', JSON.stringify(alerts));
                createAlert(message, timestamp);
            }
        }

        @if (Session::has('success'))
            const successMessage = "{{ Session::get('success') }}";
            const alerts = JSON.parse(sessionStorage.getItem('alerts')) || [];
            // Ensure the success message is added only once
            if (!alerts.some(alert => alert.message === successMessage)) {
                saveAlert(successMessage);
            }
        @endif

        loadAlerts();

        const dropdown = document.querySelector('.dropdown-notifications');
        dropdown.addEventListener('shown.bs.dropdown', function() {
            const alertIcon = document.getElementById('navbarDropdownAlerts');
            alertIcon.classList.remove('new-alert');
        });
    });

    document.getElementById('exit-button').addEventListener('click', function() {
        sessionStorage.removeItem('alerts');
    });
</script> --}}
