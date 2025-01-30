<div class="modal fade" id="detailTeamMember" tabindex="-1" role="dialog" aria-labelledby="detailTeamMemberTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <h5 class="modal-title" id="detailTeamMemberTitle">Detail Team Member</h5> --}}
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Detail Team</h5>
                            </div>
                            <div class="card-body">
                                <form id="modal-card-form">
                                    <div class="mb-3">
                                        <label class="form-label" for="facilitator">Fasilitator</label>
                                        <input class="form-control form-control-lg" id="facilitator" type="text"
                                            value="" readonly />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="leader">Leader</label>
                                        <input class="form-control form-control-lg" id="leader" type="text"
                                            value="" readonly />
                                    </div>
                                </form>
                            </div>
                        </div>


                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Foto Tim</h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="" id="idFotoTim" alt="Foto Tim"
                                    class="img-fluid rounded-3 shadow-sm" />
                            </div>
                        </div>

                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Foto Inovasi Produk</h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="" id="idFotoInovasi" alt="Foto Inovasi Produk"
                                    class="img-fluid rounded-3 shadow-sm" />
                            </div>
                        </div>

                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Detail Makalah</h5>
                            </div>
                            <div class="card-body">
                                <!-- Judul -->
                                <div class="mb-3">
                                    <div class="fw-bold">Judul</div>
                                    <div class="small mb-0" id="judul"></div>
                                </div>
                                <hr>
                                <!-- Lokasi Implementasi Inovasi -->
                                <div class="mb-3">
                                    <div class="fw-bold">Lokasi Implementasi Inovasi</div>
                                    <div class="small mb-0" id="inovasi_lokasi"></div>
                                </div>
                                <hr>
                                <!-- Abstrak -->
                                <div class="mb-3">
                                    <div class="fw-bold">Abstrak</div>
                                    <div class="small mb-0" id="abstrak"></div>
                                </div>
                                <hr>
                                <!-- Permasalahan -->
                                <div class="mb-3">
                                    <div class="fw-bold">Permasalahan</div>
                                    <div class="small mb-0" id="problem"></div>
                                </div>
                                <hr>
                                <!-- Penyebab Utama -->
                                <div class="mb-3">
                                    <div class="fw-bold">Penyebab Utama</div>
                                    <div class="small mb-0" id="main_cause"></div>
                                </div>
                                <hr>
                                <!-- Solusi -->
                                <div class="mb-3">
                                    <div class="fw-bold">Solusi</div>
                                    <div class="small mb-0" id="solution"></div>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        function get_data_on_modal(IdTeam) {
            var fotoTim;
            var fotoInovasi;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('query.get_data_member') }}",
                type: "POST",
                data: {
                    team_id: IdTeam
                },
                success: function(data) {

                    if (typeof data.data.member !== 'undefined') {
                        new_div_member = `
                <div class="mb-3" id="member-card">
                    <label class="mb-1" for="dataName">Team Member</label>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" id="List">
                        </ul>
                    </div>
                </div>`;

                        document.getElementById('modal-card-form').insertAdjacentHTML('beforeend',
                            new_div_member);
                        var ul = document.getElementById('List')
                    }

                    if (typeof data.data.outsource !== 'undefined') {
                        new_div_outsource = `
                <div class="mb-3" id="outsource-card">
                    <label class="mb-1" for="dataName">Team Member Outsource</label>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" id="outsource-List">

                        </ul>
                    </div>
                </div>
                `;
                        document.getElementById('modal-card-form').insertAdjacentHTML('beforeend',
                            new_div_outsource);
                        var ul_outsource = document.getElementById('outsource-List')
                    }

                    Object.keys(data.data).forEach(function(indeks) {
                        if (indeks == 'member') {

                            Object.keys(data.data[indeks]).forEach(function(indeks2) {
                                var elemenLi = document.createElement("li");

                                elemenLi.className = "list-group-item";
                                elemenLi.id = indeks + (parseInt(indeks2) + 1);

                                var elemenA = document.createElement("a");
                                elemenA.textContent = data.data[indeks][indeks2].name;

                                elemenLi.appendChild(elemenA)
                                ul.appendChild(elemenLi)
                            })
                        } else if (indeks == 'outsource') {

                            Object.keys(data.data[indeks]).forEach(function(indeks2) {
                                var elemenLi = document.createElement("li");

                                elemenLi.className = "list-group-item";
                                elemenLi.id = indeks + (parseInt(indeks2) + 1);

                                var elemenA = document.createElement("a");
                                elemenA.textContent = data.data[indeks][indeks2].name;

                                elemenLi.appendChild(elemenA)
                                ul_outsource.appendChild(elemenLi)
                            })
                        } else {
                            if (document.getElementById(indeks) !== null) {

                                document.getElementById(indeks).value = data.data[indeks].name
                            }

                        }
                    });
                    var judulElement = document.getElementById('judul');
                    judulElement.textContent = data.paper[0].innovation_title;

                    var lokasiElement = document.getElementById('inovasi_lokasi');
                    lokasiElement.textContent = data.paper[0].inovasi_lokasi;
                    //document.getElementById('inovasi_lokasi').innerHTML = data.paper[0].inovasi_lokasi;
                    // var lokasiElement = document.getElementById('inovasi_lokasi');
                    //     if (data.papers && data.paper[0] && data.paper[0].inovasi_lokasi) {
                    //         lokasiElement.textContent = data.paper[0].inovasi_lokasi;
                    //     } else {
                    //         lokasiElement.textContent = "Data tidak tersedia";
                    //     }


                    var abstractElement = document.getElementById('abstrak');
                    abstractElement.textContent = data.paper[0].abstract;

                    var problemElement = document.getElementById('problem');
                    problemElement.textContent = data.paper[0].problem;

                    // var problem_impactElement = document.getElementById('problem_impact');
                    // problem_impactElement.textContent = data.paper[0].problem_impact;

                    var main_causeElement = document.getElementById('main_cause');
                    main_causeElement.textContent = data.paper[0].main_cause;

                    var solutionElement = document.getElementById('solution');
                    solutionElement.textContent = data.paper[0].solution;

                    // var outcomeElement = document.getElementById('outcome');
                    // outcomeElement.textContent = data.paper[0].outcome;

                    // var performanceElement = document.getElementById('performance');
                    // performanceElement.textContent = data.paper[0].performance;

                    fotoTim = '{{ route('query.getFile') }}' + '?directory=' + encodeURIComponent(data.paper[0]
                        .proof_idea);
                    fotoInovasi = '{{ route('query.getFile') }}' + '?directory=' + encodeURIComponent(data
                        .paper[0].innovation_photo);

                    // Set the URL as the source for the iframe
                    document.getElementById("idFotoTim").src = fotoTim;
                    document.getElementById("idFotoInovasi").src = fotoInovasi;
                },
                error: function(error) {
                    // Menampilkan pesan kesalahan jika terjadi kesalahan dalam permintaan Ajax
                    console.log(error.responseJSON);
                    alert(error.responseJSON.message);
                }
            });

        }
    </script>
@endpush
