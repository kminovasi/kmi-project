<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BeritaAcara;
use App\Models\Event;
use Illuminate\Support\Str;
use Storage;

class DokumentasiController extends Controller
{
    //
    public function index()
    {
        return view('auth.admin.dokumentasi.index');
    }
    public function indexBeritaAcara()
    {
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
            ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
            ->get();
        $event = Event::where('status', 'active')->get();
        return view('auth.admin.dokumentasi.berita-acara.index', ['data' => $data, 'event' => $event]);
    }
    public function uploadBeritaAcara(Request $request, $id)
    {
        try {
            // Validasi bahwa file harus PDF dengan maksimal ukuran 2MB
            $request->validate([
                'signed_file' => 'required|mimes:pdf|max:2048', // Hanya file dengan format PDF dan maksimal 2MB
            ]);

            // Proses upload file
            $upload = $request->file('signed_file');

            // Temukan record berita acara
            $upPDF = BeritaAcara::findOrFail($id);

            // Cek jika sudah ada file sebelumnya
            if ($upPDF->signed_file) {
                // Hapus file lama
                Storage::disk('public')->delete($upPDF->signed_file);
            }

            // Simpan file baru
            $file = $upload->storeAs(
                'berita_acara/file_upload',
                Str::slug(pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $upload->getClientOriginalExtension(),
                'public'
            );

            // Update informasi file pada database
            $upPDF->signed_file = $file;
            $upPDF->save();

            return redirect()->back()->with('success', 'File berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $beritaAcara = BeritaAcara::findOrFail($id);
            if ($beritaAcara->signed_file) {
                // Hapus file dari storage
                Storage::disk('public')->delete($beritaAcara->signed_file);

                // Set kolom signed_file menjadi null
                $beritaAcara->signed_file = null;
                $beritaAcara->save();
            }

            return redirect()->back()->with('success', 'File berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

}