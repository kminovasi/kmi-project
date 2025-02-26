@extends('layouts.app')
@section('title', 'Benefit')
@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Atur Benefit
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div id="alertContainer"></div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row gx-1 mb-3" id="benefitFinancial">
                    <div class="row">
                        <label class="mb-1" for="dataFinancial">Benefit Finansial Riil</label>
                        <input class="form-control" id="dataFinancial" style="max-height: 2.5em" type="text"
                            name="financial" value="" placeholder="Nominal Benefit Finansial Riil" disabled>
                    </div>
                </div>
                <div class="row gx-1 mb-3" id="benefitFinancial">
                    <div class="row">
                        <label class="mb-1" for="dataBenefitPtential">Potensi Benefit Finansial</label>
                        <input class="form-control" style="max-height: 2.5em" id="dataBenefitPotential" type="text"
                            name="potential_benefit" placeholder="Nominal Potensi Benefit Finansial" value=""
                            disabled>
                    </div>
                </div>
                <div id="listBenefitNonFin">

                </div>
                <button type="button" class="btn btn-primary" id="addBenefitNonFin"
                    onclick="add_custom_benefit()"><i class="fa-solid fa-plus btn-sm"></i>&nbsp;Tambahkan Benefit Non Financial</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Penghapusan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Apakah Anda yakin ingin menghapus data ini?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
        </div>
      </div>
    </div>
  </div>


@endsection

@push('js')
    <script>
        let count = 0
        let idEvent;
        let deleteId;
        let deleteOrder; // Menyimpan order yang akan dihapus
        let deleteEventId; // Menyimpan event ID

        function get_single_data_from_ajax(table, data_where) {
            let result_data
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                async: false,
                type: 'GET',
                url: '{{ route('query.custom') }}',
                dataType: 'json',
                data: {
                    table: `${table}`,
                    where: data_where,
                    limit: 1
                },
                success: function(response) {
                    // console.log(response[0]);
                    result_data = response[0]
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    result_data = []
                }
            })
            return result_data
        }


        function get_data_benefit(idEvent) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: "{{ route('benefit.getAllCustomBenefitFinancial') }}",
                dataType: 'json',
                data: {
                    table: "custom_benefit_financials",
                    limit: 100
                },
                success: function(data) {
                    // Menampilkan data yang diterima dari server

                    // document.getElementById('dataFinancial').value = data.data_benefit_financial.financial;
                    // document.getElementById('dataBenefitPotential').value = data.data_benefit_financial.potential_benefit;

                    Object.keys(data).forEach(function(index) {
                        count++;
                        id_benefit = data[index].id
                        name_benefit = data[index].name_benefit
                        // name_value_nonfin = data.data_benefit_nonfinancial[index].value ? data.data_benefit_nonfinancial[index].value : "-"

                        const newNonFin = `
                        <div class="row gx-3 mb-3" id="nonfin-${count}" data-id="${id_benefit}">
                            <div class="col-md-5">
                                <input class="form-control" id="inputCustomBenefit-${count}" type="text" placeholder="Masukkan Nama Benefit"  value="${name_benefit}" readonly/>
                            </div>
                            <div class="col-md-5">
                                <input class="form-control" id="inputValue-${count}" type="text" value="" readonly />
                            </div>
                            <div class="col-md-2" id="button_place-${count}">
                                <button type="button" class="btn btn-warning" onclick="edit_inputfield(${count}, \'${idEvent}\')">Edit</button>
                                <button type="button" class="btn btn-danger" onclick="delete_data(${count}, \'${idEvent}\')">Hapus</button>
                            </div>
                        </div>
                        `;

                        document.getElementById('listBenefitNonFin').innerHTML += newNonFin;

                    });

                },
                error: function(error) {
                    console.error(error.responseJSON)
                }
            });

        }

        function add_custom_benefit() {
            count++;
            const newInputNonFin = `
                <div class="row gx-3 mb-3" id="nonfin-${count}">
                    <div class="col-md-5">
                        <input class="form-control" id="inputCustomBenefit-${count}" type="text" placeholder="Masukkan Nama Benefit"  />
                    </div>

                    <div class="col-md-2" id="button_place-${count}">
                        <button type="button" class="btn btn-primary" onclick="save_data(${count}, \'${idEvent}\')">Simpan</button>
                        <button type="button" class="btn btn-danger" onclick="delete_inputfield(${count})">Batal</button>
                    </div>
                </div>
                `;
            document.getElementById('listBenefitNonFin').insertAdjacentHTML('beforeend', newInputNonFin);
        }

        // menjalnkan fungsi ketika modal ditutup
        $('#addBenefit').on('hidden.bs.modal', function() {
            document.getElementById('listBenefitNonFin').innerHTML = "";
            count = 0;
            document.getElementById("addBenefitNonFin").removeAttribute("onclick");
        });

        function save_data(order, idEvent) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('query.add_benefit') }}",
                type: "POST",
                data: {
                    name_benefit: document.getElementById('inputCustomBenefit-' + order).value,
                    company_code: idEvent
                },
                success: function(data) {

                    button_nonfin = document.getElementById("button_place-" + order)
                    button_nonfin.innerHTML = ""

                    const newBtnNonFin = `
                    <div class="col-md-2" id="button_place-${order}">
                        <button type="button" class="btn btn-warning" onclick="edit_inputfield(${order}, \'${idEvent}\')">Edit</button>
                        <button type="button" class="btn btn-danger" onclick="delete_data(${order}, \'${idEvent}\')">Hapus</button>
                    </div>
                    `;
                    button_nonfin.insertAdjacentHTML('beforeend', newBtnNonFin);

                    document.getElementById("inputCustomBenefit-" + order).setAttribute('readonly', true);

                    document.getElementById("nonfin-" + order).setAttribute('data-id', data.data.id);

                    setTimeout(function() {
                        alert("berhasil");
                    }, 100);
                },
                error: function(error) {
                    console.error(error.responseJSON)
                }
            });
        }

        function edit_inputfield(order, idEvent) {
            input_value_benefit_nonfin = document.getElementById("inputCustomBenefit-" + order)
            old_value_benefit_nonfin = input_value_benefit_nonfin.value

            button_nonfin = document.getElementById("button_place-" + order)
            button_nonfin.innerHTML = ""

            const newBtnNonFin = `
            <div class="col-md-2" id="button_place-${order}">
                <button type="button" class="btn btn-primary" onclick="update_data(${order}, \'${idEvent}\')">Simpan Perubahan</button>
                <button type="button" class="btn btn-danger" onclick="cancel_edit_inputfield(${order}, \'${idEvent}\' , \'${old_value_benefit_nonfin}\')">Batal Perubahan</button>
            </div>
            `;
            button_nonfin.insertAdjacentHTML('beforeend', newBtnNonFin);


            input_value_benefit_nonfin.removeAttribute('readonly');
        }

        function cancel_edit_inputfield(order, idEvent, oldValue) {
            button_nonfin = document.getElementById("button_place-" + order)
            button_nonfin.innerHTML = ""

            const newBtnNonFin = `
            <div class="col-md-2" id="button_place-${order}">
                <button type="button" class="btn btn-warning" onclick="edit_inputfield(${order}, \'${idEvent}\')">Edit</button>
                <button type="button" class="btn btn-danger" onclick="delete_data(${order}, \'${idEvent}\')">Hapus</button>
            </div>
            `;
            button_nonfin.insertAdjacentHTML('beforeend', newBtnNonFin);

            input_value_benefit_nonfin = document.getElementById("inputCustomBenefit-" + order);
            input_value_benefit_nonfin.value = oldValue;
            input_value_benefit_nonfin.setAttribute('readonly', true);
        }

        function update_data(order, idEvent) {
            // alert(document.getElementById('inputCustomBenefit-' + order).value + " " + order)
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('query.update_benefit') }}",
                type: "PUT",
                data: {
                    id: document.getElementById('nonfin-' + order).getAttribute('data-id'),
                    name_benefit: document.getElementById('inputCustomBenefit-' + order).value,
                },
                success: function(data) {
                    button_nonfin = document.getElementById("button_place-" + order)
                    button_nonfin.innerHTML = ""

                    const newBtnNonFin = `
                    <div class="col-md-2" id="button_place-${order}">
                        <button type="button" class="btn btn-warning" onclick="edit_inputfield(${order}, \'${idEvent}\')">Edit</button>
                        <button type="button" class="btn btn-danger" onclick="delete_data(${order}, \'${idEvent}\')">Hapus</button>
                    </div>
                    `;
                    button_nonfin.insertAdjacentHTML('beforeend', newBtnNonFin);

                    document.getElementById("inputCustomBenefit-" + order).setAttribute('readonly', true);

                    setTimeout(function() {
                        alert("berhasil");
                    }, 100);

                },
                error: function(error) {
                    alert(error.responseJSON.error);
                }
            });
        }

        function delete_inputfield(order) {
            nonfin = document.getElementById("nonfin-" + order)
            // nonfin.innerHTML = "";
            nonfin.remove();
            count--;
        }


