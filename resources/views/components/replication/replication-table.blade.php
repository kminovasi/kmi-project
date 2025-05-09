<div>
    <table class="table table-bordered">
        <thead class="text-center align-middle">
            <tr style="font-size: .9rem;">
                <th scope="col">No</th>
                <th scope="col">Judul</th>
                <th scope="col">PIC Replikator</th>
                <th scope="col">Perusahaan Replikator</th>
                <th scope="col">Status</th>
                <th scope="col">Berita Acara</th>
                <th scope="col">Eviden</th>
                <th scope="col">Benefit</th>
                <th scope="col">Reward</th>
                @if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'admin')
                <th scope="col">Keterangan</th>
                @endif
            </tr>
        </thead>
        <tbody id="patent-table-container">
          @foreach ($replicationData as $index => $replication)
   
          <tr style="font-size: .8rem;">
            <td class="text-center align-middle">{{ $index + 1 }}</td>
            <td class="align-middle">{{ $replication->paper->innovation_title }}</td>
            <td class="align-middle">{{ $replication->personInCharge->name }}</td>
            <td class="align-middle">{{ $replication->company->company_name }}</td>
            <td class="text-center align-middle">{{ $replication->replication_status }}</td>
            <td class="text-center align-middle">-</td>
            <td class="text-center align-middle">-</td>
            <td class="text-center align-middle">-</td>
            <td class="text-center align-middle">-</td>
            <td class="text-center align-middle">-</td>
          </tr>
          @endforeach
          <tr>
                {{-- <td colspan="10">
                    {{ $patentData->links() }} <!-- Pagination Links -->
                </td> --}}
          </tr>
        </tbody>
    </table>
</div>

<!-- Modal Edit Status -->
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Status Replikasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editStatusForm" method="POST">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <input type="hidden" name="patent_id" id="patent_id">
            <label for="status" class="form-label">Status Pengajuan</label>
            <select class="form-control" id="status" name="status">
              <option value="Belum Diajukan">Belum Diajukan</option>
              <option value="Pengajuan">Pengajuan</option>
              <option value="Dikaji DJKI">Dikaji DJKI</option>
              <option value="Paten">Paten</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="registration_number" class="form-label">Nomor Registrasi</label>
            <input type="text" class="form-control" id="registration_number" name="registration_number" placeholder="Masukkan Registration Number">
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
            <label for="draft" class="form-label">Draft Patent</label>
            <input type="file" class="form-control" id="draft" name="draft" required>
          </div>
          <div class="mb-3">
            <label for="owner_letter" class="form-label">Surat Kepemilikan</label>
            <input type="file" class="form-control" id="owner_letter" name="owner_letter" required>
          </div>
          <div class="mb-3">
            <label for="statement_of_transfer_rights" class="form-label">Surat Pengalihan Hak</label>
            <input type="file" class="form-control" id="statement_of_transfer_rights" name="statement_of_transfer_rights" required>
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
        let registrationNumber = $(this).data('registration-number');
        
        // Ganti action form edit status
        $('#editStatusForm').attr('action', '/patent/update-status/' + patentId);

        // Set selected status
        $('#status').val(status);
        $('#patent_id').val(patentId);
        const registrationNumberField = document.getElementById('registration_number');
        registrationNumberField.value = registrationNumber ? registrationNumber : '';

        if(registrationNumber){
            registrationNumberField.disabled = true;
        } else {
            registrationNumberField.disabled = false;
        }

        // Buka modal
        $('#editStatusModal').modal('show');
    });

    // Klik tombol Upload Dokumen
    $('.upload-doc-btn').click(function() {
        let patentId = $(this).data('patent-id');
        $('#patent_id-doc').val(patentId);

        // Ganti action form upload dokumen
        $('#uploadDocumentForm').attr('action', '/patent/upload-document/' + patentId);

        // Buka modal
        $('#uploadDocumentModal').modal('show');
    });
});
</script>