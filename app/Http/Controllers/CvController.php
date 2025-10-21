<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use ZipArchive;

class CvController extends Controller
{
    public function index(Request $request) 
    {
        $employee = Auth::user();
        $myUnit  = strtoupper(trim((string) ($employee->unit_name ?? '')));
        $myComp    = trim((string) ($employee->company_code ?? ''));
        $perPage = (int) $request->get('per_page', 10);

        $myTeamIds = DB::table('pvt_members')
            ->where('employee_id', $employee->employee_id)
            ->distinct()
            ->pluck('team_id');

        $sameUnitBase = DB::table('pvt_members as pm')
            ->join('users as u', 'u.employee_id', '=', 'pm.employee_id')
            ->whereNotNull('u.unit_name')
            ->whereRaw("TRIM(u.unit_name) <> ''")
            ->whereRaw('UPPER(TRIM(u.unit_name)) = UPPER(TRIM(?))', [$myUnit]);

        if ($myComp !== '') {
            $sameUnitBase->where('u.company_code', $myComp);
        }

        $sameUnitTeamIds = $sameUnitBase->distinct()->pluck('pm.team_id');
        $visibleTeamIds = $myTeamIds->merge($sameUnitTeamIds)->unique()->values();

        if ($visibleTeamIds->isEmpty()) {
            $innovations = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1, [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]);

            $events     = DB::table('events')->where('status','finish')->select('id','event_name','year')->orderBy('year','desc')->get();
            $years      = DB::table('events')->where('status','finish')->pluck('year')->unique();
            $categories = DB::table('categories')->pluck('category_name');
            $teamRanks  = collect();

            return view('auth.admin.dokumentasi.cv.index', compact('innovations','employee','teamRanks','events','years','categories'));
        }

        $innovations = DB::table('pvt_members')
            ->select(
                'papers.id',
                'papers.innovation_title',
                'papers.potensi_replikasi',
                'teams.id as team_id',
                'teams.team_name',
                'teams.status_lomba',
                'categories.category_name as category',
                'events.event_name',
                'events.year',
                'events.date_end as event_end',
                DB::raw('(SELECT company_name FROM companies
                          JOIN company_event ON companies.id = company_event.company_id
                          WHERE company_event.event_id = events.id
                          LIMIT 1) as company_name'),
                'certificates.template_path as certificate',
                'certificates.special_template_path as special_certificate',
                'certificates.badge_rank_1',
                'certificates.badge_rank_2',
                'certificates.badge_rank_3',
                'pvt_event_teams.status as status',
                'themes.theme_name',
                'pvt_event_teams.is_best_of_the_best',
                'pvt_event_teams.is_honorable_winner',
                'pvt_event_teams.event_id as event_id',   
                DB::raw('MIN(pvt_members.status) as member_status'),
                DB::raw('MIN(users.name) as member_name')
                // HAPUS: DB::raw('MIN(users.employee_id) as employee_id')
            )

