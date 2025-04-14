<style>
    .sidenav {
        background-color: #8E1616;
    }
</style>

<div id="layoutSidenav_nav">
    <nav class="sidenav shadow-right sidenav-light opacity-75 sidebar-rounded">
        <div class="sidenav-menu">
            <div class="nav accordion" id="accordionSidenav">
                <!-- Sidenav Menu Heading (Account)-->
                <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                <div class="sidenav-menu-heading d-sm-none">Akun</div>
                <!-- Sidenav Link (Alerts)-->
                <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                <a class="nav-link d-sm-none" href="#!">
                    <div class="nav-link-icon"><i data-feather="bell"></i></div>
                    Notifikasi
                    <span class="badge bg-warning-soft text-warning ms-auto">4 Baru!</span>
                </a>
                <!-- Sidenav Link (Messages)-->
                <!-- * * Note: * * Visible only on and above the sm breakpoint-->
                <a class="nav-link d-sm-none" href="#!">
                    <div class="nav-link-icon"><i data-feather="mail"></i></div>
                    Messages
                    <span class="badge bg-success-soft text-success ms-auto">2 New!</span>
                </a>
                <!-- Sidenav Menu Heading (Core)-->
                <div class="sidenav-menu-heading text-white">Kegiatan</div>
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}" aria-expanded="false">
                        <div class="nav-link-icon"><i data-feather="sliders"></i></div>
                        Dashboard
                    </a>
                <!-- Sidenav Accordion (Dashboard)-->
                <a class="nav-link {{ request()->routeIs('paper.index') ? 'active' : '' }}"
                    href="{{ route('paper.index') }}">
                    <div class="nav-link-icon"><i data-feather="clipboard"></i></div>
                    Makalah Inovasi
                </a>
                <a class="nav-link {{ request()->routeIs('event-team.index') ? 'active' : '' }}"
                    href="{{ route('event-team.index') }}">
                    <div class="nav-link-icon"><i class="fa-solid fa-calendar-days me-1"></i></div>
                    Event
                </a>
                <a class="nav-link {{ request()->routeIs('dokumentasi.index') ? 'active' : '' }}"
                    href="{{ route('dokumentasi.index') }}">
                    <div class="nav-link-icon"><i data-feather="archive"></i></div>
                    Dokumentasi
                </a>

                <a class="nav-link" href="#">
                    <div class="nav-link-icon"><i data-feather="award"></i></div>
                    Paten
                </a>
                <a class="nav-link {{ request()->routeIs('profile.index') ? 'active' : '' }}"
                    href="{{ route('profile.index') }}">
                    <div class="nav-link-icon"><i data-feather="user-check"></i></div>
                    Profil
                </a>
                @if (Auth::user()->role == 'BOD')
                    {{-- Menu untuk BOD --}}
                    <div class="sidenav-menu-heading text-white">BOD</div>
                @elseif (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
                    {{-- Menu untuk Admin --}}
                    <div class="sidenav-menu-heading text-white">Pengelola Inovasi</div>
                    <a class="nav-link {{ request()->routeIs('benefit.index') ? 'active' : '' }}"
                        href="{{ route('benefit.index') }}">
                        <div class="nav-link-icon"><i data-feather="dollar-sign"></i></div>
                        Benefit
                    </a>

                    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                        data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                        <div class="nav-link-icon"><i data-feather="sliders"></i></div>
                        Manajemen Sistem
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapsePages" data-bs-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesMenu">
                            <a class="nav-link {{ request()->routeIs('management-system.assign.event') ? 'active' : '' }}"
                                href="{{ route('management-system.assign.event') }}">Event</a>
                            @if (Auth::user()->role == 'Admin')
                                <a class="nav-link {{ request()->routeIs('assessment.show.template') ? 'active' : '' }}"
                                    href="{{ route('assessment.show.template') }}">Penilaian</a>
                            @elseif(Auth::user()->role == 'Superadmin')
                                <a class="nav-link {{ request()->routeIs('assessment.show.template') ? 'active' : '' }}"
                                    href="{{ route('assessment.show.template') }}">Penilaian</a>
                            @endif
                            <a class="nav-link {{ request()->routeIs('management-system.role.index') ? 'active' : '' }}"
                                href="{{ route('management-system.role.index') }}">Atur Role</a>
                            <a class="nav-link {{ request()->routeIs('management-system.team.category.index') ? 'active' : '' }}"
                                href="{{ route('management-system.team.category.index') }}">Kategori Role</a>
                            @if (Auth::user()->role == 'Superadmin')
                                <a class="nav-link {{ request()->routeIs('management-system.metodologi_papers.index') ? 'active' : '' }}"
                                    href="{{ route('management-system.metodologi_papers.index') }}">
                                    Metodologi Makalah</a>
                            @endif
                            @if (Auth::user()->role == 'Superadmin')
                                <a class="nav-link {{ request()->routeIs('management-system.assessment-matrix.index') ? 'active' : '' }}"
                                    href="{{ route('management-system.assessment-matrix.index') }}">Matriks
                                    Penilaian</a>
                            @endif
                            <a class="nav-link {{ request()->routeIs('management-system.user.index') ? 'active' : '' }}"
                                href="{{ route('management-system.user.index') }}">Pengguna</a>
                            <!-- Nested Sidenav Accordion (Pages -> Account)-->
                        </nav>
                    </div>
                    <div class="sidenav-menu-heading text-white">Pengelolaan Event</div>
                    <a class="nav-link {{ request()->routeIs('certificates.index') ? 'active' : '' }}"
                        href="{{ route('certificates.index') }}">
                        <div class="nav-link-icon"><i data-feather="award"></i></div>
                        <span class="text-white">Sertifikat</span>
                    </a>
                    {{-- <a class="nav-link {{ request()->routeIs('flyer.index') ? 'active' : '' }}"
                        href="{{ route('flyer.index') }}">
                        <div class="nav-link-icon"><i data-feather="airplay"></i></div>
                        <span class="text-white">Flyer</span>
                    </a> --}}
                    <a class="nav-link {{ request()->routeIs('timeline.index') ? 'active' : '' }}"
                        href="{{ route('timeline.index') }}">
                        <div class="nav-link-icon"><i data-feather="clock"></i></div>
                        <span class="text-white">Timeline</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('post.index') ? 'active' : '' }}"
                        href="{{ route('post.index') }}">
                        <div class="nav-link-icon"><i data-feather="file-text"></i></div>
                        <span class="text-white">Postingan</span>
                    </a>
                @endif
            </div>
        </div>
        <!-- Sidenav Footer-->
        <div class="sidenav-footer">
            <div class="sidenav-footer-content">
                <div class="sidenav-footer-subtitle text-white">Masuk Sebagai:</div>
                <div class="sidenav-footer-title text-white">{{ Auth::user()->name }}</div>
            </div>
        </div>
    </nav>
</div>
