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
        ));
    }

    public function autocompleteTitle(Request $request)
    {
        $query = $request->get('query');
        $data = Paper::where('innovation_title', 'like', '%' . $query . '%')
            ->select('id', 'innovation_title')
            ->get()
            ->take(5);

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

    // Untuk Update Status
    public function updateStatus(Request $request, Patent $patent)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'patent_id' => 'required|exists:patent,id',
            'registration_number' => 'required|string',
        ]);

        $patent = Patent::findOrFail($validated['patent_id']);
        $patent->registration_number = $validated['registration_number'];
        $patent->application_status = $validated['status'];
        $patent->save();

        return redirect()->route('patent.index')->with('success', 'Status berhasil diperbarui.');
    }

    // Untuk Upload Dokumen
    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'patent_id-doc' => 'required|exists:patent,id',
            'statement_of_transfer_rights' => 'nullable|file|mimes:pdf,docx,txt|max:2048',
            'owner_letter' => 'nullable|file|mimes:pdf,docx,txt|max:2048',
            'draft' => 'nullable|file|mimes:pdf,docx,txt|max:2048',
        ]);

        $patent = Patent::findOrFail($validated['patent_id-doc']);
        $userName = Auth::user()->name;
        $picName = str_replace(' ', '_', strtolower($userName));
        $randomNumber = mt_rand(1000, 9999);

        try {
            // Draft
            if ($request->hasFile('draft')) {
                $extension = $request->file('draft')->getClientOriginalExtension();
                $fileName = $picName . '_draft_' . $randomNumber . '.' . $extension;
                $fileDraftPath = $request->file('draft')->storeAs('private/patent/draft_paten/', $fileName);

                // Timpa field draft_patent
                $patent->draft_paten = $fileDraftPath;
            }

            // Owner Letter
            if ($request->hasFile('owner_letter')) {
                $extension = $request->file('owner_letter')->getClientOriginalExtension();
                $fileName = $picName . '_owner_letter_' . $randomNumber . '.' . $extension;
                $fileOwnerLetterPath = $request->file('owner_letter')->storeAs('private/patent/ownership_letter/', $fileName);

                // Timpa field ownership_letter
                $patent->ownership_letter = $fileOwnerLetterPath;
            }

            // Statement of Transfer Rights
            if ($request->hasFile('statement_of_transfer_rights')) {
                $extension = $request->file('statement_of_transfer_rights')->getClientOriginalExtension();
                $fileName = $picName . '_statement_of_transfer_rights_' . $randomNumber . '.' . $extension;
                $fileStatementOfTransferRightsPath = $request->file('statement_of_transfer_rights')->storeAs('private/patent/statement_of_transfer_rights/', $fileName);

                // Timpa field statement_of_transfer_rights
                $patent->statement_of_transfer_rights = $fileStatementOfTransferRightsPath;
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
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
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
        ]);

        try {
            $patent = Patent::findOrFail($validated['patent_id']);
            $fileName = $patent->registration_number . '_payment_proof.' . $request->file('payment_proof')->getClientOriginalExtension();
            
            if ($request->hasFile('payment_proof')) {               
                $paymentProof = $request->file('payment_proof')->storeAs('private/patent/payment_proof/', $fileName);
            }
    
            PatentMaintenance::create([
                'patent_id' => $validated['patent_id'],
                'payment_date' => $validated['payment_date'],
                'amount' => $patent->amount,
                'payment_proof' => $paymentProof,
                'status' => 'paid',
            ]);

            return redirect()->route('patent.detailInfo', ['patentId' => $validated['patent_id']])->with('success', 'Pembayaran Baru Telah Berhasil Diunggah');
        } catch (\Exception $e) {
            return redirect()->route('patent.detailInfo', ['patentId' => $validated['patent_id']])->with('error', 'Upload Bukti Pembayaran Tidak Berhasil');
        }
    }
}