            ->leftJoin('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->leftJoin('papers', 'teams.id', '=', 'papers.team_id')
            ->leftJoin('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->leftJoin('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->leftJoin('certificates', 'events.id', '=', 'certificates.event_id')
            ->leftJoin('themes', 'teams.theme_id', '=', 'themes.id')
            ->leftJoin('categories', 'teams.category_id', '=', 'categories.id')
            ->leftJoin('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->where('events.status', 'finish')
            ->whereIn('teams.id', $visibleTeamIds->toArray());

        if ($request->filled('event'))    { $innovations->where('events.id', (int) $request->event); }
        if ($request->filled('year'))     { $innovations->where('events.year', (int) $request->year); }
        if ($request->filled('category')) { $innovations->where('categories.category_name', $request->category); }
        if ($request->filled('search'))   { $innovations->where('teams.team_name', 'like', '%'.$request->search.'%'); }

        $innovations->whereExists(function ($q) use ($employee, $myUnit, $myComp) {
            $q->select(DB::raw(1))
            ->from('pvt_members as pm2')
            ->join('users as u2', 'u2.employee_id', '=', 'pm2.employee_id')
            ->whereColumn('pm2.team_id', 'teams.id')
            ->where(function ($qq) use ($employee, $myUnit, $myComp) {
                $qq->where('pm2.employee_id', $employee->employee_id)
                    ->orWhere(function ($qq2) use ($myUnit, $myComp) {
                        $qq2->whereNotNull('u2.unit_name')
                            ->whereRaw("TRIM(u2.unit_name) <> ''")
                            ->whereRaw('UPPER(TRIM(u2.unit_name)) = ?', [$myUnit]);
                        if ($myComp !== '') {
                            $qq2->where('u2.company_code', $myComp);
                        }
                    });
            });
        });

        $innovations->groupBy(
            'papers.id','papers.innovation_title','papers.potensi_replikasi',
            'teams.id','teams.team_name','teams.status_lomba',
            'categories.category_name',
            'events.id',                
            'events.event_name','events.year','events.date_end',
            'pvt_event_teams.status','themes.theme_name',
            'pvt_event_teams.is_best_of_the_best','pvt_event_teams.is_honorable_winner','pvt_event_teams.event_id',
            'certificates.template_path','certificates.special_template_path',
            'certificates.badge_rank_1','certificates.badge_rank_2','certificates.badge_rank_3'
        );

        $innovations = $innovations
            ->orderBy('events.year','desc')
            ->orderBy('teams.team_name')
            ->orderBy('member_name')
            ->paginate($perPage)
            ->appends($request->query());

        $events     = DB::table('events')->where('status', 'finish')
                        ->select('id','event_name','year')->orderBy('year','desc')->get();
        $years      = DB::table('events')->where('status', 'finish')->pluck('year')->unique();
        $categories = DB::table('categories')->pluck('category_name');

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
            ->whereIn('teams.id', $visibleTeamIds->toArray())
            ->get()->keyBy('team_id');

        return view('auth.admin.dokumentasi.cv.index', compact('innovations', 'employee', 'teamRanks', 'events', 'years', 'categories'));
    }
    

    private function renderViewToJpeg(string $view, array $data, array $opt = []): string
    {
        $orientation = $opt['orientation'] ?? 'landscape';
        $width  = $opt['width']  ?? ($orientation === 'landscape' ? 3508 : 2480); 
        $height = $opt['height'] ?? ($orientation === 'landscape' ? 2480 : 3508);
        $quality = $opt['quality'] ?? 92;
    
        $tmpDir = storage_path('app/tmp_cert');
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);
    
        $pdf = \PDF::loadView($view, $data)->setPaper('A4', $orientation);
        $pdfPath = $tmpDir.'/cert-'.Str::uuid().'.pdf';
        file_put_contents($pdfPath, $pdf->output());
    
        $jpgPath = $tmpDir.'/cert-'.Str::uuid().'.jpg';
    
        $imagick = new \Imagick();
        $imagick->setResolution(300, 300); 
        $imagick->setBackgroundColor(new \ImagickPixel('white'));
        $imagick->readImage($pdfPath.'[0]'); 
        $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
        $imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
        $imagick->setImageFormat('jpeg');
        $imagick->setImageCompressionQuality($quality);
        $imagick->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, true); 
        $imagick->writeImage($jpgPath);
        $imagick->clear();
        $imagick->destroy();
    
        @unlink($pdfPath);
        return $jpgPath;
    }


