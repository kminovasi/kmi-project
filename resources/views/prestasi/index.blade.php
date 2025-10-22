@extends('layouts.app')
@section('title','Prestasi Karyawan')

@if(!empty($onlyTable))
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Nama</th>
                    <th class="text-center">Jumlah Inovasi</th>
                    <th class="text-center">Paten</th>
                    <th class="text-center">Replikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                    <tr>
                        <td>{{ ($data->currentPage()-1)*$data->perPage() + $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold">{{ $row->name }}</div>
                            <div class="text-muted small">{{ $row->employee_id }}</div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('prestasi.list', [$row->employee_id, 'inovasi']) }}" class="btn btn-link p-0">
                                {{ (int) $row->inovasi_total }}
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('prestasi.list', [$row->employee_id, 'paten']) }}" class="btn btn-link p-0">
                                {{ (int) $row->paten_total }}
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('prestasi.list', [$row->employee_id, 'replikasi']) }}" class="btn btn-link p-0">
                                {{ (int) $row->replikasi_total }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($data->hasPages())
        <div class="mt-2 d-flex justify-content-end">
            {{ $data->appends(request()->query())->links() }}
        </div>
    @endif

    @php return; @endphp
@endif

@section('content')
<div class="container-xl px-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Prestasi {{ strtoupper($userLogin->unit_name ?? '-') }}</h4>
        <form id="form-prestasi" method="GET" class="row g-2" onsubmit="return false;">
            <div class="col-auto">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search here...">
            </div>
            <div class="col-auto">
                <select name="per_page" class="form-select">
                    @foreach([10,25,50,100,250] as $n)
                        <option value="{{ $n }}" {{ (int)request('per_page',25)===$n?'selected':'' }}>{{ $n }}/page</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <a href="{{ route('prestasi.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="result">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Nama</th>
                                <th class="text-center">Jumlah Inovasi</th>
                                <th class="text-center">Paten</th>
                                <th class="text-center">Replikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td>{{ ($data->currentPage()-1)*$data->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-normal">{{ $row->name }}</div>
                                        <div class="text-muted small">{{ $row->employee_id }}</div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('prestasi.list', [$row->employee_id, 'inovasi']) }}" class="btn btn-link p-0">
                                            {{ (int) $row->inovasi_total }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('prestasi.list', [$row->employee_id, 'paten']) }}" class="btn btn-link p-0">
                                            {{ (int) $row->paten_total }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('prestasi.list', [$row->employee_id, 'replikasi']) }}" class="btn btn-link p-0">
                                            {{ (int) $row->replikasi_total }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($data->hasPages())
                    <div class="mt-2 d-flex justify-content-end">
                        {{ $data->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function(){
    const form   = document.getElementById('form-prestasi');
    const input  = form.querySelector('input[name="q"]');
    const select = form.querySelector('select[name="per_page"]');
    const result = document.getElementById('result');

    let typingTimer;
    const DEBOUNCE_MS = 300;

    function buildURL(page=null){
        const params = new URLSearchParams(new FormData(form));
        if (!page) params.set('page','1'); 
        const url = new URL("{{ route('prestasi.index') }}", window.location.origin);
        url.search = params.toString();
        return url.toString();
    }

    async function fetchTable(url){
        try{
            const resp = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
            const html = await resp.text();
            result.innerHTML = html;                  
            window.history.replaceState({}, '', url); 
        }catch(e){ console.error(e); }
    }

    function debounced(){
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => fetchTable(buildURL()), DEBOUNCE_MS);
    }
    input.addEventListener('input', debounced);
    select.addEventListener('change', () => fetchTable(buildURL()));
    result.addEventListener('click', function(e){
        const a = e.target.closest('a.page-link');
        if (!a) return;
        e.preventDefault();
        const url = new URL(a.getAttribute('href'), window.location.origin);
        const params = new URLSearchParams(new FormData(form));
        url.searchParams.set('q', params.get('q') || '');
        url.searchParams.set('per_page', params.get('per_page') || '25');
        fetchTable(url.toString());
    });

    form.addEventListener('keydown', function(e){
        if (e.key === 'Enter' && e.target.tagName.toLowerCase() === 'input') {
            e.preventDefault();
        }
    });
})();
</script>
@endpush
