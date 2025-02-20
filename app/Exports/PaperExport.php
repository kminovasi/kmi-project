<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PaperExport implements FromView
{

    protected $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function view(): View
    {
        $papers = \DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->where('teams.category_id', $this->categoryId)
            ->where('events.status', 'finish')
            ->select(
                'papers.*',
                'teams.team_name',
                'teams.company_code',
                'pvt_event_teams.*',
                'events.event_name',
                'events.year',
                'themes.theme_name'
            )->get();


        return view('auth.admin.dokumentasi.evidence.export', compact('papers'));
    }
}