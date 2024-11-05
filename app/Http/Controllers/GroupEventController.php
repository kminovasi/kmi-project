<?php

namespace App\Http\Controllers;

use App\Mail\EventAssignmentNotification;
use App\Models\Paper;
use App\Models\Team;
use App\Models\Event;
use App\Models\PvtEventTeam;
use Auth;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Log;

class GroupEventController extends Controller
{
    public function getAllPaper(Request $request)
    {
        $superadmin = Auth::user()->role === "Superadmin";
        $admin = Auth::user()->role === "Admin";
        $company_code = Auth::user()->company_code;

        if ($request->ajax()) {
            $validStatuses = [
                'Presentation',
                'tidak lolos Presentation',
                'Lolos Presentation',
                'Tidak lolos Caucus',
                'Presentation BOD',
                'Juara'
            ];

            $papers = Paper::with(['team' => function ($query) {
                $query->select('id', 'team_name', 'company_code');
            }, 'team.events'])
                ->join('teams', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'teams.company_code', '=', 'companies.company_code')
                ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->whereIn('pvt_event_teams.status', $validStatuses);

            // Tambahkan filter berdasarkan company_code jika user adalah Admin
            if ($admin) {
                $papers->where('teams.company_code', $company_code);
            }

            $papers = $papers->select([
                'papers.id',
                'teams.id as team_id',
                'teams.team_name',
                'papers.innovation_title',
                'papers.created_at',
                'companies.company_name'
            ])
                ->groupBy(
                    'papers.id',
                    'teams.id',
                    'teams.team_name',
                    'papers.innovation_title',
                    'papers.created_at',
                    'companies.company_name'
                )
                ->orderBy('papers.created_at', 'desc');

            return DataTables::of($papers)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" name="paper_checkbox" class="paper_checkbox" value="' . $row->id . '">';
                })
                ->addColumn('registered_events', function ($row) {
                    $eventsList = $row->team->events->map(function ($event) {
                        $statusClass = $this->getStatusClass($event->pivot->status);
                        return sprintf(
                            '<span class="badge %s">%s (%s)</span>',
                            $statusClass,
                            $event->event_name,
                            $event->pivot->status
                        );
                    })->implode(' ');

                    return $eventsList ?: '<span class="badge bg-secondary">No events</span>';
                })
                ->rawColumns(['checkbox', 'registered_events'])
                ->make(true);
        }
    }
    private function getStatusClass($status)
    {
        switch ($status) {
            case 'Presentation':
                return 'bg-primary';
            case 'tidak lolos Presentation':
                return 'bg-danger';
            case 'Lolos Presentation':
                return 'bg-success';
            case 'Tidak lolos Caucus':
                return 'bg-danger';
            case 'Caucus':
                return 'bg-info';
            case 'Presentation BOD':
                return 'bg-warning';
            case 'Juara':
                return 'bg-success';
            default:
                return 'bg-secondary';
        }
    }
    public function assignTeamsToEvent(Request $request)
    {
        try {
            $paperIds = $request->team_ids;
            $eventId = $request->event_id;
            $event = Event::findOrFail($eventId);

            foreach ($paperIds as $paperId) {
                $paper = Paper::with(['team.pvtMembers.user'])->find($paperId);

                if (!$paper) {
                    continue;
                }

                $teamId = $paper->team_id;

                $existingEntry = PvtEventTeam::where('team_id', $teamId)
                    ->where('event_id', $eventId)
                    ->first();

                if (!$existingEntry) {
                    PvtEventTeam::create([
                        'team_id' => $teamId,
                        'event_id' => $eventId,
                    ]);

                    // Kirim email ke semua anggota tim
                    foreach ($paper->team->pvtMembers as $member) {
                        // Pastikan user memiliki email
                        if ($member->user && $member->user->email) {
                            try {
                                FacadesMail::to($member->user->email)->send(
                                    new EventAssignmentNotification(
                                        $paper->team->team_name,
                                        $event->event_name,
                                        $member->user->name
                                    )
                                );
                            } catch (\Exception $e) {
                                Log::error('Failed to send email to ' . $member->user->email . ': ' . $e->getMessage());
                            }
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Teams successfully assigned to event and notifications sent'
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning teams to event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
