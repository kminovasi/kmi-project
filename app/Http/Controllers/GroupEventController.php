<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Team;
use App\Models\Event;
use App\Models\Paper;
use App\Models\History;
use App\Models\PvtEventTeam;
use App\Models\PvtAssessmentEvent;
use App\Models\pvtAssesmentTeamJudge;
use Illuminate\Http\Request;
use App\Mail\EventAssignmentNotification;
use Illuminate\Support\Facades\{Auth, Mail, Log};

class GroupEventController extends Controller
{
    private const VALID_STATUSES = [
        'Presentation',
        'Tidak lolos Caucus',
        'Caucus',
        'Presentation BOD',
        'Juara'
    ];

    private const STATUS_CLASSES = [
        'AP' => [
            'Presentation' => 'bg-secondary bg-opacity-25 text-secondary',
            'tidak lolos Presentation' => 'bg-secondary bg-opacity-25 text-secondary',
            'Lolos Presentation' => 'bg-secondary bg-opacity-25 text-secondary',
            'Tidak lolos Caucus' => 'bg-secondary bg-opacity-25 text-secondary',
            'Caucus' => 'bg-secondary bg-opacity-25 text-secondary',
            'Presentation BOD' => 'bg-secondary bg-opacity-25 text-secondary',
            'Juara' => 'bg-secondary bg-opacity-25 text-secondary',
            'tidak lolos On Desk' => 'bg-secondary bg-opacity-25 text-secondary',
            'On Desk' => 'bg-secondary bg-opacity-25 text-secondary',
        ],
        'internal' => [
            'Presentation' => 'bg-secondary bg-opacity-25 text-secondary',
            'tidak lolos Presentation' => 'bg-secondary bg-opacity-25 text-secondary',
            'Lolos Presentation' => 'bg-secondary bg-opacity-25 text-secondary',
            'Tidak lolos Caucus' => 'bg-secondary bg-opacity-25 text-secondary',
            'Caucus' => 'bg-secondary bg-opacity-25 text-secondary',
            'Presentation BOD' => 'bg-secondary bg-opacity-25 text-secondary',
            'Juara' => 'bg-secondary bg-opacity-25 text-secondary',
            'tidak lolos On Desk' => 'bg-secondary bg-opacity-25 text-secondary',
            'On Desk' => 'bg-secondary bg-opacity-25 text-secondary',
        ],
        'group' => [
            'Presentation' => 'bg-success bg-opacity-25 text-success',
            'tidak lolos Presentation' => 'bg-success bg-opacity-25 text-success',
            'Lolos Presentation' => 'bg-success bg-opacity-25 text-success',
            'Tidak lolos Caucus' => 'bg-success bg-opacity-25 text-success',
            'Caucus' => 'bg-success bg-opacity-25 text-success',
            'Presentation BOD' => 'bg-success bg-opacity-25 text-success',
            'Juara' => 'bg-success bg-opacity-25 text-success',
            'tidak lolos On Desk' => 'bg-success bg-opacity-25 text-success',
            'On Desk' => 'bg-success bg-opacity-25 text-success',
        ],
    ];


    public function getAllPaper(Request $request)
    {
        $superadmin = Auth::user()->role === 'Superadmin';
        if (!$request->ajax()) {
            return;
        }

        $query = $this->buildPaperQuery();

        // Filter tahun jika ada
        if ($request->has('year') && $request->year) {
            $query->whereYear('events.year', $request->year);
        }

        // Filter perusahaan untuk superadmin
        if ($superadmin && $request->has('company') && $request->company) {
            $query->where('companies.company_code', $request->company);
        }

        return $this->generateDataTable($query);
    }

