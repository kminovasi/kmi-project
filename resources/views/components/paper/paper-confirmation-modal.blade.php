<div class="modal fade" id="fixationModal" tabindex="-1" aria-labelledby="fixationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fixationModalLabel">Konfirmasi Fiksasi Makalah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin melakukan fiksasi makalah ini? Setelah difiksasi, perubahan tidak dapat
                dilakukan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmFixation">
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
            $('#confirmFixation').data('paper-id', paperId);
        });

        $('#confirmFixation').on('click', function () {
            const paperId = $(this).data('paper-id');
            const button = $(this);
            const spinner = button.find('.spinner-border');

            // Tampilkan spinner dan ubah tombol menjadi disabled
            button.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: `{{ route('paper.fixatePaper', ['id' => '__ID__']) }}`.replace('__ID__', paperId),
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Makalah berhasil difiksasi!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat memproses fiksasi.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function () {
                    // Sembunyikan spinner dan aktifkan tombol kembali
                    button.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });
    });
</script>
