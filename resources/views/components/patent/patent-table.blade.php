<div>
    <table class="table table-bordered">
        <thead class="text-center align-middle">
            <tr style="font-size: .9rem;">
                <th scope="col">No</th>
                <th scope="col" style="width: 12rem">Judul Inovasi</th>
                <th scope="col" style="width: 6rem">Judul Patent</th>
                <th scope="col" style="width: 6.7rem">PIC</th>
                <th scope="col" style="width: 7.5rem">Draft Paten</th>
                <th scope="col">Pernyataan Kepemilikan</th>
                <th scope="col">Surat Pengalihan Hak</th>
                <th scope="col" style="width: 6rem">Status</th>
                <th scope="col">No Registrasi</th>
                <th scope="col">Pemeliharaan Paten</th>
            </tr>
        </thead>
        <tbody id="patent-table-container">
            @isset($patentData)
        @foreach ($patentData as $index => $patent)
            <tr style="font-size: .8rem;">
                <td class="text-center align-middle">{{ $index + 1 }}</td>
                <td class="align-middle">{{ $patent->paper->innovation_title }}</td>
                <td class="align-middle">{{ $patent->patent_title ?? 'Belum Ada Judul' }}</td>
                <td class="align-middle">{{ $patent->employee->name }}</td>
                <td class="text-center align-middle">
                    @include('components.patent.patent-document-link', [
                        'file' => $patent->hasDraft(), 
                        'documentType' => 'draft_paten', 
                        'patentId' => $patent->id])
                </td>
                <td class="text-center align-middle">
                    @include('components.patent.patent-document-link', [
                        'file' => $patent->hasOwnershipLetter(), 
                        'documentType' => 'ownership_letter', 
                        'patentId' => $patent->id])
                </td>
                <td class="text-center align-middle">
                    @include('components.patent.patent-document-link', [
                        'file' => $patent->hasStatementOfTransferRights(), 
                        'documentType' => 'statement_of_transfer_rights', 
                        'patentId' => $patent->id])
                </td>
                @php
                    $statusColors = [
                        'Paten' => 'bg-success',
                        'Ditolak' => 'bg-danger',
                    ];
                    $colorClass = $statusColors[$patent->application_status] ?? 'bg-primary';
                @endphp
                
                <td class="text-center align-middle {{ $colorClass }} text-black" style="--bs-bg-opacity: .5;">
                    {{ $patent->application_status }}
                </td>
                <td class="text-center align-middle">
                    @if($patent->registration_number == null)
                        <p class="text-md fw-500">-</p>
                    @else
                           {{ $patent->registration_number }}
                    @endif
                </td>
                <td class="text-center align-middle">
                    <a href="{{ route('patent.detailInfo', ['patentId' => $patent->id]) }}" class="btn btn-sm btn-primary">Detail</a>
                    @if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'admin')
                    <button class="btn btn-sm btn-warning edit-status-btn" 
                        data-patent-id="{{ $patent->id }}"
                        data-patent-status="{{ $patent->application_status }}"
                        data-registration-number="{{ $patent->registration_number }}"
                        data-patent-title="{{ $patent->patent_title }}">
                        Edit
                    </button>
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="10">
                {{ $patentData->links() }} <!-- Pagination Links -->
            </td>
        </tr>
    @else
        <tr>
            <td colspan="10" class="text-danger text-center">Data paten tidak tersedia</td>
        </tr>
    @endisset
        </tbody>
    </table>
</div>