    private function buildPaperQuery()
    {
        $query = Paper::with([
            'team' => function ($query) {
                $query->select('id', 'team_name', 'company_code');
            },
            'team.events' => function ($query) {
                $query->where('events.status', 'finish');
            },
            'team.apEvents' => function ($query) {
                $query->where('type', 'AP');
            },
            'team.internalEvents' => function ($query) {
                $query->where('type', 'internal');
            }
        ])
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('companies', 'teams.company_code', '=', 'companies.company_code')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id') // ✅ perlu join ini
            ->where('events.status', 'finish') // ✅ filter status event
            ->whereIn('pvt_event_teams.status', self::VALID_STATUSES);

        if (Auth::user()->role === 'Admin') {
            $query->where('teams.company_code', Auth::user()->company_code);
        }

        return $query->select([
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
    }

    private function generateDataTable($query)
    {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn(
                'checkbox',
                fn($row) =>
                '<input type="checkbox" name="paper_checkbox" class="paper_checkbox" value="' . $row->id . '">'
            )
            ->addColumn('ap_events', function ($row) {
                // Ambil event internal (group)
                $apEvents = $row->team->events()->where('type', 'AP')->get()->map(function ($event) {
                    $statusClass = self::STATUS_CLASSES['internal'][$event->pivot->status] ?? 'bg-secondary';
                    return sprintf(
                        '<span class="badge %s">%s (%s)</span>',
                        $statusClass,
                        $event->event_name . ' Tahun ' . $event->year,
                        $event->pivot->status
                    );
                })->implode(' ');

                return $apEvents ?: '<span class="badge bg-primary">Team Tidak Mengikuti Event AP</span>';
            })
            ->addColumn('internal_events', function ($row) {
                // Ambil event internal (group)
                $internalEvents = $row->team->events()->where('type', 'internal')->get()->map(function ($event) {
                    $statusClass = self::STATUS_CLASSES['internal'][$event->pivot->status] ?? 'bg-secondary';
                    return sprintf(
                        '<span class="badge %s">%s (%s)</span>',
                        $statusClass,
                        $event->event_name . ' Tahun ' . $event->year,
                        $event->pivot->status
                    );
                })->implode(' ');

                return $internalEvents ?: '<span class="badge bg-primary">Team Tidak Mengikuti Event Internal</span>';
            })
            ->addColumn('group_events', function ($row) {
                $groupEvents = $row->team->events()
                    ->where('type', 'group') // Asumsi ada kolom type di tabel events
                    ->get()
                    ->map(function ($event) {
                        $statusClass = self::STATUS_CLASSES['group'][$event->pivot->status] ?? 'bg-info';
                        return sprintf(
                            '<span class="badge %s">%s (%s)</span>',
                            $statusClass,
                            $event->event_name . ' Tahun ' . $event->year,
                            $event->pivot->status
                        );
                    })->implode(' ');

                return $groupEvents ?: '<span class="badge bg-danger">Team Tidak Mengikuti Event Group</span>';
            })
            ->rawColumns(['checkbox', 'internal_events', 'group_events', 'ap_events'])
            ->make(true);
    }
    
    public function assignTeamsToEvent(Request $request)
    {
        try {
            $event = Event::findOrFail($request->event_id);
            $papers = Paper::with(['team.pvtMembers.user'])
                ->whereIn('id', $request->team_ids)
                ->get();

            foreach ($papers as $paper) {
                $idEventTeam = $this->processTeamAssignment($paper, $event);
                
                $category = PvtEventTeam::join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                    ->join('categories', 'categories.id', '=', 'teams.category_id')
                    ->where('pvt_event_teams.id', $idEventTeam)
                    ->pluck('category_parent')
                    ->first();
                if ($category == 'IDEA BOX'){
                    $category = 'IDEA';
                } else {
                    $category = 'BI/II';
                }
            
                $data_assessment_event = PvtAssessmentEvent::where('event_id', $event->id)
                    ->where('category', $category)
                    ->where('status_point', 'active')
                    ->where('stage', 'on desk')
                    ->pluck('id')
                    ->toArray();
            
                foreach ($data_assessment_event as $assessmentEventId) {
                    pvtAssesmentTeamJudge::updateOrCreate([
                        'event_team_id' => $idEventTeam,
                        'assessment_event_id' => $assessmentEventId,
                        'stage' => 'on desk'
                    ]);
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

    private function processTeamAssignment($paper, $event)
    {
        $pap = Paper::findOrFail($paper->id);
    
        // Cek apakah sudah ada
        $existingEntry = PvtEventTeam::where([
            'team_id' => $paper->team_id,
            'event_id' => $event->id
        ])->first();
    
        if ($existingEntry) {
            return $existingEntry->id;
        }
    
        // Update status paper dan team
        $pap->update([
            'status_event' => 'accept_group'
        ]);
    
        $eventTeam = PvtEventTeam::create([
            'team_id' => $paper->team_id,
            'event_id' => $event->id,
        ]);
    
        $updateTeam = Team::findOrFail($paper->team_id);
        $updateTeam->update([
            'status_lomba' => 'group'
        ]);
    
        History::create([
            'team_id' => $paper->team_id,
            'activity' => "Accepted to Event Group",
            'status' => 'Accepted'
        ]);
    
        return $eventTeam->id;
    }

    private function sendNotifications($team, $event)
    {
        foreach ($team->pvtMembers as $member) {
            if (!$member->user?->email) {
                continue;
            }

            try {
                Mail::to($member->user->email)->send(
                    new EventAssignmentNotification(
                        $team->team_name,
                        $event->event_name,
                        $member->user->name
                    )
                );
            } catch (\Exception $e) {
                Log::error("Failed to send email to {$member->user->email}: {$e->getMessage()}");
            }
        }
    }
}