<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CoachingClinic;
use App\Models\PvtMember;
use Illuminate\Support\Facades\Auth;

class CoachingClinicController extends Controller
{
    public function index()
    {
        // Ambil semua data Coaching Clinic
        $coachingClinics = CoachingClinic::with(['team', 'company', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Kirim data ke view
        return view('dashboard.coaching.index', compact('coachingClinics'));
    }

    public function storeCoachingClinic(Request $request)
    {
        // Validasi data yang diterima
        $validatedData = $request->validate([
            'input_team_id' => 'required|exists:teams,id',
            'coaching_date' => 'required|date'
        ]);

        $isMember = PvtMember::where('team_id', $validatedData['input_team_id'])
            ->where('employee_id', Auth::user()->employee_id)
            ->exists();

        if (!$isMember) {
            return redirect()->route('paper.index')->with('error', 'Anda bukan anggota tim ini.');
        }

        try{
            // Simpan data Coaching Clinic
            CoachingClinic::create([
                'person_in_charge' => Auth::user()->employee_id,
                'company_code' => Auth::user()->company_code,
                'team_id' => $validatedData['input_team_id'],
                'coaching_date' => $validatedData['coaching_date'],
                'status' => 'pending',
            ]);

            // Redirect atau tampilkan pesan sukses
            return redirect()->route('paper.index')->with('success', 'Coaching Clinic berhasil ditambahkan.');  
        
        } catch (\Exception $e) {
            // Log error atau lakukan penanganan kesalahan lainnya
            return redirect()->route('paper.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }      
    }

    public function updateCoachingApply($coachingId, $status, Request $request)
    {
        // Temukan Coaching Clinic berdasarkan ID
        $coachingClinic = CoachingClinic::findOrFail($coachingId);

        if ($status == 'accept') {
            $validate = $request->validate([
                'coaching_date' => ['nullable', 'date'],
            ]);
            $coachingDate = $request->input('coaching_date') ?: $coachingClinic->coaching_date;;
        } elseif ($status == 'reject') {
            $coachingDate = $coachingClinic->coaching_date;
        } else {
            return redirect()->route('coaching-clinic.index')->with('error', 'Status tidak valid.');
        }

        // Update status Coaching Clinic
        $coachingClinic->update([
            'status' => $status,
            'coaching_date' => $coachingDate,
        ]);

        // Redirect atau tampilkan pesan sukses
        return redirect()->route('coaching-clinic.index')->with('success', 'Status Coaching Clinic berhasil diperbarui.');   
    }
}