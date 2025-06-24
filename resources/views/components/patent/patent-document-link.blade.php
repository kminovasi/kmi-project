<div class="">
    @if($file)
        <a href="{{ route('patent.downloadTemplateDownload', ['documentType' => $documentType]) }}" class="fs-1"><i class="bi bi-box-arrow-in-down"></i></a>
    @else
        <a href="{{ route('patent.documentView', ['patentId' => $patentId, 'documentType' => $documentType]) }}" target="_blank" class="fs-1"><i class="bi bi-eye-fill"></i></a>
    @endif
    <a href="#" id="upload"
       class="fs-1 upload-modal-trigger"
       data-patent-id="{{ $patentId }}"
       data-document-type="{{ $documentType }}">
       <i class="bi bi-file-earmark-arrow-up-fill"></i>
    </a>
</div>