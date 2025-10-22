<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        $userLogin      = Auth::user();
        $isSuperadmin   = $userLogin->role === 'Superadmin';
        $unitLogin      = strtoupper(trim((string) ($userLogin->unit_name ?? '')));
        $kodePerusahaan = trim((string) ($userLogin->company_code ?? ''));
        $perPage        = (int) $request->get('per_page', 25);
        $kataKunci      = trim((string) $request->get('q', ''));

        $basisKaryawan = DB::table('users as u')
            ->select('u.employee_id', 'u.name', 'u.unit_name', 'u.company_code')
            ->when(!$isSuperadmin, function ($w) use ($unitLogin, $kodePerusahaan, $userLogin) {
                $w->where(function($q) use ($unitLogin, $kodePerusahaan) {
                    $q->whereNotNull('u.unit_name')
                    ->whereRaw("TRIM(u.unit_name) <> ''")
                    ->whereRaw('UPPER(TRIM(u.unit_name)) = ?', [$unitLogin]);

                    if ($kodePerusahaan !== '') {
                        $q->where('u.company_code', $kodePerusahaan);
                    }
                })
                ->orWhere('u.employee_id', $userLogin->employee_id);
            })
            ->when($kataKunci !== '', function($q) use ($kataKunci) {
                $q->where(function($qq) use ($kataKunci){
                    $qq->where('u.name', 'like', "%{$kataKunci}%")
                    ->orWhere('u.employee_id', 'like', "%{$kataKunci}%");
                });
            });

        $subInovasi = DB::table('pvt_members as pm')
            ->join('papers as p','p.team_id','=','pm.team_id')
            ->groupBy('pm.employee_id')
            ->select('pm.employee_id', DB::raw('COUNT(DISTINCT p.id) AS inovasi_total'));

        $subPaten = DB::table('patent as pt')
            ->groupBy('pt.person_in_charge')
            ->select('pt.person_in_charge', DB::raw('COUNT(DISTINCT pt.id) AS paten_total'));

        $subReplikasi = DB::table('replication_requests as r')
            ->groupBy('r.pic_name')
            ->select('r.pic_name', DB::raw('COUNT(DISTINCT r.paper_id) AS replikasi_total'));

        $ringkasan = DB::query()->fromSub($basisKaryawan, 'k')
            ->leftJoinSub($subInovasi,  'inv', 'inv.employee_id',     '=', 'k.employee_id')
            ->leftJoinSub($subPaten,    'pt',  'pt.person_in_charge', '=', 'k.employee_id')
            ->leftJoinSub($subReplikasi,'rep', 'rep.pic_name',         '=', 'k.employee_id')
            ->selectRaw("
                k.employee_id,
                k.name,
                COALESCE(inv.inovasi_total, 0)   AS inovasi_total,
                COALESCE(pt.paten_total, 0)      AS paten_total,
                COALESCE(rep.replikasi_total, 0) AS replikasi_total
            ")
            ->orderBy('k.name');

        $data = $ringkasan->paginate($perPage)->appends($request->query());

        if ($request->ajax()) {
            return view('prestasi.index', [
                'data'      => $data,
                'userLogin' => $userLogin,
                'onlyTable' => true,
            ])->render();
        }

        return view('prestasi.index', [
            'data'      => $data,
            'userLogin' => $userLogin,
        ]);
    }

    public function listByEmployee(Request $request, string $employeeId, string $tipe)
    {
        $userLogin      = Auth::user();
        $unitLogin      = strtoupper(trim((string) ($userLogin->unit_name ?? '')));
        $kodePerusahaan = trim((string) ($userLogin->company_code ?? ''));
        $perPage        = (int) $request->get('per_page', 25);

        $targetSatuUnit = DB::table('users')
            ->where('employee_id', $employeeId)
            ->whereNotNull('unit_name')
            ->whereRaw("TRIM(unit_name) <> ''")
            ->whereRaw('UPPER(TRIM(unit_name)) = ?', [$unitLogin]);

        if ($kodePerusahaan !== '') {
            $targetSatuUnit->where('company_code', $kodePerusahaan);
        }

        $boleh = ($employeeId === $userLogin->employee_id) || $targetSatuUnit->exists();
        abort_unless($boleh, 403, 'Tidak diizinkan melihat data di luar unit Anda.');

        if ($tipe === 'inovasi') {
            $teamIds = DB::table('pvt_members')
                ->where('employee_id', $employeeId)
                ->pluck('team_id');

            if ($teamIds->isEmpty()) {
                $items = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1, [
                    'path'  => $request->url(),
                    'query' => $request->query(),
                ]);
                $teamRanks = collect();
            } else {
                $items = DB::table('teams as t')
                    ->join('papers as p', 'p.team_id', '=', 't.id')
                    ->leftJoin('pvt_event_teams as pet', 'pet.team_id', '=', 't.id')
                    ->leftJoin('events as e', 'e.id', '=', 'pet.event_id')
                    ->leftJoin('themes as th', 'th.id', '=', 't.theme_id')
                    ->leftJoin('categories as c', 'c.id', '=', 't.category_id')
                    ->leftJoin('certificates', 'e.id', '=', 'certificates.event_id')
                    ->whereIn('t.id', $teamIds)
                    ->select(
                        'p.id',
                        'p.innovation_title',
                        'p.potensi_replikasi',
                        't.id as team_id',
                        't.team_name',
                        't.status_lomba',
                        'c.category_name as category_name',
                        'e.event_name',
                        'e.year',
                        'e.date_end as event_end',
                        DB::raw('(SELECT company_name FROM companies
                                JOIN company_event ON companies.id = company_event.company_id
                                WHERE company_event.event_id = e.id
                                LIMIT 1) as company_name'),
                        'certificates.template_path as certificate',
                        'certificates.special_template_path as special_certificate',
                        'certificates.badge_rank_1',
                        'certificates.badge_rank_2',
                        'certificates.badge_rank_3',
                        'pet.status as status',
                        'th.theme_name',
                        'pet.is_best_of_the_best',
                        'pet.is_honorable_winner',
                        'pet.event_id as event_id'
                    )
                    ->orderByDesc('e.year')
                    ->orderBy('t.team_name')
                    ->paginate($perPage)
                    ->appends($request->query());

                $teamRanks = DB::table('teams')
                    ->select(
                        'teams.id as team_id',
                        'categories.category_name',
                        'events.event_name',
                        'events.year',
                        DB::raw('COALESCE(pvt_event_teams.final_score, 0) as score'),
                        DB::raw('(
                            SELECT COUNT(*) + 1
                            FROM teams AS t
                            JOIN pvt_event_teams AS pet2 ON t.id = pet2.team_id
                            WHERE t.category_id = teams.category_id
                            AND pet2.event_id = events.id
                            AND COALESCE(pet2.final_score, 0) > COALESCE(pvt_event_teams.final_score, 0)
                        ) AS `rank`')
                    )
                    ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('categories', 'teams.category_id', '=', 'categories.id')
                    ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
                    ->whereIn('teams.id', $teamIds)
                    ->get()->keyBy('team_id');
            }

        } elseif ($tipe === 'paten') {
        $items = DB::table('patent as pt')
            ->leftJoin('users as u', 'u.employee_id', '=', 'pt.person_in_charge')
            ->where('pt.person_in_charge', $employeeId)
            ->selectRaw("
                pt.id,
                pt.patent_title,
                pt.person_in_charge,
                COALESCE(u.name, pt.pic_name) AS pic_name,
                NULL AS team_name,     
                NULL AS event_name,    
                NULL AS year           
            ")
            ->orderByDesc('pt.created_at')  
            ->orderBy('pt.patent_title')
            ->paginate($perPage)
            ->appends($request->query());

        $teamRanks = collect();


        } else {
            $items = DB::table('replication_requests as r')
                ->join('papers as p','p.id','=','r.paper_id')
                ->join('teams as t','t.id','=','p.team_id')
                ->leftJoin('themes as th','th.id','=','t.theme_id')
                ->leftJoin('categories as c','c.id','=','t.category_id')
                ->leftJoin('pvt_event_teams as pet','pet.team_id','=','t.id')
                ->leftJoin('events as e','e.id','=','pet.event_id')
                ->where('r.pic_name', $employeeId) 
                ->selectRaw("
                    r.id,
                    p.innovation_title,
                    t.team_name,
                    th.theme_name,
                    c.category_name,
                    e.event_name,
                    e.year
                ")
                ->orderByDesc('e.year')
                ->orderBy('t.team_name')
                ->paginate($perPage)
                ->appends($request->query());

            $teamRanks = collect();
        }

        $namaKaryawan = DB::table('users')->where('employee_id',$employeeId)->value('name');

        return view('prestasi.list', [
            'tipe'         => $tipe,
            'employeeId'   => $employeeId,
            'namaKaryawan' => $namaKaryawan,
            'items'        => $items,
            'teamRanks'    => $teamRanks,
        ]);
    }

}