@extends('layouts.app')
@section('title', 'Benefit Non Finansial | Dashboard')

@section('content')
    @push('css')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <link
            href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
            rel="stylesheet">
    @endpush

    <x-header-content :title="'Benefit Non Finansial | Kategori : ' . $customBenefitPotentialName">
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-blue">Kembali</a>
    </x-header-content>
    <div class="container">
        <div class="card p-3">
            <table id="paperTable" class="display">
                <thead>
                    <tr>
                        <th>Nama Non Finansial Benefit</th>
                        <th>Judul Makalah</th>
                        <th>Deskripsi Benefit Non Finansial</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated via DataTables -->
                </tbody>
            </table>
        </div>
    </div>

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
                                    <h5 class="m-0">Detail Tim</h5>
                                </div>
                                <div class="card-body">
                                    <form id="modal-card-form">
                                        <div class="mb-3">
                                            <label class="form-label" for="facilitator">Fasilitator</label>
                                            <input class="form-control form-control-lg" id="facilitator" type="text"
                                                value="" readonly />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="leader">Ketua</label>
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
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script
            src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#paperTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('dashboard.showAllBenefit', ['customBenefitPotentialId' => $customBenefitPotentialId]) }}',
                    columns: [{
                            data: 'name_benefit',
                            name: 'name_benefit'
                        },
                        {
                            data: 'paper_title',
                            name: 'paper_title'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    dom: 'Bfrtip',
                    buttons: ['csv', 'excel', 'pdf']
                });
            });

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
                        console.log(data);

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

            $('#detailTeamMember').on('hidden.bs.modal', function () {
                remove_detail()
            });
            function remove_detail(){
                document.getElementById('facilitator').value = ''
                document.getElementById('leader').value = ''

                var elemenMember = document.getElementById('member-card')
                if(elemenMember != null){
                    elemenMember.remove()
                }
                var elemenOutsource = document.getElementById('outsource-card');
                if(elemenOutsource != null){
                    elemenOutsource.remove()
                }
            }
        </script>
    @endpush
@endsection
