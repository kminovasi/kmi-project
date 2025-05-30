<div>
    <table class="table table-bordered">
        <thead class="text-center align-middle">
            <tr style="font-size: .9rem;">
                <th scope="col">No</th>
                <th scope="col" class="w-25">Judul</th>
                <th scope="col">Replikator</th>
                <th scope="col">Perusahaan</th>
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
                @php
                    $status = $replication->replication_status;
                    $cellBg = match ($status) {
                        'Pengajuan' => 'bg-secondary',
                        'Progres' => 'bg-primary',
                        'Replikasi Berhasil' => 'bg-success',
                        'Replikasi Gagal' => 'bg-danger',
                        default => 'bg-light', // fallback
                    };
                @endphp
              <td class="text-center align-middle {{ $cellBg }}">
                <button class="btn btn-md edit-status-btn text-white"
                        data-replication-id="{{ $replication->id }}"
                        data-replication-status="{{ $replication->replication_status }}">
                    {{ $replication->replication_status }}
                </button>
              </td>

              {{-- News Letter --}}
              <td class="text-center align-middle">
                @if($replication->event_news == null)
                  <button href="" class="btn btn-sm btn-secondary upload-news-btn"
                    data-replication-id="{{ $replication->id }}"><i class="bi bi-upload"></i>
                  </button>
                @else
                  <a href="{{ route('replication.viewDocument', ['replicationId' => $replication->id, 'type' => 'news_letter']) }}" class="text-decoration-none btn-sm btn-primary" target="_blank"><i class="bi bi-file-earmark-fill"></i></a>
                @endif
              </td>

              {{-- Evidence --}}
              <td class="text-center align-middle">
                @if($replication->evidence == null)
                  <button href="" class="btn btn-sm btn-secondary upload-evidence-benefit"
                    data-replication-id="{{ $replication->id }}"><i class="bi bi-upload"></i>
                  </button>
                @else
                  <a href="{{ route('replication.viewDocument', ['replicationId' => $replication->id, 'type' => 'evidence']) }}" class="text-decoration-none btn-sm btn-primary" target="_blank"><i class="bi bi-file-earmark-fill"></i></a>
                @endif
              </td>

              {{-- Benefit --}}
              <td class="text-center align-middle">
                @if($replication->financial_benefit == null)
                <button href="" class="btn btn-sm btn-secondary upload-evidence-benefit"
                  data-replication-id="{{ $replication->id }}"><i class="bi bi-upload"></i>
                </button>
                @else
                Rp{{ number_format($replication->financial_benefit, 0, ',', '.') }}
                @endif
              </td>
              <td class="text-center align-middle">
                @if($replication->reward == null)
                  <button href="" class="btn btn-sm btn-secondary upload-reward-desc"
                    data-replication-id="{{ $replication->id }}"><i class="bi bi-upload"></i>
                  </button>
                @else
                  <a href="{{ route('replication.viewDocument', ['replicationId' => $replication->id, 'type' => 'reward']) }}" class="text-decoration-none btn-sm btn-primary" target="_blank"><i class="bi bi-file-earmark-fill"></i></a>
                @endif
              </td>
              @if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'admin')
                <td class="text-center align-middle">
                    @if($replication->description == null)
                      <button class="btn btn-sm btn-primary upload-reward-desc"
                              data-replication-id="{{ $replication->id }}">
                          <i class="bi bi-upload"></i>
                      </button>
                    @else
                      <button class="btn btn-sm btn-primary btn-desc-view"
                              data-replication-id="{{ $replication->id }}">
                          <i class="bi bi-file-earmark-fill"></i>
                      </button>
                    @endif
                </td>
              @endif
            </tr>
          @endforeach
          <tr>
                <td colspan="10">
                    {{ $replicationData->links() }} <!-- Pagination Links -->
                </td>
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
            <label for="replication-status" class="form-label">Status Pengajuan</label>
            <select class="form-control" id="replication-status" name="replication-status">
              <option value="Pengajuan">Pengajuan</option>
              <option value="Progres">Progres</option>
              <option value="Replikasi Berhasil">Replikasi Berhasil</option>
              <option value="Replikasi Gagal">Replikasi Gagal</option>
            </select>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary">Update Status</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Upload Berita Acara -->
<div class="modal fade" id="uploadNewsLetterModal" tabindex="-1" aria-labelledby="uploadNewsLetterModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Berita Acara</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="uploadNewsLetterForm" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="news-letter" class="form-label">Berita Acara</label>
            <input type="file" class="form-control" id="news-letter" name="news-letter" required>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary">Upload Dokumen</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Upload Benefit dan Evidence --}}
<div class="modal fade" id="uploadBenefitEvidenceModal" tabindex="-1" aria-labelledby="uploadBenefitEvidenceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Benefit dan Evidence</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="uploadBenefitEvidenceForm" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="evidence" class="form-label">Evidence</label>
            <input type="file" class="form-control" id="evidence" name="evidence" required>
          </div>
          <div class="mb-3">
            <label for="benefit" class="form-label">Benefit</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" class="form-control" id="benefit" name="benefit" required>
            </div>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-primary">Upload Dokumen</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Upload Reward dan Description --}}
<div class="modal fade" id="uploadRewardDescModal" tabindex="-1" aria-labelledby="uploadRewardDescModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Benefit dan Evidence</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="uploadRewardDescForm" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="reward" class="form-label text-capitalize">Reward</label>
            <input type="file" class="form-control" id="reward" name="reward" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Keterangan</label>
            <textarea class="form-control" name="description" id="description" cols="20" rows="10"></textarea>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-sm btn-primary">Upload Dokumen</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- View Description --}}
<div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Deskripsi Replikasi Inovasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <textarea class="form-control descriptionValue" name="descriptionValue" id="descriptionValue" cols="20" rows="10" readonly></textarea>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Klik tombol Edit Status
    $('.edit-status-btn').click(function() {
        let replicationId = $(this).data('replication-id');
        let status = $(this).data('replication-status');
        
        // Ganti action form edit status
        $('#editStatusForm').attr('action', '/replication/update-status/' + replicationId);

        // Set selected status
        $('#replication-status').val(status);

        // Buka modal
        $('#editStatusModal').modal('show');
    });

    // Klik tombol Upload Berita Acara
    $('.upload-news-btn').click(function() {
        let replicationId = $(this).data('replication-id');

        // Ganti action form upload dokumen
        $('#uploadNewsLetterForm').attr('action', '/replication/upload-news-letter/' + replicationId);

        // Buka modal
        $('#uploadNewsLetterModal').modal('show');
    });

    // Klik tombol Upload Dokumen
    $('.upload-evidence-benefit').click(function() {
        let replicationId = $(this).data('replication-id');

        // Ganti action form upload dokumen
        $('#uploadBenefitEvidenceForm').attr('action', '/replication/upload-benefit-evidence/' + replicationId);

        // Buka modal
        $('#uploadBenefitEvidenceModal').modal('show');
    });

    // Klik tombol Upload Dokumen
    $('.upload-reward-desc').click(function() {
        let replicationId = $(this).data('replication-id');

        // Ganti action form upload dokumen
        $('#uploadRewardDescForm').attr('action', 'replication/upload-reward-desc/' + replicationId);

        // Buka modal
        $('#uploadRewardDescModal').modal('show');
    });

    // Lihat Deskripsi
    $('.btn-desc-view').on('click', function() {
        const id = $(this).data('replication-id');

        $.get(`/replication/view-document/${id}/description`, function(res) {
            $('#descriptionValue').val(res.description);
            $('#descriptionModal').modal('show');
        });
    });

});
</script>