    public function generateCertificate(Request $request)
    {
        $inovasi         = json_decode($request->input('inovasi'), true);
        $certificateType = $request->input('certificate_type');
        $teamRanks       = json_decode($request->input('team_rank'), true);

        \Carbon\Carbon::setLocale('id');
        
            $titleCase = function (?string $s): string {
            if (!$s) return '';
            $minor = ['dan','atau','yang','untuk','dari','pada','ke','di','dengan','serta','terhadap','oleh','bagi','dalam','sebagai','tentang','hingga','guna','para'];
            $parts = preg_split('/(\s+)/u', trim($s), -1, PREG_SPLIT_DELIM_CAPTURE);
            $idx = 0; $words = array_values(array_filter($parts, fn($x)=>trim($x) !== '')); $last = count($words)-1;
            foreach ($parts as $i=>$t) {
                if (preg_match('/^\s+$/u',$t)) continue;
                $isFirst = ($idx===0); $isLast = ($idx===$last);
                $parts[$i] = preg_replace_callback('/[^-\s]+/u', function($m) use($minor,$isFirst,$isLast){
                    $w=$m[0]; $low=mb_strtolower($w,'UTF-8');
                    if (preg_match('/^[A-Z0-9\-]+$/u',$w)) return $w; // akronim/ROMAWI
                    $cap = $isFirst || $isLast || !in_array($low,$minor,true) || mb_strlen($low,'UTF-8')>3;
                    return $cap ? mb_strtoupper(mb_substr($low,0,1)).mb_substr($low,1) : $low;
                }, $t);
                $idx++;
            }
            return implode('', $parts);
        };


        $view = null;
        $data = [];
        $certificateName = 'Certificate';
        
        $parseInt = function ($raw) {
            if ($raw === null) return null;
            if (is_numeric($raw)) return (int)$raw;
            $dec = json_decode($raw, true);
            return is_numeric($dec) ? (int)$dec : null;
        };

        $resolveBg = function (?string $relPath) {
        if (empty($relPath)) return null;
        $relPath = trim($relPath, '/');
        $p1 = storage_path('app/public/' . $relPath);
        if (is_file($p1)) return $p1;
        $p2 = public_path('storage/' . $relPath);
        if (is_file($p2)) return $p2;
        return null;
        };
        
            $getEvent = function ($eventId) {
            if (empty($eventId)) return null;
            return \DB::table('events')
                ->leftJoin('certificates', 'events.id', '=', 'certificates.event_id')
                ->where('events.id', $eventId)
                ->select([
                    'events.event_name',
                    'events.year',
                    'certificates.certificate_date',
                    'certificates.template_path',
                    'certificates.special_template_path',
                ])->first();
        };


        //SERTIFIKAT JURI 
        if ($certificateType === 'judge') {

             $view = 'auth.user.profile.judge-certificate';   

            $employeeRaw  = $request->input('employee');
            $employeeData = is_string($employeeRaw) ? json_decode($employeeRaw, true) : $employeeRaw;
            $employeeId   = is_array($employeeData) ? ($employeeData['employee_id'] ?? null) : $employeeRaw;

            if (!$employeeId) {
                abort(422, 'employee_id juri tidak valid.');
            }

            // Data Juri
            $employee = \DB::table('users')
                ->where('employee_id', $employeeId)
                ->select('name', 'company_name', 'employee_id', 'position_title')
                ->first();

            if (!$employee) {
                abort(404, 'Data Juri tidak ditemukan.');
            }

            $eventId = $request->input('event_id') ?? ($inovasi['event_id'] ?? null);
            if (!$eventId) {
                abort(422, 'event_id tidak ditemukan.');
            }

            $bodData = \DB::table('bod_events')
                ->leftJoin('users', 'users.employee_id', '=', 'bod_events.employee_id')
                ->where('bod_events.event_id', $eventId)
                ->select('users.name as bod_name', 'users.position_title as title')
                ->first();

            $judgeEvent = \DB::table('events')
                ->leftJoin('certificates', 'events.id', '=', 'certificates.event_id')
                ->where('events.id', $eventId)
                ->where('events.status', 'finish')
                ->select([
                        'certificates.template_path',
                        'certificates.certificate_date', 
                        'events.event_name as event_name', 
                        'events.year as year',
                    ])
                ->first();
            
            if (empty($judgeEvent) || empty($judgeEvent->template_path)) {
            abort(404, "Template sertifikat belum diatur untuk event ini.");
        }

            $templatePath = trim($judgeEvent->template_path, '/');

            $bg_abs = storage_path('app/public/' . $templatePath);
            if (!is_file($bg_abs)) {
                $tryPublic = public_path('storage/' . $templatePath);
                if (is_file($tryPublic)) {
                    $bg_abs = $tryPublic;
                } else {
                    abort(404, "File template sertifikat tidak ditemukan: {$templatePath}");
                }
            }

            $data = [
                'user_name'      => $employee->name,
                'position_title'   => $employee->position_title ?? '-',
                'company_name'   => $employee->company_name,
                'event_name'       => $judgeEvent->event_name ?? null, 
                'year'             => $judgeEvent->year ?? null,     
                'template_path'  => $judgeEvent->template_path ?? null,
                'event_end_date' => $judgeEvent->date_end ?? null,
                'certificate_date' => $judgeEvent->certificate_date ?? null,
                'bodName'        => $bodData->bod_name ?? '-',
                'bodTitle'       => $bodData->title ?? '-',
                'bg_abs'         => $bg_abs,
            ];

            $certificateName = $employee->name;

            $imgPath = $this->renderViewToJpeg($view, $data, [
                'orientation' => 'landscape',
                'quality' => 92,
            ]);
            $filename = 'Sertifikat - ' . $certificateName . '.jpg';
            return response()->download($imgPath, $filename, [
                'Content-Type' => 'image/jpeg'
            ])->deleteFileAfterSend(true);

        }

        // SEMUA PESERTA
        if ($certificateType === 'participant') {
            $auth    = \Auth::user();
            $role    = optional($auth)->role;
            $empId   = optional($auth)->employee_id;
        
            $bodData = \DB::table('bod_events')
                ->leftJoin('users', 'users.employee_id', '=', 'bod_events.employee_id')
                ->where('bod_events.event_id', $inovasi['event_id'] ?? 0)
                ->select('users.name as bod_name', 'users.position_title as title')
                ->first();
        
            $event = $getEvent($inovasi['event_id'] ?? null);
            $bg_abs = $resolveBg($inovasi['certificate'] ?? null);
            if (!$bg_abs) {
                abort(404, 'Template sertifikat peserta tidak ditemukan.');
            }
        
            // ---- MODE SUPERADMIN: ZIP SEMUA ANGGOTA (kecuali GM) ----
            if (strcasecmp((string)$role, 'Superadmin') === 0) {
                $team_members = \DB::table('pvt_members as pm')
                    ->join('users as u', 'u.employee_id', '=', 'pm.employee_id')
                    ->where('pm.team_id', $inovasi['team_id'])
                    ->whereRaw("LOWER(pm.status) <> 'gm'")
                    ->select('u.name as member_name', 'u.company_name', 'pm.status', 'pm.employee_id')
                    ->get();
        
                if ($team_members->isEmpty()) {
                    abort(404, 'Tidak ada anggota tim ditemukan untuk dibuatkan ZIP.');
                }
        
                $zip = new \ZipArchive();
                $zipFileName = 'Sertifikat_' . ($inovasi['team_name'] ?? 'Tim') . '.zip';
                $zipPath = storage_path('app/public/' . $zipFileName);
        
                $tempImages = [];
                if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                    abort(500, 'Gagal membuka/membuat file ZIP.');
                }
        
                foreach ($team_members as $member) {
                    $data = [
                        'user_name'        => $member->member_name,
                        'team_name'        => $inovasi['team_name'],
                        'company_name'     => $member->company_name ?? $inovasi['company_name'],
                        'category_name'    => $inovasi['category'],
                        'template_path'    => $inovasi['certificate'],
                        'team_rank'        => $teamRanks,
                        'member_status'    => $member->status ?? '-',
                        'event_end_date'   => $inovasi['event_end'],
                        'bodName'          => $bodData->bod_name ?? '-',
                        'bodTitle'         => $bodData->title ?? '-',
                        'event_name'       => $event->event_name ?? ($inovasi['event_name'] ?? null),
                        'year'             => $event->year ?? ($inovasi['year'] ?? null),
                        'certificate_date' => $event->certificate_date ?? null,
                        'bg_abs'           => $bg_abs,
                    ];
        
                    $imgPath = $this->renderViewToJpeg(
                        'auth.admin.dokumentasi.cv.participant-certificate',
                        $data,
                        ['orientation' => 'landscape', 'quality' => 92]
                    );
        
                    $zip->addFile($imgPath, 'Sertifikat - ' . $member->member_name . '.jpg');
                    $tempImages[] = $imgPath;
                }
        
                $zip->close();
                foreach ($tempImages as $tmp) { @unlink($tmp); }
                return response()->download($zipPath)->deleteFileAfterSend(true);
            }
        
            // ---- MODE NON-SUPERADMIN: HANYA MILIKNYA SENDIRI ----
            if (empty($empId)) {
                abort(403, 'Tidak dapat mengidentifikasi akun Anda.');
            }
        
            $me = \DB::table('pvt_members as pm')
                ->join('users as u', 'u.employee_id', '=', 'pm.employee_id')
                ->where('pm.team_id', $inovasi['team_id'])
                ->where('pm.employee_id', $empId)
                ->whereRaw("LOWER(pm.status) <> 'gm'")
                ->select('u.name as member_name', 'u.company_name', 'pm.status')
                ->first();
        
            if (!$me) {
                abort(403, 'Sertifikat tidak tersedia.');
            }
        
            $data = [
                'user_name'        => $me->member_name,
                'team_name'        => $inovasi['team_name'],
                'company_name'     => $me->company_name ?? $inovasi['company_name'],
                'category_name'    => $inovasi['category'],
                'template_path'    => $inovasi['certificate'],
                'team_rank'        => $teamRanks,
                'member_status'    => $me->status ?? '-',
                'event_end_date'   => $inovasi['event_end'],
                'bodName'          => $bodData->bod_name ?? '-',
                'bodTitle'         => $bodData->title ?? '-',
                'event_name'       => $event->event_name ?? ($inovasi['event_name'] ?? null),
                'year'             => $event->year ?? ($inovasi['year'] ?? null),
                'certificate_date' => $event->certificate_date ?? null,
                'bg_abs'           => $bg_abs,
            ];
        
            $imgPath = $this->renderViewToJpeg(
                'auth.admin.dokumentasi.cv.participant-certificate',
                $data,
                ['orientation' => 'landscape', 'quality' => 92]
            );
        
            $filename = 'Sertifikat - ' . $me->member_name . '.jpg';
            return response()->download($imgPath, $filename, ['Content-Type' => 'image/jpeg'])
                ->deleteFileAfterSend(true);
        }