<!-- Modal Edit Status -->
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Status Paten</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editStatusForm" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="patent_title" class="form-label">Judul Paten</label>
            <input type="text" class="form-control" id="patent_title" name="patent_title" placeholder="Masukkan Judul Paten">
          </div>
          <div class="mb-3">
            <input type="hidden" name="patent_id" id="patent_id">
            <label for="status" class="form-label">Status Pengajuan</label>
            <select class="form-control" id="status" name="status">
                <option value="Permohonan">Permohonan</option>
                <option value="Pemeriksaan Administratif">Pemeriksaan Administratif</option>
                <option value="Pengumuman/Publikasi">Pengumuman/Publikasi</option>
                <option value="Paten">Paten</option>
                <option value="Pemeriksaan Substantif">Pemeriksaan Substantif</option>
                <option value="Ditolak">Ditolak</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="registration_number" class="form-label">Nomor Registrasi</label>
            <input type="text" class="form-control" id="registration_number" name="registration_number" placeholder="Masukkan Nomor Registrasi">
          </div>
          <div class="mb-3">
            <label for="certificate_number" class="form-label">Nomor Sertifikat</label>
            <input type="text" class="form-control" id="certificate_number" name="certificate_number" placeholder="Masukkan Nomor Sertifikat">
          </div>
          <div class="mb-3">
            <label for="upload_file" id="fileLabel" class="form-label"></label>
            <input type="file" class="form-control" id="upload_file" accept=".pdf,application/pdf">
            <small>Upload File .pdf Maks 5MB</small>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary">Update Status</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Upload Dokumen -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Dokumen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="uploadDocumentForm" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <input type="hidden" name="patent_id-doc" id="patent_id-doc">
          <div class="mb-3">
            <label for="input_completeness_documents" id="label_completeness_documents" class="form-label"></label>
            <input type="file" class="form-control" id="input_completeness_documents" required accepted=".pdf">
            <small>Upload File .pdf Maks 5MB</small>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary">Upload Dokumen</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Klik tombol Edit Status
    $('.edit-status-btn').click(function() {
        let patentId = $(this).data('patent-id');
        let status = $(this).data('patent-status');
        let patentTitle = $(this).data('patent-title') ?? "";
        let registrationNumber = $(this).data('registration-number');
        
        // Ganti action form edit status
        $('#editStatusForm').attr('action', '/patent/update-status/' + patentId);

        // Set selected status
        $('#status').val(status);
        $('#patent_id').val(patentId);
        $('#patent_title').val(patentTitle);
        const registrationNumberField = document.getElementById('registration_number');
        registrationNumberField.value = registrationNumber ? registrationNumber : '';

        updateFileInputAttributes();

        // Buka modal
        $('#editStatusModal').modal('show');
    });

    $('.upload-modal-trigger').click(function (e) {
        e.preventDefault(); // cegah redirect

        const patentId = $(this).data('patent-id');
        const documentType = $(this).data('document-type');

        // Atur label berdasarkan tipe dokumen
        const labels = {
            draft_paten: 'Draft Paten',
            ownership_letter: 'Surat Kepemilikan',
            statement_of_transfer_rights: 'Surat Pengalihan Hak',
            // Tambah sesuai kebutuhan
        };

        const label = labels[documentType] || 'Dokumen';

        // Atur form dan input
        $('#uploadDocumentForm').attr('action', `/patent/upload-document/${patentId}/${documentType}`);
        $('#input_completeness_documents').attr('name', documentType);
        $('#label_completeness_documents').text(label);

        // Tampilkan modal
        $('#uploadDocumentModal').modal('show');
    });
    
    $('#status').change(function() {
        updateFileInputAttributes();
    });
    
    $('#status').trigger('change');
});

function updateFileInputAttributes() {
    const statusValue = $('#status').val();
    let label = (statusValue === 'Paten') ? 'Sertifikat Paten' : statusValue;
    let name = 'application_file';
    let id = 'upload_file'; 

    switch (statusValue) {
        case 'Permohonan':
            label = 'Bukti Permohonan';
            name = 'application_file';
            break;
        case 'Pemeriksaan Administratif':
            label = 'Dokumen Pemeriksaan';
            name = 'administrative_file';
            break;
        case 'Pengumuman/Publikasi':
            label = 'File Pengumuman';
            name = 'publication_file';
            break;
        case 'Paten':
            label = 'Sertifikat Paten';
            name = 'certificate';
            break;
        case 'Pengajuan Banding':
            label = 'Dokumen Banding';
            name = 'appeal_file';
            break;
        case 'Ditolak':
            label = 'Dokumen Penolakan';
            name = 'reject_file';
            break;
    }

    // Update label dan atribut input
    $('#fileLabel').text(label);
    $('#upload_file').attr('name', name);
}
</script>