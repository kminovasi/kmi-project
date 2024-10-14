<nav class="nav nav-borders">
    <a class="nav-link ms-0 {{ Route::is('assessment.on_desk') ? 'active' : '' }}" href="{{route('assessment.on_desk')}}">On Desk</a>
    <a class="nav-link {{ Route::is('assessment.presentation') ? 'active' : '' }}" href="{{route('assessment.presentation')}}">Presentasi</a>
    <a class="nav-link {{ Route::is('assessment.caucus.data') ? 'active' : '' }}" href="{{route('assessment.caucus.data')}}">Caucus</a>
    <a class="nav-link {{ Route::is('assessment.presentasiBOD') ? 'active' : '' }}" href="{{route('assessment.presentasiBOD')}}">Presentasi BOD</a>
    <a class="nav-link {{ Route::is('assessment.showDeterminingTheBestOfTheBestTeam') ? 'active' : '' }}" href="{{route('assessment.showDeterminingTheBestOfTheBestTeam')}}">Penetapan Best Of The Best</a>
    <a class="nav-link {{ Route::is('assessment.penetapanJuara') ? 'active' : '' }}" href="{{route('assessment.penetapanJuara')}}">Pengumuman Pemenang</a>
</nav>
<hr class="mt-0 mb-4" />