        // SERTIFIKAT TIM 
        if ($certificateType === 'team') {
            $userRole = optional(\Auth::user())->role;
        
            $bodData = \DB::table('bod_events')
                ->leftJoin('users', 'users.employee_id', '=', 'bod_events.employee_id')
                ->where('bod_events.event_id', $inovasi['event_id'] ?? 0)
                ->select('users.name as bod_name', 'users.position_title as title')
                ->first();
            
            $event = $getEvent($inovasi['event_id'] ?? null);
        
            $bg_abs     = $resolveBg($inovasi['certificate'] ?? null);
            $badge1_abs = $resolveBg($inovasi['badge_rank_1'] ?? null);
            $badge2_abs = $resolveBg($inovasi['badge_rank_2'] ?? null);
            $badge3_abs = $resolveBg($inovasi['badge_rank_3'] ?? null);
        
            $rankValue = null;
            $teamStatusLabel = 'Peserta'; 
            $eventId = $inovasi['event_id'] ?? $request->input('event_id');
            $teamId  = $inovasi['team_id']  ?? $request->input('team_id');
        
            if (!empty($eventId) && !empty($teamId)) {
                // status dan skor tim
                $teamRow = \DB::table('pvt_event_teams as pet')
                    ->join('teams as t', 't.id', '=', 'pet.team_id')
                    ->where('pet.event_id', $eventId)
                    ->where('pet.team_id',  $teamId)
                    ->select(
                        't.category_id as cat_id',
                        \DB::raw('COALESCE(pet.final_score, -1) as my_score'), 
                        'pet.status as my_status'
                    )
                    ->first();
        
                if ($teamRow) {
                    $norm = trim((string)$teamRow->my_status);
                    $teamStatusLabel = $norm !== '' ? ucwords(strtolower($norm)) : 'Peserta';
        
                    // status "Juara" → hitung peringkat
                    if (strcasecmp($norm, 'Juara') === 0) {
                        $countHigher = \DB::table('pvt_event_teams as pet2')
                            ->join('teams as t2', 't2.id', '=', 'pet2.team_id')
                            ->where('pet2.event_id', $eventId)
                            ->where('t2.category_id', $teamRow->cat_id)
                            ->whereRaw('LOWER(pet2.status) = ?', ['juara'])
                            ->whereRaw('COALESCE(pet2.final_score, -1) > ?', [$teamRow->my_score])
                            ->count();
        
                        $rankValue = $countHigher + 1; // 1/2/3
                    }
                }
            }
        
            if ($rankValue === null && $userRole === 'Superadmin') {
                $currentStatus = \DB::table('pvt_event_teams')
                    ->where('event_id', $eventId)->where('team_id', $teamId)
                    ->value('status');
        
                if (strcasecmp((string)$currentStatus, 'Juara') === 0) {
                    $rankValue = (function ($raw) {
                        if ($raw === null) return null;
                        if (is_numeric($raw)) return (int)$raw;
                        $dec = json_decode($raw, true);
                        return is_numeric($dec) ? (int)$dec : null;
                    })($request->input('team_rank'));
                }
            }
        
            $data = [
                'innovation_title' => $titleCase($inovasi['innovation_title'] ?? ''),
                'team_name'        => $inovasi['team_name']        ?? '',
                'company_name'     => $inovasi['company_name']     ?? '',
                'category_name'    => $inovasi['category']         ?? '',
                'template_path'    => $inovasi['certificate']      ?? null,
                'team_rank'        => $rankValue,
                'team_status'      => $teamStatusLabel,
                'event_end_date'   => $inovasi['event_end']        ?? null,
                'bodName'          => $bodData->bod_name           ?? '-',
                'bodTitle'         => $bodData->title              ?? '-',
                'bg_abs'           => $bg_abs,
                'badge1_abs'       => $badge1_abs,
                'badge2_abs'       => $badge2_abs,
                'badge3_abs'       => $badge3_abs,
                'event_name'       => $event->event_name ?? ($inovasi['event_name'] ?? null),
                'year'             => $event->year ?? ($inovasi['year'] ?? null),
                'certificate_date' => $event->certificate_date ?? null,
            ];
        
            $view = 'auth.admin.dokumentasi.cv.team-certificate';
            $certificateName = $inovasi['team_name'] ?? 'Tim';
        }

        
        //BEST OF THE BEST
        if ($certificateType === 'best_of_the_best') {
            $bodData = \DB::table('bod_events')
                ->leftJoin('users', 'users.employee_id', '=', 'bod_events.employee_id')
                ->where('bod_events.event_id', $inovasi['event_id'] ?? 0)
                ->select('users.name as bod_name', 'users.position_title as title')
                ->first();
            
            $event = $getEvent($inovasi['event_id'] ?? null);
            
            $specialPath = $inovasi['special_certificate'] 
                ?? ($event->special_template_path ?? null);
        
            if (empty($specialPath)) {
                abort(404, 'Template spesial (special_template_path) belum diatur untuk event ini.');
            }
        
            $bg_abs = $resolveBg($specialPath);
            if (!$bg_abs) {
                abort(404, "File template spesial tidak ditemukan: {$specialPath}");
            }

            $data = [
                'innovation_title' => $inovasi['innovation_title'],
                'team_name'        => $inovasi['team_name'],
                'company_name'     => $inovasi['company_name'],
                'category_name'    => $inovasi['category'],
                'template_path'    => $inovasi['certificate'],
                'team_rank'        => $teamRanks,
                'event_end_date'   => $inovasi['event_end'],
                'bodName'          => $bodData->bod_name ?? '-',
                'bodTitle'         => $bodData->title ?? '-',
                'bg_abs'           => $bg_abs,
                'event_name'       => $event->event_name ?? ($inovasi['event_name'] ?? null),
                'year'             => $event->year ?? ($inovasi['year'] ?? null),
                'certificate_date' => $event->certificate_date ?? null,
            ];

            $view = 'auth.admin.dokumentasi.cv.best-of-the-best-certificate';
            $certificateName = $inovasi['team_name'] . '_BestOfTheBest';
        }

