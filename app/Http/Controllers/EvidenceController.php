<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Event;
use App\Models\Team;
use App\Models\Theme;
use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    function index()
    {
        $categories = Category::all();

        return view('auth.admin.dokumentasi.evidence.index', compact('categories'));
    }


    function List_paper($id, Request $request)
    {

        $category = Category::find($id);

        // Filter dan Pencarian
        $search = $request->input('search');
        $companyCode = $request->input('code');
        $theme = $request->input('theme');
        $event = $request->input('event');

        // join tabel untuk list paper
        $papers = \DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->where('teams.category_id', $id)
            ->where('events.status', 'finish')
            ->select(
                'papers.*',
                'teams.team_name',
                'teams.company_code',
                'pvt_event_teams.*',
                'events.event_name',
                'events.year',
                'themes.theme_name'
            );

        // Filter berdasarkan judul paper (pencarian)
        if ($search) {
            $papers = $papers->where('papers.innovation_title', 'ILIKE', '%' . $search . '%');
        }

        // Filter berdasarkan company code
        if ($companyCode) {
            $papers = $papers->where('teams.company_code', '=', $companyCode);
        }

        // Filter berdasarkan theme
        if ($theme) {
            $papers = $papers->where('teams.theme_id', '=', $theme);
        }

        // filter berdasarkan event
        if ($event) {
            $papers = $papers->where('pvt_event_teams.event_id', '=', $event);
        }

        $papers = $papers->paginate(10);

        // ambil data themes, companies dan events untuk kebutuhan filter
        $themes = Theme::select('id', 'theme_name');
        $companies = Company::select('company_name', 'company_code')->get();
        $events = Event::where('status', 'finish')->select('id', 'event_name', 'year')->get();

        return view('auth.admin.dokumentasi.evidence.list-innovations', compact('papers', 'events', 'themes', 'category', 'companies'));
    }

    function paper_detail($id)
    {

        $team = Team::findOrFail($id);

        // join tabel untuk data detail
        $papers = \DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->join('document_supportings', 'papers.id', '=', 'document_supportings.paper_id')
            ->where('papers.team_id', $id)
            ->select(
                'papers.*',
                'pvt_event_teams.final_score',
                'pvt_event_teams.total_score_on_desk',
                'pvt_event_teams.total_score_presentation',
                'pvt_event_teams.total_score_caucus',
                'pvt_event_teams.is_best_of_the_best',
                'themes.theme_name',
                'events.event_name',
                'document_supportings.path'
            )
            ->get();

        $teamMember = $team->pvtMembers()->with('user')->get();


        return view('auth.admin.dokumentasi.evidence.detail-team', compact('teamMember', 'papers'));
    }
}
