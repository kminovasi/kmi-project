<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Event;
use App\Models\Paper;
use App\Models\Company;
use App\Models\History;
use App\Models\PvtMember;
use App\Models\PvtEventTeam;
use Illuminate\Http\Request;
use App\Mail\PaperStatusUpdated;
use App\Models\PvtCustomBenefit;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\CustomBenefitFinancial;
use Illuminate\Support\Facades\Storage;
use App\Notifications\PaperNotification;
use App\Services\PaperFileUploadService;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\UpdatePaperRequest;

class EventTeamController extends Controller
{
    public function index()
    {
        $companies = Company::all(); // Ambil semua perusahaan
        return view('event-team.index', compact('companies'));
    }

    public function getEvents(Request $request)
    {
        $user = Auth::user();
    
        $query = Event::with('companies');
    
        if ($user->role !== 'Superadmin') {
            if ($user->role === 'Admin') {
                if (in_array($user->company_code, [2000, 7000])) {
                    $filteredCompanyCode = [2000, 7000];
                } else {
                    $filteredCompanyCode = [$user->company_code];
                }
                $query->whereHas('companies', function ($q) use ($filteredCompanyCode) {
                    $q->whereIn('company_code', $filteredCompanyCode);
                });
            } else {
                $teams = PvtMember::where('employee_id', $user->employee_id)
                    ->pluck('team_id');
    
                $eventIds = PvtEventTeam::whereIn('team_id', $teams)
                    ->pluck('event_id')
                    ->unique();
    
                if ($eventIds->isNotEmpty()) {
                    $query->whereIn('id', $eventIds);
                } else {
                    return DataTables::of([])->toJson();
                }
            }
        }
    
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
    
        if ($request->has('company_code') && $request->company_code != '') {
            $query->whereHas('companies', function ($q) use ($request) {
                $q->where('company_code', $request->company_code);
            });
        }
    
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
    
        $query->orderByRaw("CASE WHEN status = 'active' THEN 1 ELSE 2 END")
            ->orderBy('date_start', 'desc');
    
        return DataTables::of($query)
            ->addColumn('company', function ($event) {
                return $event->companies->pluck('company_name')->implode(', ') ?: 'N/A';
            })
            ->editColumn('event_name', function ($event) {
                $year = \Carbon\Carbon::parse($event->date_start)->format('Y');
                return $event->event_name . ' Tahun ' . $year;
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
        $role = $currentUser->role;
    
        $data = PvtEventTeam::with(['team.paper', 'team.company', 'team.pvtMembers.user', 'event'])
            ->where('event_id', $id)
            ->get()
            ->map(function ($item) use ($currentUser, $id, $role) {
                $userMembership = $item->team->pvtMembers
                    ->where('employee_id', $currentUser->employee_id)
                    ->first();
    
                $userRole = null;
                $isUserTeamMember = false;
    
                if ($role === 'Superadmin' || $role === 'Admin') {
                    $isUserTeamMember = true;
                } else if ($userMembership) {
                    $isUserTeamMember = true;
                    $userRole = $userMembership->status;
                }
    
                $hasFullPaper = false;
                if (isset($item->team->paper)) {
                    $hasFullPaper = !empty($item->team->paper->full_paper);
                }
    
                return [
                    'team_id' => $item->team_id,
                    'team_name' => $item->team->team_name,
                    'innovation_title' => $item->team->paper->innovation_title ?? '-',
                    'company_name' => $item->team->company->company_name ?? '-',
                    'status_inovasi' => $item->team->paper->status === 'accepted by innovation admin',
                    'event_type' => $item->event->type,
                    'has_full_paper' => $hasFullPaper,
                    'view_url' => route('event-team.show', $item->team_id),
                    'edit_url' => route('event-team.paper.edit', [
                        'id' => $item->team->paper->id ?? 0,
                        'eventId' => $id
                    ]),
                    'edit_benefit_url' => route('event-team.benefit.edit', [
                        'id' => $item->team->paper->id ?? 0,
                        'eventId' => $id
                    ]),
                    'check_paper' => route('event-team.showCheckPaper', [
                        'id' => $item->team->paper->id ?? 0,
                        'eventId' => $id
                    ]),
                    'is_user_team' => $isUserTeamMember,
                    'user_role' => $userRole,
                    'role' => $role,
                    'has_paper' => isset($item->team->paper),
                    'event_status' => $item->event->status ?? '-'
                ];
            })
            ->unique('team_id')
            ->values();
    
        // FILTERING khusus untuk non-admin
        $data = $data->filter(function ($item) use ($role) {
            if ($role === 'Superadmin' || $role === 'Admin') {
                return true; // admin tetap lihat semua
            }
    
            return $item['is_user_team'] === true;
        })->values(); // reset index setelah filter
    
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
            return redirect()->back()->with('success', 'Makalah Inovasi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating paper: ' . $e->getMessage());
            return back()->with('error', 'Terjadi error ketika memperbarui makalah: ' . $e->getMessage());
        }
    }
    
    public function editBenefit($id, $eventId)
    {
        // Ambil paper beserta relasi team dan company
        $paper = Paper::with(['team.company'])->findOrFail($id);

        // Ambil company_code dari tabel companies melalui relasi
        $companyCode = $paper->team->company->company_code;

        // Ambil semua custom benefits
        $customBenefits = CustomBenefitFinancial::all();

        // Filter custom benefits berdasarkan company code
        $filteredCustomBenefits = $customBenefits->filter(function ($benefit) use ($companyCode) {
            return $benefit->papers->some(function ($pvtCustomBenefit) use ($companyCode) {
                return $pvtCustomBenefit->paper->team->company->company_code === $companyCode;
            });
        });

        // Ambil nilai custom benefit yang sudah ada
        $existingCustomBenefits = PvtCustomBenefit::where('paper_id', $paper->id)->pluck('value', 'custom_benefit_financial_id');

        return view('event-team.edit-benefit', compact('paper', 'filteredCustomBenefits', 'existingCustomBenefits', 'eventId'));
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
            ->with('success', 'Benefit Inovasi Telah Berhasil diperbarui');
    }

    public function showCheckPaper($id, $eventId)
    {
        $paper = Paper::with([
            'team',
            'team.pvtMembers.user' // Ambil data anggota tim beserta user terkait
        ])->findOrFail($id);
        $eventType = Event::findOrFail($eventId)->type;

        // Pisahkan anggota berdasarkan status
        $facilitator = $paper->team->pvtMembers->firstWhere('status', 'facilitator');
        $leader = $paper->team->pvtMembers->firstWhere('status', 'leader');
        $members = $paper->team->pvtMembers->whereNotIn('status', ['facilitator', 'leader']);

        return view('event-team.check-paper', compact('paper', 'eventId', 'facilitator', 'leader', 'members', 'eventType'));
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

        return redirect()->back()->with('success', 'Status Makalah Inovasi Berhasil Diperbarui');
    }
}