function delete_data(order, idEvent) {
    // Menyimpan id benefit yang akan dihapus dan order yang akan dihapus
    deleteOrder = order;
    deleteEventId = idEvent;
    deleteId = document.getElementById('nonfin-' + order).getAttribute('data-id');

    // Tampilkan modal konfirmasi
    $('#confirmDeleteModal').modal('show');
}

// Menangani klik pada tombol konfirmasi di dalam modal
$('#confirmDeleteButton').on('click', function() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('query.delete_benefit') }}",
        type: "DELETE",
        data: {
            id: deleteId,
        },
        success: function(data) {
            // Menutup modal konfirmasi
            $('#confirmDeleteModal').modal('hide');

            // Menghapus input field yang terkait dengan order yang dihapus
            delete_inputfield(deleteOrder);

            // Menampilkan alert berhasil
            showAlert('success', 'Data berhasil dihapus.');
        },
        error: function(error) {
            // Menutup modal konfirmasi
            $('#confirmDeleteModal').modal('hide');

            // Menampilkan error alert
            showAlert('danger', error.responseJSON.error);
        }
    });
});

function delete_inputfield(order) {
    nonfin = document.getElementById("nonfin-" + order);
    nonfin.remove(); // Menghapus elemen berdasarkan order
    count--; // Mengurangi jumlah benefit jika perlu
}


// Fungsi untuk menampilkan alert menggunakan Bootstrap
function showAlert(type, message) {
    let alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    // Menampilkan alert di dalam container
    document.getElementById('alertContainer').innerHTML = alertHTML;

    // Menghilangkan alert setelah beberapa detik
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000); // 5 detik
}


        $(document).ready(function() {
            idEvent = "{{ Auth::user()->company_code }}"

            get_data_benefit(idEvent)
        });
    </script>
@endpush
