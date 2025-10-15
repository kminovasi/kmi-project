<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Paper;
use App\Models\Patent;
use Illuminate\Http\Request;
use App\Models\PatentMaintenance;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\TemplateDocumentPatent;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Exceptions\Exception;

class PatentController extends Controller
{
    
    public function index() {
        return view('auth.admin.paten.index');
    }

   public function detailInfo($patentId)
    {
        $patent = Patent::findOrFail($patentId);
        $paidPatent = $patent->patenMaintenance()->where('status', 'paid')->get();
        
        $patentMaintenance = $patent->patenMaintenance()->latest('created_at')->first();

        $paidYears = $paidPatent->map(function ($item) {
            return $item->payment_date->format('Y');
        })->unique()->values()->toArray();

        $paymentDates = [];
        foreach ($paidPatent as $item) {
            $year = $item->payment_date->format('Y');
            $paymentDates[$year][] = $item->payment_date->toDateString();
        }

        $startYear = !empty($paidYears) ? min($paidYears) : now()->year;

        return view('components.patent.patent-detail', compact(
            'patent',
            'paidPatent',
            'paidYears',
            'paymentDates',
            'startYear',
            'patentMaintenance'
        ));
    }

    public function autocompleteTitle(Request $request)
    {
        $query = $request->get('query');
        $data = Paper::where('innovation_title', 'like', '%' . $query . '%')
            ->select('id', 'innovation_title')
            ->take(1000)
            ->get();

        return response()->json($data);
    }

