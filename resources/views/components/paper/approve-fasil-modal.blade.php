<div class="modal fade" id="accFasilitator" tabindex="-1" role="dialog" aria-labelledby="accFasilitatorTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accFasilitatorTitle">Approval Makalah oleh Fasilitator</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"
                    onclick="resetModalApproveFasil()"></button>
            </div>
            <form id="accFasilPaperForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="mb-2">
                        <select class="form-select" aria-label="Default select example" name="status"
                            id="status_by_fasil" require>
                            <option selected>-</option>
                            <option value="accepted paper by facilitator">accept</option>
                            <option value="revision paper by facilitator">revisi</option>
                            <option value="rejected paper by facilitator">reject</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <div id="stepsContainer"></div>
                    </div>
                    <div class="mb">
                        <label class="mb-1" for="commentFacilitator">Berikan Komentar</label>
                        <textarea name="comment" class="form-control" placeholder="Mohon berikan komentar yang jelas dan terstruktur"
                            id="commentFacilitator" cols="30" rows="5"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Kirim</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal"
                        onclick="resetModalApproveFasil()">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script>
        let currentPaperId = null; // Deklarasikan currentPaperId di luar fungsi

        $('#status_by_fasil').on('change', function() {
            const selectedValue = $(this).val();
            const commentField = $('#commentFacilitator');
            const modalBody = $('.modal-body');

            if (selectedValue === 'revision paper by facilitator') {
                commentField.attr('required', 'required'); // Wajibkan komentar

                if (!currentPaperId) {
                    alert('Paper ID tidak ditemukan. Pastikan Anda telah memilih paper.');
                    return;
                }

                $.ajax({
                    url: `/paper/checkStepNotEmptyOrNullOnPaper/${currentPaperId}`,
                    method: 'GET',
                    success: function(response) {
                        $('#stepsContainer').remove();

                        if (response.status === 'success') {
                            const stepsContainer = $('<div id="stepsContainer" class="mb"></div>');
                            stepsContainer.append('<h5 class="mb-3">Pilih langkah yang direvisi</h5>');

                            response.steps.forEach(step => {
                                stepsContainer.append(`
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="revision_steps[]" value="${step}" id="step_${step}">
                                <label class="form-check-label" for="step_${step}">Langkah ${step}</label>
                            </div>
                        `);
                            });

                            modalBody.append(stepsContainer);
                        } else if (response.status === 'full_paper') {
                            const fullPaperContainer = $(`
                        <div id="stepsContainer" class="mb">
                            <h5 class="mb-3">Revisi berdasarkan dokumen Full Paper</h5>
                            <p>Pengumpulan dilakukan menggunakan dokumen full_paper yang telah diunggah.</p>
                            <a href="/storage/${response.full_paper_path}" target="_blank" class="btn btn-primary">Lihat Full Paper</a>
                            <input type="hidden" name="full_paper" value="1">
                        </div>
                    `);

                            modalBody.append(fullPaperContainer);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Gagal memuat data step.');
                    }
                });
            } else {
                commentField.removeAttr('required');
                $('#stepsContainer').remove();
            }
        });


        function approve_paper_fasil_modal(idPaper) {
            // alert(idPapern)
            const form = document.getElementById('accFasilPaperForm');
            
            let url = `{{ route('paper.approvePaperFasil', ['id' => ':idPaper']) }}`;
            url = url.replace(':idPaper', idPaper);
            form.action = url;
            currentPaperId = idPaper;
        }

        const resetModalApproveFasil = () => {
            const form = $('#accFasilPaperForm');
            form.removeAttr('action'); // Hapus atribut action dari form
            $('#stepsContainer').remove(); // Hapus stepsContainer dalam modal
            $('#status_by_fasil').val('-'); // Set kembali nilai default dropdown
            currentPaperId = null; // Reset currentPaperId
        };
    </script>