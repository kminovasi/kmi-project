<div class="col-lg-3">
    <div class="nav-sticky">
        <div class="card">
            <div class="card-header text-white bg-primary opacity-75">
                SUB MENU
            </div>
            <div class="card-body">
                <ul class="nav flex-column" id="stickyNav">
                    <li class="nav-item {{ Route::is('management-system.team.category.index') ? 'active-link-nav' : '' }}">
                        <a class="nav-link" href="{{ route('management-system.team.category.index') }}">Kategori</a>
                    </li>
                    <li class="nav-item {{ Route::is('management-system.team.theme.index') ? 'active-link-nav' : '' }}">
                        <a class="nav-link" href="{{ route('management-system.team.theme.index') }}">Tema</a>
                    </li>
                    <li class="nav-item {{ Route::is('management-system.team.company.index') ? 'active-link-nav' : '' }}">
                        <a class="nav-link" href="{{ route('management-system.team.company.index') }}">Perusahaan</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
