 {{-- modal untuk approval admin --}}
 <div class="modal fade" id="accAdmin" tabindex="-1" role="dialog" aria-labelledby="accAdminTitle" aria-hidden="true">
     <div class="modal-dialog modal-md" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="addBenefitTitle">Approval oleh Admin</h5>
                 <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <form id="accAdminForm" method="POST" enctype="multipart/form-data">
                 @csrf
                 @method('PUT')
                 <input type="text" name="evaluatedBy" value="innovation admin" hidden>

                 <div class="modal-body">
                     <div class="mb-3">
                         <label class="mb-1" for="status_by_admin">Status</label>
                         <select class="form-select" aria-label="Default select example" name="status"
                             id="status_by_admin" require>
                             <option selected>-</option>
                             <option value="accept">Accept</option>
                             <option value="revision">Revisi</option>
                             <option value="reject">Reject</option>
                         </select>
                     </div>
                     <div class="mb-3">
                         <div id="registEvent">

                         </div>
                     </div>

                     <div id="revisionTypeContainerAdmin" class="mb-3" style="display: none;">
                         <h5>Pilih Tipe Revisi:</h5>
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" name="revision_type[]" value="benefit"
                                 id="revision_benefit">
                             <label class="form-check-label" for="revision_benefit">Revisi Benefit</label>
                         </div>
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" name="revision_type[]" value="paper"
                                 id="revision_paper_admin">
                             <label class="form-check-label" for="revision_paper">Revisi Paper</label>
                         </div>
                     </div>

                     <div id="stepsContainerAdmin" class="mb-2"></div>
                     <!-- <input type="text" name="status" value="accept" hidden> -->

                     <div class="mb">
                         <label class="mb-1" for="commentFacilitator">Komentar</label>
                         <textarea name="comment" class="form-control" id="commentFacilitator" cols="30" rows="3"  placeholder="Mohon berikan komentar yang jelas dan terstruktur" required></textarea>
                     </div>
                 </div>

                 <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" data-bs-dismiss="modal" id="accAdminButton">
                        Kirim</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
                 </div>
             </form>
         </div>
     </div>
 </div>

 @push('js')
     <script>
         $('#status_by_admin').on('change', async function() {
             const selectedValueAdmin = $(this).val();
             const revisionTypeContainerAdmin = $('#revisionTypeContainerAdmin');
             const commentFieldAdmin = $('#commentAdmin');
             const stepsContainerAdmin = $('#stepsContainerAdmin');

             if (selectedValueAdmin === 'revision') {
                 revisionTypeContainerAdmin.show();
                 commentFieldAdmin.attr('required', 'required');
             } else if (selectedValueAdmin === 'accept') {
                 revisionTypeContainerAdmin.hide();
                 await check_admin_approve(currentTeamId);
             } else {
                 revisionTypeContainerAdmin.hide();
                 commentFieldAdmin.removeAttr('required');
                 stepsContainerAdmin.empty().hide();
             }
         });

         $('#accAdmin').on('hidden.bs.modal', function() {
             var form = document.getElementById('accAdminForm');

             form.removeAttribute('action');

             document.getElementById('registEvent').innerHTML = ""
             document.getElementById('status_by_admin').value = '-'
         });

         $('#revision_paper_admin').on('change', function() {
             const isChecked_admin = $(this).is(':checked');
             const stepsContainerAdmin = $('#stepsContainerAdmin');

             if (isChecked_admin) {
                 // Panggil API untuk mendapatkan langkah-langkah
                 $.ajax({
                     url: `/paper/checkStepNotEmptyOrNullOnPaper/${currentPaperId}`,
                     method: 'GET',
                     success: function(response) {
                         stepsContainerAdmin.empty().show();

                         if (response.status === 'success') {
                             stepsContainerAdmin.append(
                                 '<h5 class="mb-3">Pilih langkah yang direvisi:</h5>');
                             response.steps.forEach(step => {
                                 stepsContainerAdmin.append(`
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="revision_steps[]" value="${step}" id="step_${step}">
                                    <label class="form-check-label" for="step_${step}">Langkah ${step}</label>
                                </div>
                            `);
                             });
                         } else if (response.status === 'full_paper') {
                             const fullPaperContainer = $(`
                        <div id="stepsContainer" class="mb">
                            <h5 class="mb-3">Revisi berdasarkan dokumen Full Paper</h5>
                            <p>Pengumpulan dilakukan menggunakan dokumen full_paper yang telah diunggah.</p>
                            <a href="/storage/${response.full_paper_path}" target="_blank" class="btn btn-primary">Lihat Full Paper</a>
                            <input type="hidden" name="full_paper" value="1">
                        </div>
                    `);

                             stepsContainerAdmin.append(fullPaperContainer);
                         } else {
                             stepsContainerAdmin.append(`<p>${response.message}</p>`);
                         }
                     },
                     error: function() {
                         alert('Gagal memuat data langkah.');
                     }
                 });
             } else {
                 stepsContainerAdmin.empty().hide();
             }
         });


         async function check_admin_approve(idTeam) {
             await check_if_accept(idTeam);
         }

         function approve_admin_modal(idPaper, idTeam) {
             // alert(idPapern)
             var form = document.getElementById('accAdminForm');
             statusSelectField = document.getElementById('status_by_admin')

             var url = `{{ route('paper.approveadmin', ['id' => ':idPaper']) }}`;
             url = url.replace(':idPaper', idPaper);
             form.action = url;

             // check_admin_approve()
             statusSelectField.setAttribute('onchange', `check_admin_approve(${idTeam})`)
             currentPaperId = idPaper;
             currentTeamId = idTeam;
         }

         function check_if_accept(idTeam) {
             return new Promise((resolve, reject) => {
                 const registEventDiv = $('#registEvent'); // Gunakan jQuery untuk memilih elemen
                 const statusSelectField = $('#status_by_admin'); // Gunakan jQuery untuk memilih elemen

                 if (statusSelectField.val() === 'accept') {
                     // Ambil data tim menggunakan fungsi AJAX yang sudah ada
                     const data_team = get_single_data_from_ajax('teams', {
                         id: idTeam
                     });

                     if (!data_team || !data_team.company_code) {
                         registEventDiv.html(`
                    <div class="mb-3">
                        <p>Data tim tidak valid atau tidak terhubung ke perusahaan.</p>
                    </div>
                `);
                         reject('Data tim tidak valid atau tidak terhubung ke perusahaan.');
                         return;
                     }

                     // Bangun URL secara dinamis dengan company_code dari data tim
                     const url = `/user/events/${data_team.company_code}/${idTeam}`;

                     $.ajax({
                         headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                 'content') // Tambahkan CSRF token jika diperlukan
                         },
                         type: 'GET',
                         url: url,
                         dataType: 'json',
                         success: function(data) {
                             if (data.success && data.events.length > 0) {
                                 const options = data.events.map(event =>
                                     `<option value="${event.id}">${event.event_name} - ${event.year}</option>`
                                 ).join('');

                                 // Tambahkan elemen dropdown baru ke dalam DOM
                                 const newInput = `
                            <div class="mb-3">
                                <label class="mb-1" for="id_eventID">Event - Year</label>
                                <select class="form-select" aria-label="Default select example"
                                    name="event_id" id="id_eventID" required>
                                    <option value="">Pilih Event</option>
                                    ${options}
                                </select>
                            </div>
                        `;

                                 registEventDiv.html(newInput); // Ganti konten div dengan elemen baru
                                 resolve(data.events); // Kembalikan data events
                             } else {
                                 registEventDiv.html(`
                            <div class="mb-3">
                                <p>Tidak ada event yang tersedia untuk perusahaan Anda.</p>
                            </div>
                        `);
                                 resolve([]); // Kembalikan array kosong jika tidak ada event
                             }
                         },
                         error: function(xhr, status, error) {
                             console.error('Error fetching events:', xhr.responseText);
                             registEventDiv.html(`
                        <div class="mb-3">
                            <p>Terjadi kesalahan saat memuat event.</p>
                        </div>
                    `);
                             reject('Terjadi kesalahan saat memuat event.');
                         }
                     });
                 } else {
                     registEventDiv.html(""); // Kosongkan konten jika status bukan "accept"
                     resolve(null); // Kembalikan null jika status bukan "accept"
                 }
             });
         }
     </script>
 @endpush
