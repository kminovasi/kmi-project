<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Event;
use Auth;
use DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CertificateController extends Controller
{
    /**
     * Display a listing of the certificates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userRole = Auth::user();
        // dd($userRole);

        if ($userRole->role == 'Admin') {
            $eventsWithoutCertificate = \DB::table('events')
                ->leftjoin('certificates', 'events.id', '=', 'certificates.event_id')
                ->leftJoin('company_event', 'events.id', '=', 'company_event.event_id')
                ->leftJoin('companies', 'company_event.company_id', '=', 'companies.id')
                ->whereNull('certificates.event_id')
                ->where('companies.company_code', '=', $userRole->company_code)
                ->select(
                    'events.id as event_id',
                    'events.event_name',
                    'events.year',
                    'certificates.template_path'
                )
                ->distinct()
                ->get();

            $certificates = Certificate::with(['event.companies'])
                ->whereHas('event.companies', function ($query) use ($userRole) {
                    $query->where('company_code', '=', $userRole->company_code);
                })
                ->get();

        } else {
            $eventsWithoutCertificate = \DB::table('events')
                ->leftjoin('certificates', 'events.id', '=', 'certificates.event_id')
                ->leftJoin('company_event', 'events.id', '=', 'company_event.event_id')
                ->leftJoin('companies', 'company_event.company_id', '=', 'companies.id')
                ->whereNull('certificates.event_id')
                ->select(
                    'events.id as event_id',
                    'events.event_name',
                    'events.year',
                    'certificates.template_path'
                )
                ->distinct()
                ->get();

            $certificates = Certificate::with('event.companies')->get();
        }

        return view("admin.certificate.certificate", compact('certificates', 'eventsWithoutCertificate'));
    }

    /**
     * Store a newly created certificate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
        
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'template_certificate' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'special_template_certificate' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'badge_rank_1' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'badge_rank_2' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'badge_rank_3' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'certificate_date' => 'required|date',
        ], [
            'required' => ':attribute wajib diisi.',
            'mimes'    => ':attribute harus berupa file JPG/PNG.',
            'max'      => 'Ukuran :attribute maksimal 5MB.',
            'exists'   => 'Event tidak ditemukan.',
        ], [
            'template_certificate' => 'Template Sertifikat',
            'special_template_certificate' => 'Template Sertifikat Khusus',
            'badge_rank_1' => 'Badge Juara 1',
            'badge_rank_2' => 'Badge Juara 2',
            'badge_rank_3' => 'Badge Juara 3',
            'certificate_date' => 'Tanggal Sertifikat',
        ]);

        $disk = Storage::disk('public');
        $saved = []; 

        DB::beginTransaction();
        try {
            $certificateTemplatePath = $request->file('template_certificate')
                ->store('certificate', 'public');
            if (!$certificateTemplatePath) throw new \RuntimeException('Gagal upload template sertifikat.');
            $saved[] = $certificateTemplatePath;

            $specialCertificateTemplatePath = $request->file('special_template_certificate')
                ->store('certificate', 'public');
            if (!$specialCertificateTemplatePath) throw new \RuntimeException('Gagal upload template sertifikat khusus.');
            $saved[] = $specialCertificateTemplatePath;

            $badgeRank1Path = $request->file('badge_rank_1')->store('certificate/badge', 'public');
            if (!$badgeRank1Path) throw new \RuntimeException('Gagal upload badge juara 1.');
            $saved[] = $badgeRank1Path;

            $badgeRank2Path = $request->file('badge_rank_2')->store('certificate/badge', 'public');
            if (!$badgeRank2Path) throw new \RuntimeException('Gagal upload badge juara 2.');
            $saved[] = $badgeRank2Path;

            $badgeRank3Path = $request->file('badge_rank_3')->store('certificate/badge', 'public');
            if (!$badgeRank3Path) throw new \RuntimeException('Gagal upload badge juara 3.');
            $saved[] = $badgeRank3Path;

            $date = Carbon::parse($request->certificate_date)->toDateString();

            Certificate::create([
                'event_id'             => $request->event_id,
                'template_path'        => $certificateTemplatePath,
                'special_template_path'=> $specialCertificateTemplatePath,
                'badge_rank_1'         => $badgeRank1Path,
                'badge_rank_2'         => $badgeRank2Path,
                'badge_rank_3'         => $badgeRank3Path,
                'certificate_date'     => $date,
            ]);

            DB::commit();
            return redirect()->route('certificates.index')->with('success', 'Sertifikat berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ($saved as $path) {
                try { $disk->delete($path); } catch (\Throwable $ex) {}
            }

            Log::error('Gagal membuat sertifikat', [
                'event_id' => $request->event_id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Sertifikat tidak berhasil diunggah. Silakan coba lagi atau hubungi admin. (' . $e->getMessage() . ')');
        }
    }


    /**
     * Remove the specified certificate from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
    
        $paths = [
            $certificate->template_path,
            $certificate->special_template_path,
            $certificate->badge_rank_1,
            $certificate->badge_rank_2,
            $certificate->badge_rank_3,
        ];
    
        foreach ($paths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    
        $certificate->delete();
    
        return redirect()->route('certificates.index')->with('success', 'Sertifikat berhasil dihapus.');
    }

    /**
     * Activate the specified certificate.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        Certificate::where('is_active', true)->update(['is_active' => false]);

        $certificate = Certificate::findOrFail($id);
        $certificate->is_active = true;
        $certificate->save();

        return redirect()->route('certificates.index')->with('success', 'Sertifikat berhasil diaktifkan.');
    }

    public function showAll()
    {
        $certificates = Certificate::all(); 
        return view('admin.certificate.show-all-certificate', compact('certificates'));
    }

    public function showJudgeCertificatesAll(Request $request)
    {
        $eventId = $request->input('event_id');

        $judges = DB::table('judges')
            ->join('users',  'users.employee_id', '=', 'judges.employee_id')
            ->join('events', 'events.id',         '=', 'judges.event_id')
            ->where('judges.status', 'active')
            ->when($eventId, fn($q) => $q->where('judges.event_id', $eventId))
            ->select(
                'users.name',
                'users.company_name',
                'users.employee_id',
                'events.id as event_id',
                'events.event_name as event_title',
                'events.year as event_year',
                'events.date_end'
            )
            ->orderBy('events.date_end', 'desc')
            ->orderBy('users.name')
            ->get();

        $events = DB::table('events')
            ->join('judges', 'judges.event_id', '=', 'events.id')
            ->where('judges.status', 'active')
            ->distinct()
            ->orderByDesc('events.year')
            ->orderBy('events.event_name')
            ->get(['events.id', 'events.event_name', 'events.year']);

        return view('admin.certificate.list-judge-certificate', [
            'judges'  => $judges,
            'events'  => $events,
            'eventId' => $eventId,
            'event'   => null,
        ]);
    }

    public function showParticipantCertificatesAll(Request $request)
    {
        abort_unless(\Auth::user() && \Auth::user()->role === 'Superadmin', 403);

        $q          = trim($request->input('q', ''));       
        $eventId    = $request->input('event_id');          
        $categoryId = $request->input('category_id');        

        $base = \DB::table('teams')
            ->join('pvt_event_teams as pet', 'teams.id', '=', 'pet.team_id')
            ->join('events as e', 'pet.event_id', '=', 'e.id')
            ->leftJoin('certificates as c', 'e.id', '=', 'c.event_id')
            ->leftJoin('categories as cat', 'teams.category_id', '=', 'cat.id')
            ->leftJoin('company_event as ce', 'e.id', '=', 'ce.event_id')
            ->leftJoin('companies as comp', 'ce.company_id', '=', 'comp.id')
            ->leftJoin('papers as p', 'teams.id', '=', 'p.team_id') 
            ->where('e.status', 'finish');

        if ($q !== '') {
            $base->where('teams.team_name', 'LIKE', "%{$q}%");
        }
        if (!empty($eventId)) {
            $base->where('e.id', '=', $eventId);
        }
        if (!empty($categoryId)) {
            $base->where('teams.category_id', '=', $categoryId);
        }

        $teams = $base
            ->groupBy('teams.id', 'teams.category_id', 'e.id')
            ->selectRaw('
                teams.id                                   AS team_id,
                e.id                                       AS event_id,

                MAX(teams.team_name)                       AS team_name,
                MAX(e.event_name)                          AS event_name,
                MAX(e.year)                                AS year,
                MAX(e.date_end)                            AS event_end,
                MAX(cat.category_name)                     AS category,
                MAX(COALESCE(comp.company_name, "-"))      AS company_name,

                MAX(c.template_path)                       AS certificate,
                MAX(c.badge_rank_1)                        AS badge_rank_1,
                MAX(c.badge_rank_2)                        AS badge_rank_2,
                MAX(c.badge_rank_3)                        AS badge_rank_3,

                MAX(COALESCE(pet.final_score,0))           AS score,
                MAX(COALESCE(p.innovation_title, teams.team_name)) AS innovation_title,
                MAX(COALESCE(pet.is_best_of_the_best,0))   AS is_best_of_the_best,
                MAX(COALESCE(pet.is_honorable_winner,0))   AS is_honorable_winner,
                MAX(COALESCE(pet.keputusan_bod,0))   AS keputusan_bod,

                (
                    SELECT COUNT(*) + 1
                    FROM pvt_event_teams pet2
                    JOIN teams t2 ON t2.id = pet2.team_id
                    WHERE t2.category_id = teams.category_id
                    AND pet2.event_id  = e.id
                    AND COALESCE(pet2.final_score,0) > (
                        SELECT COALESCE(MAX(pet_self.final_score),0)
                        FROM pvt_event_teams pet_self
                        WHERE pet_self.team_id  = teams.id
                            AND pet_self.event_id = e.id
                    )
                ) AS team_rank
            ')
            ->orderByDesc('year')
            ->orderBy('team_name')
            ->get();

        $events = \DB::table('events')
            ->where('status','finish')
            ->orderByDesc('year')->orderBy('event_name')
            ->get(['id','event_name','year']);

        $categories = \DB::table('categories')
            ->orderBy('category_name')
            ->get(['id','category_name']);

        return view('admin.certificate.list-participant-certificate', [
            'teams'       => $teams,
            'events'      => $events,
            'categories'  => $categories,
            'q'           => $q,
            'eventId'     => $eventId,
            'categoryId'  => $categoryId,
        ]);
    }

}
