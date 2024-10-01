<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BeritaAcara;
use App\Models\Event;
use Illuminate\Support\Str;

class DokumentasiController extends Controller
{
    //
    public function index(){
        return view('auth.admin.dokumentasi.index');
    }
    public function indexBeritaAcara(){
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
                            ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
                            ->get();
        $event = Event::where('status', 'active')->get();
        return view('auth.admin.dokumentasi.berita-acara.index', ['data' => $data, 'event' => $event]);
    }
    public function uploadBeritaAcara(Request $request, $id){
        // dd($request->all());
        try{
            //update PPT
            $upload = $request->file('signed_file');
            $file = $upload->storeAs(
                    'berita_acara/file_upload',
                    Str::slug(pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $upload->getClientOriginalExtension(),
                    'public'
            );

            $upPDF = BeritaAcara::findOrFail($id);
            $upPDF->signed_file = $file;
            $upPDF->save();

            return redirect()->back()->with('success', 'update successfully');
        }catch(Exception $e){
            // DB::rollback();
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }
}