        //JUARA HARAPAN
        if ($certificateType === 'honorable_winner') {
            $bodData = \DB::table('bod_events')
                ->leftJoin('users', 'users.employee_id', '=', 'bod_events.employee_id')
                ->where('bod_events.event_id', $inovasi['event_id'] ?? 0)
                ->select('users.name as bod_name', 'users.position_title as title')
                ->first();
            
            $event = $getEvent($inovasi['event_id'] ?? null);

            $specialPath = $inovasi['special_certificate'] 
                ?? ($event->special_template_path ?? null);
        
            if (empty($specialPath)) {
                abort(404, 'Template spesial (special_template_path) belum diatur untuk event ini.');
            }
        
            $bg_abs = $resolveBg($specialPath);
            if (!$bg_abs) {
                abort(404, "File template spesial tidak ditemukan: {$specialPath}");
            }

            $data = [
                'innovation_title' => $inovasi['innovation_title'],
                'team_name'        => $inovasi['team_name'],
                'company_name'     => $inovasi['company_name'],
                'category_name'    => $inovasi['category'],
                'template_path'    => $inovasi['certificate'],
                'team_rank'        => $teamRanks,
                'event_end_date'   => $inovasi['event_end'],
                'bodName'          => $bodData->bod_name ?? '-',
                'bodTitle'         => $bodData->title ?? '-',
                'bg_abs'           => $bg_abs,
                'event_name'       => $event->event_name ?? ($inovasi['event_name'] ?? null),
                'year'             => $event->year ?? ($inovasi['year'] ?? null),
                'certificate_date' => $event->certificate_date ?? null,
            ];

            $view = 'auth.admin.dokumentasi.cv.honorable-winner-certificate';
            $certificateName = $inovasi['team_name'] . '_JuaraHarapan';
        }
        
