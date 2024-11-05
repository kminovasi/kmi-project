<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use Auth;
use DB;
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
            $eventsWithoutCertificate = \DB::table('events')
                ->leftjoin('certificates', 'events.id', '=', 'certificates.event_id')
                // ->leftJoin('companies', 'companies.company_code', '=', 'events.company_code')
                ->whereNull('certificates.event_id')
                ->where('events.company_code', '=', $userRole->company_code)
                ->select(
                    'events.id as event_id',
                    'events.event_name',
                    'events.year',
                    'certificates.template_path'
                )
                ->get();

                $certificates = Certificate::with(['event.company'])
                ->whereHas('event', function ($query) use ($userRole) {
                    $query->where('company_code', '=', $userRole->company_code);
                })
                ->get();

        } else {
            $eventsWithoutCertificate = \DB::table('events')
                ->leftjoin('certificates', 'events.id', '=', 'certificates.event_id')
                ->whereNull('certificates.event_id')
                ->leftJoin('companies', 'companies.company_code', '=', 'events.company_code')
                ->select(
                    'events.id as event_id',
                    'events.event_name',
                    'events.year',
                    'companies.company_name',
                    'certificates.template_path'
                )
                ->get();


                $certificates = Certificate::with('event.company')->get();
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
            'template' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('template')->store('certificate', 'public');

        Certificate::create([
            'event_id' => $request->event_id,
            'template_path' => $path,
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

        if (Storage::disk('public')->exists($certificate->template_path)) {
            Storage::disk('public')->delete($certificate->template_path);
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
