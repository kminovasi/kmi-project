 <div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalTitle"
     aria-hidden="true">
     <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content border-0 shadow">
             <div class="modal-header " style="background-color: #eb4a3a">
                 <h5 class="modal-title text-white fw-bold" id="commentTitle">Komentar</h5>
                 <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                     aria-label="Close"></button>
             </div>

             <div class="modal-body">
                 <div class="mb-3">
                     <label class="fw-bold mb-2" for="commentList">Semua Komentar</label>
                     <div id="commentList" class="bg-light p-3 rounded border overflow-auto" style="max-height: 400px;">
                         <!-- List komentar akan dimasukkan di sini melalui JavaScript -->
                     </div>
                 </div>
             </div>

             <div class="modal-footer d-flex justify-content-end">
                 <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                     Close
                 </button>
             </div>
         </div>
     </div>
 </div>

 @push('js')
     <script>
         $('#commentModal').on('hidden.bs.modal', function() {
             document.getElementById('commentList').innerHTML = "";
             document.getElementById('commentTitle').innerHTML = "";
         });


         $('#commentModal').on('hidden.bs.modal', function() {
             document.getElementById('comment').value = "";
             document.getElementById('commentTitle').innerHTML = "";
         });


         $('#commentModal').on('hidden.bs.modal', function() {
             document.getElementById('comment').value = ""
             document.getElementById('commentTitle').innerHTML = ""
         });


         function get_comment(idPaper, writer) {
             $.ajax({
                 url: '/api/comments/by-paper',
                 type: 'GET',
                 data: {
                     paper_id: idPaper
                 },
                 success: function(data) {
                     let commentList = document.getElementById('commentList');
                     let commentTitle = document.getElementById('commentTitle');

                     // Kosongkan konten sebelumnya
                     commentList.innerHTML = "";

                     if (data.length > 0) {
                         // Buat elemen untuk setiap komentar
                         data.forEach(comment => {
                             let commentItem = document.createElement('div');
                             commentItem.className = 'mb-3 p-2 rounded border bg-white';
                             commentItem.innerHTML = `
                        <strong>${comment.writer}</strong>
                        <p class="mb-0 text-muted">${comment.comment}</p>
                    `;
                             commentList.appendChild(commentItem);
                         });
                     } else {
                         // Tampilkan pesan jika tidak ada komentar
                         commentList.innerHTML = `
                    <div class="text-muted text-center">
                        <em>Tidak ada komentar.</em>
                    </div>
                `;
                     }

                     commentTitle.innerHTML = `List Komentar`;
                 },
                 error: function(xhr) {
                     console.error('Failed to fetch comments:', xhr);
                     document.getElementById('commentList').innerHTML = `
                <div class="text-danger text-center">
                    <em>Gagal Memuat Komentar.</em>
                </div>
            `;
                 }
             });
         }
     </script>
 @endpush