        // KEPUTUSAN BOD
        if ($certificateType === 'keputusan_bod') {
            $bodData = \DB::table('bod_events')
                ->leftJoin('users', 'users.employee_id', '=', 'bod_events.employee_id')
                ->where('bod_events.event_id', $inovasi['event_id'] ?? 0)
                ->select('users.name as bod_name', 'users.position_title as title')
                ->first();
            
             $event = $getEvent($inovasi['event_id'] ?? null);

            $specialPath = $inovasi['special_certificate'] 
                ?? ($event->special_template_path ?? null);
        
            if (empty($specialPath)) {
                abort(404, 'Template spesial (special_template_path) belum diatur untuk event ini.');
            }
        
            $bg_abs = $resolveBg($specialPath);
            if (!$bg_abs) {
                abort(404, "File template spesial tidak ditemukan: {$specialPath}");
            }

            $keputusanBodLabel = !empty($inovasi['keputusan_bod'])
                ? ucwords(strtolower($inovasi['keputusan_bod']))
                : '-';

            $data = [
                'innovation_title' => $inovasi['innovation_title'],
                'team_name'        => $inovasi['team_name'],
                'company_name'     => $inovasi['company_name'],
                'category_name'    => $inovasi['category'],
                'template_path'    => $inovasi['certificate'],
                'team_rank'        => $teamRanks,
                'event_end_date'   => $inovasi['event_end'],
                'bodName'          => $bodData->bod_name ?? '-',
                'bodTitle'         => $bodData->title ?? '-',
                'keputusan_bod'    => $keputusanBodLabel,
                'bg_abs'           => $bg_abs,
                'event_name'       => $event->event_name ?? ($inovasi['event_name'] ?? null),
                'year'             => $event->year ?? ($inovasi['year'] ?? null),
                'certificate_date' => $event->certificate_date ?? null,
            ];

            $view = 'auth.admin.dokumentasi.cv.keputusan-bod-certificate';
            $certificateName = $inovasi['team_name'] . '_KeputusanBOD_' . $keputusanBodLabel;
        }

