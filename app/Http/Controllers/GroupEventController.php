<?php

namespace App\Http\Controllers;

use App\Mail\EventAssignmentNotification;
use App\Models\Paper;
use App\Models\Event;
use App\Models\PvtEventTeam;
use App\Models\Team;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\{Auth, Mail, Log};

class GroupEventController extends Controller
{
    private const VALID_STATUSES = [
        'Presentation',
        'tidak lolos Presentation',
        'Lolos Presentation',
        'Tidak lolos Caucus',
        'Presentation BOD',
        'Juara'
    ];

    private const STATUS_CLASSES = [
        'Presentation' => 'bg-primary',
        'tidak lolos Presentation' => 'bg-danger',
        'Lolos Presentation' => 'bg-success',
        'Tidak lolos Caucus' => 'bg-danger',
        'Caucus' => 'bg-info',
        'Presentation BOD' => 'bg-warning',
        'Juara' => 'bg-success'
    ];

    public function getAllPaper(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $query = $this->buildPaperQuery();
        return $this->generateDataTable($query);
    }

    private function buildPaperQuery()
    {
        $query = Paper::with(['team' => function ($query) {
            $query->select('id', 'team_name', 'company_code');
        }, 'team.events'])
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('companies', 'teams.company_code', '=', 'companies.company_code')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
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
            ->addColumn('registered_events', function ($row) {
                $events = $row->team->events->map(function ($event) {
                    $statusClass = self::STATUS_CLASSES[$event->pivot->status] ?? 'bg-secondary';
                    return sprintf(
                        '<span class="badge %s">%s (%s)</span>',
                        $statusClass,
                        $event->event_name,
                        $event->pivot->status
                    );
                })->implode(' ');

                return $events ?: '<span class="badge bg-secondary">No events</span>';
            })
            ->rawColumns(['checkbox', 'registered_events'])
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
                $this->processTeamAssignment($paper, $event);
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
        $existingEntry = PvtEventTeam::where([
            'team_id' => $paper->team_id,
            'event_id' => $event->id
        ])->exists();

        if ($existingEntry) {
            return;
        }
        $pap->update([
            'status_event' => 'reject_group'
        ]);

        PvtEventTeam::create([
            'team_id' => $paper->team_id,
            'event_id' => $event->id,
        ]);

        $updateTeam = Team::findOrFail($paper->team_id);
        $updateTeam->update([
            'status_lomba' => 'group'
        ]);

        $this->sendNotifications($paper->team, $event);
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
