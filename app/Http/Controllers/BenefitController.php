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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BenefitController extends Controller
{
    //
    public function createBenefitAdmin()
    {
        // dd(Auth::user());
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

            $is_owner = PvtMember::where('employee_id', auth()->user()->employee_id)
            ->where('team_id', $row->team_id) // Cek apakah user adalah bagian dari tim yang terkait dengan paper
            ->where('status', ['leader', 'member']) // Atau status lain yang menunjukkan pemilik benefit
            ->exists(); // Gunakan exists() untuk cek keberadaan pemilik benefit


            $file_content = null;
            if ($row->file_review) {
                // Perbaiki path ke file sesuai dengan direktori yang benar
                $file_path = $row->file_review;

                // Cek apakah file benar-benar ada sebelum mencoba mengambilnya
                if (Storage::disk('public')->exists($file_path)) {
                    // Ambil konten file
                    $file_content = Storage::disk('public')->get($file_path);
                }
            }

            $benefit_custom = CustomBenefitFinancial::where('company_code', $row->company_code)
            ->get()->keyBy('id')->toArray();
        // $benefit_custom = $benefit_custom_query;

        foreach ($benefit_custom as $bencus) {
            $pvt = PvtCustomBenefit::where('custom_benefit_financial_id', $bencus['id'])
                ->where('paper_id', $id)
                ->first();
            if ($pvt) {
                $benefit_custom[$bencus['id']]['value'] = $pvt->getValueFormattedAttribute();
            } else {
                $benefit_custom[$bencus['id']]['value'] = null;
            }
        }
        $gm = PvtMember::where('team_id', $row->team_id)->where('status', 'gm')->first();
        $gmName = null;
        if($gm !== null){
            $gmName = User::where('employee_id', $gm->employee_id)->select('name')->first();
        }else{
            $gmName = null;
        }
        return view('auth.user.benefit.index', compact('row', 'benefit_custom', 'file_content', 'is_owner', 'gmName'));
    }

    public function storeBenefitUser(Request $request, $id)
    {
        //dd($request->all());

        $record = Paper::with('team')->findOrFail($id);
        $record = Paper::findOrFail($id);
        $record->financial = $request->financial;
        $record->potential_benefit =  $request->potential_benefit;
        $record->non_financial = $request->non_financial;
        $record->potensi_replikasi = $request->input('potensi_replikasi');
        $financial = $request->financial;
        $potential_benefit = $request->potential_benefit;
        $potensi_replikasi = $request->input('potensi_replikasi');
        $non_financial = $request->non_financial;

        if ($request->file('file_review')) {
            $record->file_review = $request->file('file_review')->storeAs('/file_review', $record->innovation_title . "." . $request->file('file_review')->extension(), 'public');
            $record->file_review = '/' . $record->file_review;
        }

        // if($request->nonfin != null){
        //     foreach ($request->nonfin as $key => $value) {
        //         BenefitNonFin::where('id', $key)->update([
        //             'value' => $value
        //         ]);
        //     }
        // }
        $record->status = 'upload benefit';
        $record->updateAndHistory([], 'update benefit form');
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

        $idTeam = $request->team_id;

        PvtMember::updateOrCreate([
            'team_id' => $idTeam,
            'employee_id' => $request->gm_id,
            'status' => 'gm'
        ]);
        // dd($validatedData['nonfin']);

        if ($record->status = 'upload benefit') {
            // Pastikan relasi Team sudah dimuat dengan benar
            if ($record->team) {
                $fasilId = PvtMember::where('team_id', $record->team->id)
                    ->where('status', 'facilitator')
                    ->pluck('employee_id')
                    ->first();

                $fasilData = User::where('employee_id', $fasilId)
                    ->select('name', 'email')
                    ->first();

                $leaderId = PvtMember::where('team_id', $record->team->id)
                    ->where('status', 'leader')
                    ->pluck('employee_id')
                    ->first();

                $leaderData = User::where('employee_id', $leaderId)
                    ->select('name', 'email')
                    ->first();

                $inovasi_lokasi = Paper::where('id', $id)
                    ->select('inovasi_lokasi')
                    ->first();

                // Membuat objek EmailNotification
                $mail = new EmailNotificationBenefit(
                    $record,
                    $record->status,
                    $record->innovation_title,
                    $record->team->team_name,
                    $leaderData,
                    $financial,
                    $potential_benefit,
                    $potensi_replikasi,
                    $non_financial,
                    $fasilData,
                    $inovasi_lokasi
                );

                //dd($fasilData);

                // Mengirim email ke fasilitator
                Mail::to($fasilData->email)->send($mail);
            } else {
                throw new \Exception('Paper tidak memiliki relasi dengan Team.');
            }
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
}
