<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
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
        $eventsWithoutCertificate = \DB::table('events')
            ->leftjoin('certificates' , 'events.id', '=', 'certificates.event_id')
            ->whereNull('certificates.event_id')
            ->select(
                'events.id as event_id',
                'events.event_name',
                'events.year',
                'certificates.template_path')
            ->get();

        // dd($eventsWithoutCertificate);

        $certificates = Certificate::with('event')->get();
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

        // dd($request->all());

        $path = $request->file('template')->store('certificate', 'public');

        Certificate::create([
            'event_id' => $request->event_id,
            'template_path' => $path,
        ]);

        return redirect()->route('certificates.index')->with('success', 'Sertifikat berhasil dibuat.');
    }

    /**
     * Update the specified certificate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'template' => 'nullable|file|mimes:jpg,jpeg,png'
    //     ]);

    //     $certificate = Certificate::findOrFail($id);

    //     if ($request->hasFile('template')) {
    //         $path = $request->file('template')->store('certificates', 'public');
    //         $certificate->template_path = $path;
    //     }

    //     $certificate->update([
    //         'title' => $request->title,
    //     ]);

    //     return redirect()->route('certificates.index')->with('success', 'Sertifikat berhasil diperbarui.');
    // }

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
