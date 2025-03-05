<div class="modal fade" id="accGM" tabindex="-1" role="dialog" aria-labelledby="accGMTitle" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accGMTitle">Approval Benefit oleh General Manager</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close" onclick="resetModalApproveGM()"></button>
            </div>
            <form id="accGmBenefitForm" method="POST" enctype="multipart/form-data" class="p-2">
                @csrf
                @method('PUT')
                <select class="form-select" aria-label="Default select example" name="status" id="change_benefit_by_gm" required>
                    <option selected>-</option>
                    <option value="accepted benefit by general manager">Accept</option>
                    <option value="revision">Revisi</option>
                    <option value="rejected benefit by general manager">Reject</option>
                </select>

                <div class="modal-body">
                    <div id="revisionTypeContainer" class="mb-3" style="display: none;">
                        <h5>Pilih Tipe Revisi:</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="revision_type[]" value="benefit" id="revision_benefit">
                            <label class="form-check-label" for="revision_benefit">Revisi Benefit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="revision_type[]" value="paper" id="revision_paper">
                            <label class="form-check-label" for="revision_paper">Revisi Paper</label>
                        </div>
                    </div>

                    <div id="stepsContainerGm" class="mb-2"></div>

                    <div class="mb">
                        <label class="mb-1" for="commentGM">Berikan Komentar</label>
                        <textarea name="comment" class="form-control" id="commentGMr" cols="30" rows="3" placeholder="Mohon berikan komentar yang jelas dan terstruktur"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Kirim</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal" onclick="resetModalApproveGM()">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    $('#change_benefit_by_gm').on('change', function() {
        const selectedValue = $(this).val();
        const revisionTypeContainer = $('#revisionTypeContainer');
        const commentField = $('#commentGMr');
        const stepsContainerGm = $('#stepsContainerGm');

        if (selectedValue === 'revision') {
            revisionTypeContainer.show();
            commentField.attr('required', 'required');
        } else {
            revisionTypeContainer.hide();
            commentField.removeAttr('required');
            stepsContainerGm.empty().hide();
        }
    });

    $('#revision_paper').on('change', function() {
        const isChecked = $(this).is(':checked');
        const stepsContainerGm = $('#stepsContainerGm');

        if (isChecked) {
            $.ajax({
                url: `/paper/checkStepNotEmptyOrNullOnPaper/${currentPaperId}`,
                method: 'GET',
                success: function(response) {
                    stepsContainerGm.empty().show();

                    if (response.status === 'success') {
                        stepsContainerGm.append('<h5 class="mb-3">Pilih langkah yang direvisi:</h5>');
                        response.steps.forEach(step => {
                            stepsContainerGm.append(`
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="revision_steps[]" value="${step}" id="step_${step}">
                                    <label class="form-check-label" for="step_${step}">Langkah ${step}</label>
                                </div>
                            `);
                        });
                    } else if (response.status === 'full_paper') {
                        const fullPaperContainer = $(`
                            <div id="stepsContainerGm" class="mb">
                                <h5 class="mb-3">Revisi berdasarkan dokumen Full Paper</h5>
                                <p>Pengumpulan dilakukan menggunakan dokumen full_paper yang telah diunggah.</p>
                                <a href="/storage/${response.full_paper_path}" target="_blank" class="btn btn-primary">Lihat Full Paper</a>
                                <input type="hidden" name="full_paper" value="1">
                            </div>
                        `);

                        stepsContainerGm.append(fullPaperContainer);
                    } else {
                        stepsContainerGm.append(`<p>${response.message}</p>`);
                    }
                },
                error: function() {
                    alert('Gagal memuat data langkah.');
                }
            });
        } else {
            stepsContainerGm.empty().hide();
        }
    });

    function approve_benefit_gm_modal(idPaper) {
        const form = document.getElementById('accGmBenefitForm');
        let url = `{{ route('paper.approveBenefitGM', ['id' => ':idPaper']) }}`;
        url = url.replace(':idPaper', idPaper);
        form.action = url;
        currentPaperId = idPaper;
    }

    const resetModalApproveGM = () => {
        const form = $('#accGmBenefitForm');
        form.removeAttr('action');
        $('#stepsContainerGm').empty().hide();
        $('#change_benefit_by_gm').val('-');
        currentPaperId = null;
    };

    $('#accGM').on('hidden.bs.modal', function() {
        resetModalApproveGM();
    });
</script>