<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaperRequest;
use App\Mail\PaperStatusUpdated;
use App\Models\Company;
use App\Models\CustomBenefitFinancial;
use App\Models\Event;
use App\Models\History;
use App\Models\Paper;
use App\Models\PvtCustomBenefit;
use App\Models\PvtEventTeam;
use App\Models\PvtMember;
use App\Models\Team;
use App\Notifications\PaperNotification;
use App\Services\PaperFileUploadService;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
        // Ambil user yang sedang login
        $user = Auth::user();

        // Buat query untuk mengambil event
        $query = Event::with('company'); // Ambil semua event dengan relasi company

        // Jika user bukan Superadmin, ambil tim yang diikuti oleh user
        if ($user->role !== 'Superadmin') {
            // Ambil tim yang diikuti oleh user
            $teams = PvtMember::where('employee_id', $user->employee_id)
                ->pluck('team_id');

            // Ambil event yang diikuti oleh tim
            $eventIds = PvtEventTeam::whereIn('team_id', $teams)
                ->pluck('event_id')
                ->unique();

            // Filter berdasarkan event yang diikuti oleh tim
            if ($eventIds->isNotEmpty()) {
                $query->whereIn('id', $eventIds);
            } else {
                // Jika user tidak mengikuti event, kembalikan data kosong
                return DataTables::of([])->toJson();
            }
        }

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
        $superadmin = Auth::user()->role === 'Superadmin';
        return view('event-team.show', compact('event', 'superadmin'));
    }


    public function buildPaperQueryByEvent($id)
    {
        $currentUser = auth()->user();
        $role = Auth::user()->role;

        $data = PvtEventTeam::with(['team.paper', 'team.company', 'team.pvtMembers.user'])
            ->where('event_id', $id)
            ->get()
            ->map(function ($item) use ($currentUser, $id, $role) {
                $userMembership = $item->team->pvtMembers
                    ->where('employee_id', $currentUser->employee_id)
                    ->first();

                $userRole = null;
                $isUserTeamMember = false;
                if ($userMembership) {
                    $isUserTeamMember = true;
                    $userRole = $userMembership->status;
                }

                // Check if full paper exists and is uploaded
                $hasFullPaper = false;
                if (isset($item->team->paper)) {
                    $fullPaperPath = $item->team->paper->full_paper;
                    // Check if the full_paper path contains '/group/' instead of '/AP/'
                    $hasFullPaper = !empty($fullPaperPath) && strpos($fullPaperPath, '/group/') !== false;
                }

                return [
                    'team_name' => $item->team->team_name,
                    'innovation_title' => $item->team->paper->innovation_title ?? '-',
                    'company_name' => $item->team->company->company_name ?? '-',
                    'status_lolos' => $item->team->paper->status_event === 'accept_group',
                    'has_full_paper' => $hasFullPaper,
                    'view_url' => route('event-team.show', $item->team_id),
                    'edit_url' => route('event-team.paper.edit', ['id' => $item->team->paper->id ?? 0, 'eventId' => $id]),
                    'edit_benefit_url' => route('event-team.benefit.edit', ['id' => $item->team->paper->id ?? 0, 'eventId' => $id]),
                    'check_paper' => route('event-team.showCheckPaper', ['id' => $item->team->paper->id ?? 0, 'eventId' => $id]),
                    'is_user_team' => $isUserTeamMember,
                    'user_role' => $userRole,
                    'role' => $role,
                    'has_paper' => isset($item->team->paper),
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
            Log::info('Redirecting back with success message.');
            return redirect()->back()->with('success', 'Paper updated successfully');
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

    public function showCheckPaper($id, $eventId)
    {
        $paper = Paper::with('team')->findOrFail($id);
        return view('event-team.check-paper', compact('paper', 'eventId'));
    }
    public function updatePaperStatus(Request $request, $id, $eventId)
    {
        $paper = Paper::findOrFail($id);
        $paper->status_event = $request->status_event;

        // Jika status reject, comments harus ada
        if (str_contains($request->status_event, 'reject')) {
            $request->validate([
                'comments' => 'required|string'
            ]);
            $paper->rejection_comments = $request->comments;

            // Handle perubahan path full paper
            if ($paper->full_paper && strpos($paper->full_paper, 'internal/group/') !== false) {
                try {
                    // Ambil nama team dari path lama
                    $teamName = explode('/', $paper->full_paper)[2];

                    // Path lama dan baru
                    $oldPath = $paper->full_paper;
                    $newPath = str_replace('internal/group/', 'internal/AP/', $oldPath);

                    // Cek apakah file lama exists
                    if (Storage::exists($oldPath)) {
                        // Copy file ke path baru
                        Storage::copy($oldPath, $newPath);

                        // Hapus file lama
                        Storage::delete($oldPath);

                        // Update path di database
                        $paper->full_paper = $newPath;

                        Log::info('File berhasil dipindahkan dari ' . $oldPath . ' ke ' . $newPath);
                    } else {
                        Log::warning('File tidak ditemukan di path: ' . $oldPath);
                    }
                } catch (\Exception $e) {
                    Log::error('Error saat memindahkan file: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses file');
                }
            }
        }

        $paper->save();

        // Ambil semua email anggota team menggunakan join
        // Hanya mengambil anggota dengan status 'leader' atau 'member'
        $teamMembers = DB::table('users')
            ->join('pvt_members', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->where('pvt_members.team_id', $paper->team_id)
            ->whereIn('pvt_members.status', ['leader', 'member'])
            ->select('users.email', 'users.name')
            ->get();

        // Kirim email ke semua anggota team yang memenuhi kriteria
        foreach ($teamMembers as $member) {
            Mail::to($member->email)->send(new PaperStatusUpdated($paper, $member));
        }

        return redirect()->back()->with('success', 'Paper status has been updated successfully');
    }
}
