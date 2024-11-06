<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaperRequest;
use App\Models\Company;
use App\Models\CustomBenefitFinancial;
use App\Models\Event;
use App\Models\History;
use App\Models\Paper;
use App\Models\PvtCustomBenefit;
use App\Models\PvtEventTeam;
use App\Models\Team;
use App\Notifications\PaperNotification;
use App\Services\PaperFileUploadService;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Log;

class EventTeamController extends Controller
{
    public function index()
    {
        $companies = Company::all(); // Ambil semua perusahaan
        return view('event-team.index', compact('companies'));
    }

    public function getEvents(Request $request)
    {
        $query = Event::with('company');

        // Filter berdasarkan type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter berdasarkan perusahaan
        if ($request->has('company_code') && $request->company_code != '') {
            $query->where('company_code', $request->company_code);
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Urutkan berdasarkan status (active first) dan tanggal mulai terbaru
        $query->orderByRaw("CASE WHEN status = 'active' THEN 1 ELSE 2 END")
            ->orderBy('date_start', 'desc');

        return DataTables::of($query)
            ->addColumn('company', function ($event) {
                return $event->company ? $event->company->company_name : 'N/A';
            })
            ->toJson();
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('event-team.show', compact('event'));
    }


    public function buildPaperQueryByEvent($id)
    {
        $currentUser = auth()->user();

        $data = PvtEventTeam::with(['team.paper', 'team.company', 'team.pvtMembers.user'])
            ->where('event_id', $id)
            ->get()
            ->map(function ($item) use ($currentUser, $id) {
                $userMembership = $item->team->pvtMembers
                    ->where('employee_id', $currentUser->employee_id)
                    ->first();

                $userRole = null;
                $isUserTeamMember = false;
                if ($userMembership) {
                    $isUserTeamMember = true;
                    $userRole = $userMembership->status;
                }

                return [
                    'team_name' => $item->team->team_name,
                    'innovation_title' => $item->team->paper->innovation_title ?? '-',
                    'company_name' => $item->team->company->company_name ?? '-',
                    'view_url' => route('event-team.show', $item->team_id),
                    'edit_url' => route('event-team.paper.edit', ['id' => $item->team->paper->id ?? 0, 'eventId' => $id]),
                    'edit_benefit_url' => route('event-team.benefit.edit', ['id' => $item->team->paper->id ?? 0, 'eventId' => $id]), // Tambahkan route untuk edit benefit
                    'is_user_team' => $isUserTeamMember,
                    'user_role' => $userRole,
                    'has_paper' => isset($item->team->paper)
                ];
            });

        return response()->json(['data' => $data]);
    }

    public function editPaper($id, $eventId)
    {
        $paper = Paper::findOrFail($id);
        return view('event-team.edit-paper', compact('paper', 'eventId'));
    }

    public function updatePaper(UpdatePaperRequest $request, $id, $eventId, PaperFileUploadService $fileUploadService)
    {
        try {
            DB::beginTransaction();

            $paper = Paper::findOrFail($id);
            $team = Team::findOrFail($paper->team_id);
            $validatedData = $request->validated();

            $fileFields = ['full_paper', 'innovation_photo', 'proof_idea'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $validatedData[$field] = $fileUploadService->uploadPaperFile(
                        $request->file($field),
                        $field,
                        $team->status_lomba,
                        $team->team_name,
                        $paper->$field // Pass the old file path
                    );
                }
            }

            $paper->update($validatedData);

            // Create history record
            History::create([
                'team_id' => $paper->team_id,
                'activity' => "Paper updated: " . $paper->innovation_title,
                'status' => 'updated'
            ]);

            // Send notification
            $user = Auth::user();
            $user->notify(new PaperNotification(
                'Paper Updated',
                'Paper ' . $paper->innovation_title . ' has been updated.',
                route('event-team.show', $paper->team_id)
            ));

            DB::commit();

            return redirect()->route('event-team.editPaper', ['id' => $paper->id, 'eventId', $eventId])
                ->with('success', 'Paper updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating paper: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while updating the paper: ' . $e->getMessage());
        }
    }
    public function editBenefit($id, $eventId)
    {
        $paper = Paper::with(['team.company'])->findOrFail($id);

        // Ambil custom benefit yang tersedia untuk perusahaan ini
        $customBenefits = CustomBenefitFinancial::where('company_code', $paper->team->company->company_code)->get();

        // Ambil nilai custom benefit yang sudah ada
        $existingCustomBenefits = PvtCustomBenefit::where('paper_id', $paper->id)->pluck('value', 'custom_benefit_financial_id');

        return view('event-team.edit-benefit', compact('paper', 'customBenefits', 'existingCustomBenefits', 'eventId'));
    }

    public function updateBenefit(Request $request, $id, $eventId)
    {
        $paper = Paper::findOrFail($id);

        // Bersihkan format angka
        $financial = str_replace('.', '', $request->financial);
        $potential_benefit = str_replace('.', '', $request->potential_benefit);

        $customBenefits = [];
        if ($request->has('custom_benefit')) {
            foreach ($request->custom_benefit as $key => $value) {
                $customBenefits[$key] = str_replace('.', '', $value);
            }
        }

        // Validasi dengan data yang sudah dibersihkan
        $validatedData = $request->validate([
            'financial' => 'required',
            'potential_benefit' => 'required',
            'non_financial' => 'required|string',
            'custom_benefit.*' => 'nullable',
        ]);

        // Update benefit utama dengan nilai yang sudah dibersihkan
        $paper->update([
            'financial' => $financial,
            'potential_benefit' => $potential_benefit,
            'non_financial' => $validatedData['non_financial'],
        ]);

        // Update custom benefits
        if (!empty($customBenefits)) {
            foreach ($customBenefits as $benefitId => $value) {
                if (!empty($value)) {
                    PvtCustomBenefit::updateOrCreate(
                        [
                            'custom_benefit_financial_id' => $benefitId,
                            'paper_id' => $paper->id
                        ],
                        ['value' => $value]
                    );
                }
            }
        }

        return redirect()->route('event-team.benefit.edit', ['id' => $id, 'eventId' => $eventId])
            ->with('success', 'Benefit information updated successfully.');
    }
}
