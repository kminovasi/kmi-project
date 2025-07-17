<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Display a listing of the certificates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userRole = Auth::user();
        // dd($userRole);

        if ($userRole->role == 'Admin') {
            $eventsWithoutCertificate = DB::table('events')
                ->leftjoin('certificates', 'events.id', '=', 'certificates.event_id')
                ->leftJoin('company_event', 'events.id', '=', 'company_event.event_id')
                ->leftJoin('companies', 'company_event.company_id', '=', 'companies.id')
                ->whereNull('certificates.event_id')
                ->where('companies.company_code', '=', $userRole->company_code)
                ->select(
                    'events.id as event_id',
                    'events.event_name',
                    'events.year',
                    'certificates.template_path'
                )
                ->distinct()
                ->get();

            $certificates = Certificate::with(['event.companies'])
                ->whereHas('event.companies', function ($query) use ($userRole) {
                    $query->where('company_code', '=', $userRole->company_code);
                })
                ->get();

        } else {
            $eventsWithoutCertificate = DB::table('events')
                ->leftjoin('certificates', 'events.id', '=', 'certificates.event_id')
                ->leftJoin('company_event', 'events.id', '=', 'company_event.event_id')
                ->leftJoin('companies', 'company_event.company_id', '=', 'companies.id')
                ->whereNull('certificates.event_id')
                ->select(
                    'events.id as event_id',
                    'events.event_name',
                    'events.year',
                    'certificates.template_path'
                )
                ->distinct()
                ->get();

            $certificates = Certificate::with('event.companies')->distinct()->get();
        }

        return view("admin.certificate.certificate", compact('certificates', 'eventsWithoutCertificate'));
    }

    /**
     * Store a newly created certificate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'template_certificate' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'badge_rank_1' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'badge_rank_2' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'badge_rank_3' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);
    
        $certificateTemplatePath = $request->file('template_certificate')->store('certificate', 'public');
    
        $badgeRank1Path = $request->file('badge_rank_1')->store('certificate/badge', 'public');
        $badgeRank2Path = $request->file('badge_rank_2')->store('certificate/badge', 'public');
        $badgeRank3Path = $request->file('badge_rank_3')->store('certificate/badge', 'public');
    
        Certificate::create([
            'event_id' => $request->event_id,
            'template_path' => $certificateTemplatePath,
            'badge_rank_1' => $badgeRank1Path,
            'badge_rank_2' => $badgeRank2Path,
            'badge_rank_3' => $badgeRank3Path,
        ]);
    
        return redirect()->route('certificates.index')->with('success', 'Sertifikat berhasil dibuat.');
    }


    /**
     * Remove the specified certificate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
    
        $paths = [
            $certificate->template_path,
            $certificate->badge_rank_1,
            $certificate->badge_rank_2,
            $certificate->badge_rank_3,
        ];
    
        foreach ($paths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    
        $certificate->delete();
    
        return redirect()->route('certificates.index')->with('success', 'Sertifikat berhasil dihapus.');
    }

    /**
     * Activate the specified certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        // Nonaktifkan semua sertifikat lain
        Certificate::where('is_active', true)->update(['is_active' => false]);

        // Aktifkan sertifikat yang dipilih
        $certificate = Certificate::findOrFail($id);
        $certificate->is_active = true;
        $certificate->save();

        return redirect()->route('certificates.index')->with('success', 'Sertifikat berhasil diaktifkan.');
    }
}