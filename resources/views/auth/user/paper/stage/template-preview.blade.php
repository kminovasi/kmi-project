<!-- Modal -->
<div class="modal fade" id="templatePreview" tabindex="-1" aria-labelledby="templatePreviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="templatePreviewLabel">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="height: 80vh;">
        <iframe id="pdfFrame" src="" width="100%" height="100%" frameborder="0" oncontextmenu="return false;"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
    function loadPdf(button) {
        var pdfUrl = button.getAttribute('data-pdf-url');
        var pdfFrame = document.getElementById('pdfFrame');
        pdfFrame.src = pdfUrl + "#toolbar=0";
    }

    document.getElementById('templatePreview').addEventListener('hidden.bs.modal', () => {
        document.getElementById('pdfFrame').src = ''; // Clear the iframe source when the modal is closed
    })

    document.getElementById('pdfFrame').oncontextmenu = () => false;
</script>