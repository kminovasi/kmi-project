<div class="d-flex flex-column align-items-start">
    @if ($paperId != null)
        <a href="{{ route('assessment.watermarks', ['paperId' => $paperId != null]) }}" class="btn btn-sm text-white" style="background-color: #e84637" target="_blank">
            Lihat Makalah
        </a>

        @if ($fullPaperUpdatedAt)
            <small class="text-muted mt-2">
                <small class="text-muted mt-2">
                    Makalah Terakhir diubah pada:
                    {{ \Carbon\Carbon::parse($fullPaperUpdatedAt)->translatedFormat('d F Y H:i') }}
                </small>

            </small>
        @else
            <small class="text-muted mt-2">
                Terakhir diubah pada: Tidak tersedia
            </small>
        @endif
    @else
        <p class="text-muted">File paper belum tersedia.</p>
    @endif
</div>