    public function autocompletePic(Request $request)
    {
        $titleId = $request->get('title_id');

        // Ambil paper berdasarkan ID
        $paper = Paper::with('team.members')->find($titleId);

        if (!$paper || !$paper->team) {
            return response()->json([]);
        }

        // Ambil anggota tim dari relasi
        $members = $paper->team->members->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
            ];
        });

        return response()->json($members);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|exists:papers,id',
            'pic_id' => 'required|exists:users,id',
            'status' => 'required|string',
        ]);

        Patent::create([
            'paper_id' => $validated['title_id'],
            'person_in_charge' => $validated['pic_id'],
            'application_status' => $validated['status'],
            'patent_status' => 'Belum Aktif',
        ]);

        return redirect()->back()->with('success', 'Data Pengajuan Berhasil Ditambahkan');
    }

    public function updateStatus(Request $request, Patent $patent)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'patent_id' => 'required|exists:patent,id',
            'registration_number' => 'nullable|string',
            'application_file' => 'nullable|file|mimes:pdf|max:5120',
            'administrative_file' => 'nullable|file|mimes:pdf|max:5120',
            'publication_file' => 'nullable|file|mimes:pdf|max:5120',
            'certificate' => 'nullable|file|mimes:pdf|max:5120',
            'appeal_file' => 'nullable|file|mimes:pdf|max:5120',
            'reject_file' => 'nullable|file|mimes:pdf|max:5120',
            'patent_title' => 'nullable|string|max:255',
        ]);
    
        $randomNumber = mt_rand(1000, 9999);
        $patent = Patent::findOrFail($validated['patent_id']);
        $patent->registration_number = $validated['registration_number'];
    
        $paperTitle = optional($patent->paper)->innovation_title;
        $patent->patent_title = $validated['patent_title'] ?? $paperTitle;
        $patent->application_status = $validated['status'];
    
        // Daftar semua field file dan subfoldernya
        $fileFields = [
            'application_file' => 'application_file',
            'administrative_file' => 'administrative_file',
            'publication_file' => 'publication_file',
            'certificate' => 'certificate',
            'appeal_file' => 'appeal_file',
            'reject_file' => 'reject_file',
        ];
    
        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                try {
                    // Hapus file lama
                    if ($patent->$field && Storage::exists($patent->$field)) {
                        Storage::delete($patent->$field);
                    }
    
                    $extension = strtolower($request->file($field)->getClientOriginalExtension());
                    $baseName = $patent->registration_number ?: $patent->person_in_charge ?: 'paten';
                    $fileName = $baseName . '_' . $folder . '_' . $randomNumber . '.' . $extension;
    
                    $filePath = $request->file($field)->storeAs(
                        "private/patent/$folder",
                        $fileName
                    );
    
                    $patent->$field = $filePath;
                } catch (\Exception $e) {
                    return back()->with('error', 'Gagal mengunggah file.');
                }
            }
        }
    
        $patent->save();
    
        return redirect()->route('patent.index')->with('success', 'Data Paten Berhasil Diperbarui');
    }

    // Untuk Upload Dokumen
    public function uploadDocument(Request $request, $patentId, $documentType)
    {
        $allowedTypes = ['ownership_letter', 'statement_of_transfer_rights', 'draft_paten'];

        if (!in_array($documentType, $allowedTypes)) {
            abort(400, 'Jenis dokumen tidak valid.');
        }
        
        $validated = $request->validate([
            $documentType => 'required|file|mimes:pdf|max:5120',
        ]);

        $patent = Patent::findOrFail($patentId);
        $userName = Auth::user()->name;
        $picName = str_replace(' ', '_', strtolower($userName));
        $randomNumber = mt_rand(1000, 9999);

        try {
            // Draft
            if ($request->hasFile($documentType)) {
                if ($patent->$documentType && Storage::exists($patent->$documentType)) {
                    Storage::delete($patent->$documentType);
                }
                $extension = $request->file($documentType)->getClientOriginalExtension();
                $fileName = $picName . '_' . $documentType . '_' . $randomNumber . '.' . strtolower($extension);
                $filePath = $request->file($documentType)->storeAs('private/patent/'. $documentType .'/', $fileName);

                // Timpa field draft_patent
                $patent->$documentType = $filePath;
            }

            $patent->save(); // <--- penting! buat commit perubahan ke database

        } catch (\Exception $e) {
            return redirect()->route('patent.index')->with('error', 'Upload Gagal: Silahkan Coba Lagi Nanti atau Hubingi Pengelola Inovasi');
        }

        return redirect()->route('patent.index')->with('success', 'Dokumen berhasil diperbarui.');
    }

    // Method untuk search patents
    public function search(Request $request)
    {
        $query = strtolower($request->input('q'));

        $patentData = Patent::with(['paper', 'employee', 'patenMaintenance'])
    ->whereHas('paper', function ($q) use ($query) {
        $q->whereRaw('LOWER(innovation_title) LIKE ?', ["%{$query}%"]);
    })  // Pencarian berdasarkan 'innovation_title' dari relasi paper
    ->visibleTo(Auth::user())  
    ->paginate(10);

        // Kembalikan hanya bagian body dari tabel agar dapat di-render menggunakan Ajax
        return view('components.patent.patent-body-table', compact('patentData'));
    }

    public function updateTemplateDocument(Request $request)
    {
        $request->validate([
            'draft_paten' => 'file|mimes:pdf,docx|max:2048',
            'ownership_letter' => 'file|mimes:pdf,docx|max:2048',
            'statement_of_transfer_rights' => 'file|mimes:pdf,docx|max:2048',
        ]);

        $template = TemplateDocumentPatent::first();

        if (!$template) {
            return redirect()->route('patent.index')->with('error', 'Template tidak ditemukan.');
        }

        try {
            $updated = false; // <-- Tambahkan ini buat cek ada file yang diupload atau tidak

            if ($request->hasFile('draft_paten')) {
                if ($template->draft_paten) {
                    Storage::delete($template->draft_paten);
                }
                
                $extension = $request->file('draft_paten')->getClientOriginalExtension();
                $draftPath = $request->file('draft_paten')->storeAs('public/patent/draft_paten', 'template_draft_paten.' . $extension);
                $template->draft_paten = $draftPath;
                $updated = true;
            }

            if ($request->hasFile('ownership_letter')) {
                if ($template->ownership_letter) {
                    Storage::delete($template->ownership_letter);
                }
                
                $extension = $request->file('ownership_letter')->getClientOriginalExtension();
                $ownerLetterPath = $request->file('ownership_letter')->storeAs('public/patent/ownership_letter', 'template_ownership_letter.' . $extension);
                $template->ownership_letter = $ownerLetterPath;
                $updated = true;
            }

            if ($request->hasFile('statement_of_transfer_rights')) {
                if ($template->statement_of_transfer_rights) {
                    Storage::delete($template->statement_of_transfer_rights);
                }
                
                $extension = $request->file('statement_of_transfer_rights')->getClientOriginalExtension();
                $statementPath = $request->file('statement_of_transfer_rights')->storeAs('public/patent/statement_of_transfer_rights', 'template_statement_of_transfer_rights.' . $extension);
                $template->statement_of_transfer_rights = $statementPath;
                $updated = true;
            }

            if ($updated) {
                $template->save();
                return redirect()->route('patent.index')->with('success', 'Template berhasil diperbarui.');
            } else {
                return redirect()->route('patent.index')->with('error', 'Tidak ada file yang diupload.');
            }

        } catch (Exception $e) {
            return redirect()->route('patent.index')->with('error', 'Terjadi kesalahan saat memperbarui template.');
        }
    }

    public function documentView($patentId, $documentType)
    {
        $filePath = storage_path('app/' . ltrim(Patent::where('id', '=', $patentId)->pluck($documentType)[0], '/'));
        if (!file_exists($filePath)) {
            throw new Exception("Error, file tidak ada");
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0'
        ]);
    }

    public function downloadTemplateDownload($documentType)
    {
        $template = TemplateDocumentPatent::first();
        $filePath = storage_path('app/' . ltrim($template->pluck($documentType)[0], '/'));
        if (!file_exists($filePath)) {
            throw new Exception("Error, file tidak ada");
        }

        return response()->download($filePath);
    }

    public function uploadPatentPaymentProof(Request $request)
    {
        $validated = $request->validate([
            'patent_id' => 'required|exists:patent,id',
            'payment_proof' => 'required|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'payment_date' => 'required|date',
            'payment_amount' => 'required|int'
        ]);

        try {
            $patent = Patent::findOrFail($validated['patent_id']);

            $extension = $request->file('payment_proof')->getClientOriginalExtension();
            $date = now()->format('Ymd');
            
            $fileName = $patent->registration_number . '_' . $date . '_payment_proof.' . $extension;
            
            if ($request->hasFile('payment_proof')) {
                $paymentProof = $request->file('payment_proof')->storeAs('private/patent/payment_proof/', $fileName);
            }
    
            PatentMaintenance::create([
                'patent_id' => $validated['patent_id'],
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['payment_amount'],
                'payment_proof' => $paymentProof,
                'status' => 'paid',
            ]);

            return redirect()->route('patent.detailInfo', ['patentId' => $validated['patent_id']])->with('success', 'Pembayaran Baru Telah Berhasil Diunggah');
        } catch (\Exception $e) {
            return redirect()->route('patent.detailInfo', ['patentId' => $validated['patent_id']])->with('error', 'Upload Bukti Pembayaran Tidak Berhasil');
        }
    }

    public function viewPatentDocument($patentId, $file)
    {
        $patent = Patent::findOrFail($patentId);
        
        if (!$patent->$file) {
            return redirect()->back()->with('error', 'File ' . str_replace('_', ' ', $file) . ' tidak ditemukan.');
        }

        $filePath = storage_path('app/' . ltrim($patent->$file, '/'));
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }
    
    public function storeCertificate(Request $request, $patentId)
    {
        $validated = $request->validate([
            'ceritificate_number' => 'required|string|max:255',
            'certificate_file' => 'nullable|file|mimes:pdf|max:5120'
        ]);
        try{
            $patent = Patent::findOrFail($patentId);
            $randomNumber = mt_rand(1000, 9999);
            
            if($request->has('certificate_file')){
                if ($patent->certificate) {
                    Storage::delete($patent->certificate);
                }
                
                $extension = $request->file('certificate_file')->getClientOriginalExtension();
                $ownerLetterPath = $request->file('certificate_file')->storeAs('privat/patent/certificate', 'certificate_'. $patent->registration_number .'_'. $randomNumber .'.' . $extension);
                $patent->certificate = $ownerLetterPath;
            }
            $patent->certificate_number = $validated['ceritificate_number'];
            
            $patent->save();
            
            return redirect()->route('patent.detailInfo', ['patentId' => $validated['patent_id']])->with('success', 'Upload Sertifikat Berhasil');
        } catch (\Exception $e) {
            return redirect()->route('patent.detailInfo', ['patentId' => $validated['patent_id']])->with('error', 'Upload Sertifikat Tidak Berhasil');
        }
    }
}