            if (!$view) {
            abort(404, 'Certificate view not found');
        }
        
        $imgPath = $this->renderViewToJpeg($view, $data, [
            'orientation' => 'landscape',
            'quality' => 92,
        ]);
        
        $filename = 'Sertifikat - ' . ($certificateName ?: 'Certificate') . '.jpg';
        return response()->download($imgPath, $filename, [
            'Content-Type' => 'image/jpeg'
        ])->deleteFileAfterSend(true);
    }
    

    function detail($id)
    {
        $team = Team::findOrFail($id);

        $papers = DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->leftJoin('document_supportings', 'papers.id', '=', 'document_supportings.paper_id')
            ->where('teams.id', $id)
            ->select(
                'papers.*',
                'teams.team_name',
                'pvt_event_teams.status as team_status',
                'pvt_event_teams.total_score_on_desk',
                'pvt_event_teams.total_score_presentation',
                'pvt_event_teams.total_score_caucus',
                'pvt_event_teams.final_score',
                'pvt_event_teams.is_best_of_the_best',
                'themes.theme_name',
                'events.event_name',
                'document_supportings.path'
            )
            ->limit(1)
            ->get();

        $outsourceMember = DB::table('ph2_members')
            ->where('team_id', $team->id)
            ->get()
            ->toArray();
            
        // dd($papers);
        // mendapatkan data member berdasarkan id team
        $teamMember = $team->pvtMembers()->with('user')->get();

        return view('auth.admin.dokumentasi.cv.detail', compact('teamMember', 'papers', 'outsourceMember'));
    }
}