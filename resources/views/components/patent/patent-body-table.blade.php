
        @foreach ($patentData as $index => $patent)
            <tr style="font-size: .8rem;">
                <td class="text-center align-middle">{{ $index + 1 }}</td>
                <td class="align-middle">{{ $patent->paper->innovation_title }}</td>
                <td class="align-middle">{{ $patent->employee->name }}</td>
                <td class="text-center align-middle">
                    @include('components.patent.patent-document-link', [
                        'file' => $patent->hasDraft(), 
                        'documentType' => 'draft_patent', 
                        'patentId' => $patent->id])
                </td>
                <td class="text-center align-middle">
                    @include('components.patent.patent-document-link', [
                        'file' => $patent->hasOwnershipLetter(), 
                        'documentType' => 'statement_of_ownership', 
                        'patentId' => $patent->id])
                </td>
                <td class="text-center align-middle">
                    @include('components.patent.patent-document-link', [
                        'file' => $patent->hasStatementOfTransferRights(), 
                        'documentType' => 'transfer_letter', 
                        'patentId' => $patent->id])
                </td>
                <td class="text-center align-middle">{{ $patent->application_status }}</td>
                <td class="text-center align-middle">
                    @if($patent->registration_number == null)
                        <p class="text-md fw-500">-</p>
                    @else
                        <a href="https://www.dgip.go.id/" target="_blank">{{ $patent->registration_number }}</a>
                    @endif
                </td>
                <td class="text-center align-middle">
                    <a href="{{ route('patent.detailInfo', ['patentId' => $patent->id]) }}" class="btn btn-sm btn-primary">Detail</a>
                    @if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'admin')
                    <button class="btn btn-sm btn-warning edit-status-btn" 
                        data-patent-id="{{ $patent->id }}"
                        data-patent-status="{{ $patent->application_status }}"
                        data-registration-number="{{ $patent->registration_number }}">
                        Edit
                    </button>
                    @elseif(Auth::user()->id == $patent->person_in_charge)
                    <button class="btn btn-sm btn-danger mt-2 upload-doc-btn" 
                        data-patent-id="{{ $patent->id }}">
                        Upload Dokumen
                    </button>
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="10">
                {{ $patentData->links() }} <!-- Pagination Links -->
            </td>
        </tr>