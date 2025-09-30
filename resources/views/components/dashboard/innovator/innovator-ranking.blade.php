<div class="card">
    <div class="card-header" style="background-color: #eb4a3a">
        <h5 class="text-white">Inovator Dengan Kontribusi Inovasi Terbanyak Setiap Tahun</h5>
    </div>
    <div class="card-body">
        <div class="accordion" id="accordionInnovator">
            @foreach ($innovatorData as $year => $innovators)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $year }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $year }}" aria-expanded="false" aria-controls="collapse{{ $year }}">
                            Tahun {{ $year }}
                        </button>
                    </h2>
                    <div id="collapse{{ $year }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $year }}"
                        data-bs-parent="#accordionInnovator">
                        <div class="accordion-body p-0">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th>Ranking</th>
                                        <th>Nama Inovator</th>
                                        <th>Jumlah Tim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($innovators as $data)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ $data['name'] }}</td>
                                            <td class="text-center">{{ $data['total'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
