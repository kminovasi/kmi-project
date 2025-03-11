 <div class="modal fade" id="accFasilitatorBnefit" tabindex="-1" role="dialog" aria-labelledby="accFasilitatorBnefit"
     aria-hidden="true">
     <div class="modal-dialog modal-md" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="accFasilitatorBnefitTitle">Approval Benefit oleh Fasilitator</h5>
                 <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <form id="accFasilBenefitForm" method="POST" enctype="multipart/form-data">
                 @csrf
                 @method('PUT')
                 <div class="modal-body">
                     <div class="mb-2">
                         <select class="form-select" aria-label="Default select example" name="status"
                             id="change_benefit_by_fasil" require>
                             <option selected>-</option>
                             <option value="accepted benefit by facilitator">Accept</option>
                             <option value="revision benefit by facilitator">Revisi</option>
                             <option value="rejected benefit by facilitator">Reject</option>
                         </select>
                     </div>
                     <div class="mb-2">
                         <label class="mb-1" for="commentFacilitator">Berikan Komentar</label>
                         <textarea name="comment" class="form-control" id="commentFacilitator" cols="30" rows="3"
                             placeholder="Mohon berikan komentar yang jelas dan terstruktur"></textarea>
                     </div>
                 </div>

                 <div class="modal-footer">
                     <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
                     <button class="btn btn-primary" type="submit" data-bs-dismiss="modal">Submit</button>
                 </div>
             </form>
         </div>
     </div>
 </div>


 @push('js')
     <script>
         function approve_benefit_fasil_modal(idPaper) {
             // alert(idPapern)
             var form = document.getElementById('accFasilBenefitForm');

             var url = `{{ route('paper.approveBenefitFasil', ['id' => ':idPaper']) }}`;
             url = url.replace(':idPaper', idPaper);
             form.action = url;
         }
         $('#accFasilitatorBnefit').on('hidden.bs.modal', function() {
             var form = document.getElementById('accFasilBenefitForm');

             form.removeAttribute('action');
         });
     </script>
 @endpush
