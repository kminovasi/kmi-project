<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paper;
use App\Models\User;
use App\Models\Team;
use App\Models\Event;
use App\Models\CustomBenefitFinancial;
use App\Models\PvtCustomBenefit;
use App\Models\PvtMember;
use App\Mail\EmailNotificationBenefit;
use App\Models\PvtEventTeam;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class BenefitController extends Controller
{
    //
    public function getAllCustomBenefitFinancial()
    {
        $data = CustomBenefitFinancial::all();
        return response()->json($data);
    }
    public function createBenefitAdmin()
    {
        $data = Paper::join('teams', 'papers.team_id', '=', 'teams.id')
            ->select(
                'papers.id as paper_id',
                'teams.id as team_id',
                'team_name',
                'company_code',
                'innovation_title'
            )
            ->get();
        return view('auth.admin.benefit.index', ['data' => $data]);
    }
    
    public function createBenefitUser($id)
    {

        $row = Paper::join('teams', 'papers.team_id', '=', 'teams.id')
            ->select(
                'papers.id as paper_id',
                'teams.id as team_id',
                'teams.company_code as company_code',
                'financial',
                'file_review',
                'potential_benefit',
                'non_financial',
                'papers.status_rollback',
                DB::raw('
                CASE
                    WHEN papers.status = \'not finish\' THEN \'nf\'
                    WHEN papers.status = \'not accepted\' THEN \'na\'
                    WHEN papers.status = \'accepted by facilitator\' THEN \'abf\'
                    WHEN papers.status = \'rejected by facilitator\' THEN \'rbf\'
                    WHEN papers.status = \'accepted by innovation admin\' THEN \'abia\'
                    WHEN papers.status = \'rejected by innovation admin\' THEN \'rbia\'
                    ELSE papers.status
                END as status')
            )
            ->where('papers.id', $id)->first();

        if (Auth::user()->role === 'Superadmin') {
            $is_owner = true;
        } else {
            $is_owner = PvtMember::where('employee_id', auth()->user()->employee_id)
                ->where('team_id', $row->team_id) // Cek apakah user adalah bagian dari tim yang terkait dengan paper
                ->whereIn('status', ['member', 'leader']) // Atau status lain yang menunjukkan pemilik benefit
                ->exists(); // Gunakan exists() untuk cek keberadaan pemilik benefit
        }


        $file_content = null;
        if ($row->file_review) {
            $filePath = storage_path('app/public/' . $row->file_review);
            if (file_exists($filePath)) {
                $file_content = file_get_contents($filePath);
            }
        }

        $benefit_custom = CustomBenefitFinancial::query()->get()->keyBy('id')->toArray();


        // $benefit_custom = $benefit_custom_query;

        foreach ($benefit_custom as $bencus) {
            $pvt = PvtCustomBenefit::where('custom_benefit_financial_id', $bencus['id'])
                ->where('paper_id', $id)
                ->first();
            if ($pvt) {
                $benefit_custom[$bencus['id']]['value'] = $pvt->value;
            } else {
                $benefit_custom[$bencus['id']]['value'] = null;
            }
        }
        $gm = PvtMember::where('team_id', $row->team_id)->where('status', 'gm')->first();
        $gmName = null;
        if ($gm !== null) {
            $gmName = User::where('employee_id', $gm->employee_id)->select('name', 'employee_id')->first();
        } else {
            $gmName = null;
        }

        if (PvtEventTeam::where('team_id', $row->team_id)->exists()) {
            $statusEventTeam = PvtEventTeam::where('team_id', $row->team_id)->first();
            $isWinnerStatusTeam = $statusEventTeam->status === 'Juara' ? true : false;
            if (Auth::user()->role === 'Superadmin') {
                // Superadmin bisa edit jika tim berstatus Juara
                $is_owner = $isWinnerStatusTeam;
            } else {
                // Untuk user biasa (pemilik paper/benefit)
                $is_owner = PvtMember::where('employee_id', auth()->user()->employee_id)
                    ->where('team_id', $row->team_id)
                    ->whereIn('status', ['member', 'leader'])
                    ->exists() && !$isWinnerStatusTeam; // Tambahkan pengecekan NOT isWinnerStatusTeam
            }
        }
        $is_disabled = true;

        if (($row->status_rollback == 'rollback benefit' ||
                $row->status == 'accepted paper by facilitator' ||
                $row->status == 'upload benefit' ||
                $row->status == 'rejected benefit by facilitator' ||
                $row->status == 'revision benefit by facilitator' ||
                $row->status == 'rejected benefit by general manager' ||
                $row->status == 'revision benefit by general manager' ||
                $row->status == 'revision paper and benefit by general manager' ||
                $row->status == 'revision paper and benefit by innovation admin' || $row->status == 'revision benefit by innovation admin') &&
            $is_owner
        ) {
            $is_disabled = false;
        } else {
            $is_disabled = true;
        }

        return view('auth.user.benefit.index', compact('row', 'benefit_custom', 'file_content', 'is_owner', 'gmName', 'is_disabled'));
    }

    public function storeBenefitUser(Request $request, $id)
    {
        $validatedData = $request->validate([
            'oldGm' => 'required_without:gm_id', // gmOld harus ada jika gm_id tidak ada
            'gm_id' => 'required_without:oldGm', // gm_id harus ada jika gmOld tidak ada
            'bencus.*' => 'nullable'
        ], [
            'oldGm.required_without' => 'GM harus di isi.',
            'gm_id.required_without' => 'GM harus di isi.',
        ]);

        $record = Paper::with('team')->findOrFail($id);

        // Mendapatkan nama tim
        $teamName = $record->team ? $record->team->team_name : 'Team tidak ditemukan';

        // Assign fields from request
        $record->financial = $request->financial;
        $record->potential_benefit = $request->potential_benefit;
        $record->non_financial = $request->non_financial;
        $record->potensi_replikasi = $request->input('potensi_replikasi');

        // Logika untuk file_review
        if ($request->hasFile('file_review')) {
            // Hapus file lama jika ada
            if ($record->file_review) {
                Storage::disk('public')->delete($record->file_review);
            }

            // Simpan file baru
            $record->file_review = $request->file('file_review')->storeAs(
                'file_review',
                $record->team_name . "." . $request->file('file_review')->extension(),
                'public'
            );
        } else {
            if ($record->file_review === null) {
                return redirect()->route('benefit.create.user', ['id' => $id])->withErrors("Error: File Review harus di upload");
            }
        }

        $record->status = 'upload benefit';
        $record->updateAndHistory([], 'update benefit form');

        // Update custom benefit
        if (isset($request->bencus)) {
            foreach ($request->bencus as $index => $bc) {
                PvtCustomBenefit::UpdateOrCreate([
                    'paper_id' => $id,
                    'custom_benefit_financial_id' => $index,
                ], [
                    'value' => $bc
                ]);
            }
        }

        // Logika untuk gm_id dan gmOld
        $idTeam = $request->team_id;

        // Cek apakah sudah ada GM dalam tim
        $existingGM = PvtMember::where('team_id', $idTeam)
            ->where('status', 'gm')
            ->first();

        if ($existingGM !== null) {
            if ($request->gm_id !== null) {
                PvtMember::where('team_id', $idTeam)
                    ->where('employee_id', $existingGM->employee_id)
                    ->update([
                        'employee_id' => $request->gm_id,
                        'status' => 'gm'
                    ]);
            } else {
                PvtMember::where('team_id', $idTeam)
                    ->where('employee_id', $existingGM->employee_id)
                    ->update([
                        'employee_id' => $request->oldGm,
                        'status' => 'gm'
                    ]);
            }
        } else {
            if ($request->gm_id !== null) {
                PvtMember::create([
                    'team_id' => $idTeam,
                    'employee_id' => $request->gm_id,
                    'status' => 'gm'
                ]);
            } else {
                return redirect()->route('benefit.create.user', [$id])->withErrors("Error: Gm tidak boleh kosong");
            }
        }

        // Mengirim email jika status 'upload benefit'
        if ($record->status === 'upload benefit' && $record->team) {
            // Ambil data fasilitator
            $fasilId = PvtMember::where('team_id', $record->team->id)
                ->where('status', 'facilitator')
                ->pluck('employee_id')
                ->first();

            $fasilData = User::where('employee_id', $fasilId)
                ->select('name', 'email')
                ->first();

            // Ambil data leader
            $leaderId = PvtMember::where('team_id', $record->team->id)
                ->where('status', 'leader')
                ->pluck('employee_id')
                ->first();

            $leaderData = User::where('employee_id', $leaderId)
                ->select('name', 'email')
                ->first();

            $inovasi_lokasi = $record->inovasi_lokasi;

            // Membuat objek email dan mengirim email
            $mail = new EmailNotificationBenefit(
                $record,
                $record->status,
                $record->innovation_title,
                $record->team->team_name,
                $leaderData,
                $record->financial,
                $record->potential_benefit,
                $record->potensi_replikasi,
                $record->non_financial,
                $fasilData,
                $inovasi_lokasi
            );

            Mail::to($fasilData->email)->send($mail);
        } else {
            throw new \Exception('Paper tidak memiliki relasi dengan Team.');
        }

        return redirect()->route('paper.index')->with('success', 'Data berhasil diperbarui');
    }


    public function approveBenefit(Request $request, $id)
    {
        $record = Paper::with('team')->findOrFail($id);
        //$paper = Paper::with('team')->findOrFail($id);
        //$paper->status = 'accepted benefit by facilitator';
        $record->status = 'accepted benefit by facilitator';
        //$paper->save();
        $record->save();

        return redirect()->route('paper.approveBenefitFasil', [$id, 'status' => 'accepted benefit by facilitator']);
        //return redirect()->back()->with('success', 'Paper approved successfully!');
    }

    public function rejectBenefit(Request $request, $id)
    {
        $record = Paper::with('team')->findOrFail($id);
        //$paper = Paper::with('team')->findOrFail($id);
        //$paper->status = 'rejected benefit by facilitator';
        $record->status = 'rejected benefit by facilitator';
        //$paper->save();
        $record->save();

        return redirect()->route('paper.approveBenefitFasil', [$id, 'status' => 'rejected benefit by facilitator']);
        //return redirect()->back()->with('success', 'Paper rejected successfully!');
    }

    public function showAllBenefit(Request $request, $customBenefitPotentialId)
    {
        $customBenefitPotentialName = CustomBenefitFinancial::findOrFail($customBenefitPotentialId)->name_benefit;
        if ($request->ajax()) {
            // Ambil data papers dengan relasi ke customBenefitFinancial
            $data = PvtCustomBenefit::where('custom_benefit_financial_id', $customBenefitPotentialId)
                ->with(['customBenefitFinancial', 'paper']) // Sertakan relasi dengan Paper dan CustomBenefitFinancial
                ->get();

            return DataTables::of($data)
                ->addColumn('name_benefit', function ($row) {
                    return $row->customBenefitFinancial->name_benefit ?? '-';
                })
                ->addColumn('paper_title', function ($row) {
                    return $row->paper->innovation_title ?? '-';
                })
                ->addColumn('description', function ($row) {
                    return $row->value ?? '-';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-primary"  type="button" data-bs-toggle="modal" data-bs-target="#detailTeamMember"  onclick="get_data_on_modal(' . $row->paper->team_id . ')">Detail Team</button>';
                })
                ->make(true);
        }
        return view('dashboard.non-financial-benefit-table', compact('customBenefitPotentialId', 'customBenefitPotentialName'));
    }
}