@if($file)
    <a href="{{ route('patent.downloadTemplateDownload', ['documentType' => $documentType]) }}" class="d-block">Template</a>
@else
    <a href="{{ route('patent.documentView', ['patentId' => $patentId, 'documentType' => $documentType]) }}" target="_blank" class="d-block">Lihat Surat Pengalihan Hak</a>
@endif