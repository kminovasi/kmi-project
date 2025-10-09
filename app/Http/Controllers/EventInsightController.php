<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventInsightController extends Controller
{
    public function categoryInnovators(Request $request, Event $event)
    {
        $category = $request->query('category');
        if (!$category) {
            return response()->json(['message' => 'category is required'], 422);
        }

        $teamTitleExpr = "COALESCE(teams.team_name, CONCAT('Team #', teams.id))";

        $inhouse = DB::table('pvt_members')
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->leftJoin('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->where('pvt_event_teams.event_id', $event->id)
            ->where('categories.category_name', $category)
            ->where('pvt_members.status', '!=', 'gm')
            ->whereExists(function ($sub) {
                $sub->from('papers')
                    ->whereColumn('papers.team_id', 'teams.id')
                    ->whereRaw("TRIM(LOWER(papers.status)) = 'accepted by innovation admin'");
            })
            ->selectRaw("
                COALESCE(users.employee_id, pvt_members.employee_id) AS person_key,
                COALESCE(users.name, '(nama tidak tersedia)')        AS user_name,
                COALESCE(users.email, '(email tidak tersedia)')      AS email,
                $teamTitleExpr                                       AS team_name,
                teams.id                                             AS team_id,
                CONCAT(COALESCE(users.employee_id, pvt_members.employee_id), '-', teams.id) AS uniq_key,
                'internal'                                           AS src
            ");

        $outsource = DB::table('ph2_members')
            ->join('teams', 'teams.id', '=', 'ph2_members.team_id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->where('pvt_event_teams.event_id', $event->id)
            ->where('categories.category_name', $category)
            ->whereExists(function ($sub) {
                $sub->from('papers')
                    ->whereColumn('papers.team_id', 'teams.id')
                    ->whereRaw("TRIM(LOWER(papers.status)) = 'accepted by innovation admin'");
            })
            ->selectRaw("
                CONCAT('OUT-', ph2_members.name)     AS person_key,
                ph2_members.name                     AS user_name,
                '-'                                  AS email,
                $teamTitleExpr                        AS team_name,
                teams.id                              AS team_id,
                CONCAT('OUT-', ph2_members.name, '-', teams.id) AS uniq_key,
                'outsource'                           AS src
            ");

        $rows = DB::query()
            ->fromSub($inhouse->unionAll($outsource), 'u')
            ->selectRaw("
                MIN(u.person_key) AS person_key,
                MIN(u.user_name)  AS user_name,
                MIN(u.email)      AS email,
                MIN(u.team_name)  AS team_name,
                MIN(u.team_id)    AS team_id,
                u.uniq_key        AS uniq_key,
                MIN(u.src)        AS src
            ")
            ->groupBy('u.uniq_key')
            ->orderByRaw('MIN(u.team_name) ASC, MIN(u.user_name) ASC')
            ->get();

        $internalCount  = $rows->where('src', 'internal')->count();
        $outsourceCount = $rows->where('src', 'outsource')->count();
        $total          = $rows->count();

        return response()->json([
            'event_id'         => $event->id,
            'event_name'       => $event->event_name,
            'category'         => $category,
            'total_innovators' => $total,
            'internal_count'   => $internalCount,
            'outsource_count'  => $outsourceCount,
            'innovators'       => $rows,
        ]);
    }
}
