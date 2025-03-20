<div class="modal fade" id="fixationModal" tabindex="-1" aria-labelledby="fixationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fixationModalLabel">Konfirmasi Fiksasi Makalah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin melakukan fiksasi makalah ini? Setelah difiksasi, perubahan tidak dapat dilakukan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmFixation"
                        data-route="{{ route('paper.fixatePaper', ['id' => '__ID__']) }}">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Fiksasi
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('#fixationModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const paperId = button.data('paper-id');
            const route = $('#confirmFixation').data('route').replace('__ID__', paperId);
            
            $('#confirmFixation').data('paper-id', paperId).data('route', route);
        });

        $('#confirmFixation').on('click', function () {
            const button = $(this);
            const spinner = button.find('.spinner-border');
            const paperId = button.data('paper-id');
            const route = button.data('route');

            // Tampilkan spinner dan disable tombol
            button.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: route,
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Makalah berhasil difiksasi!',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function () {
                    button.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });
    });
</script>
