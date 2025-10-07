<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Paper;
use App\Models\ReplicationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ReplicationSubmitted;

class ReplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuper = ($user->role === 'Superadmin');

        // Superadmin bisa filter status via ?status=pending|approved|rejected (default pending)
        $status = $isSuper ? ($request->get('status', 'pending')) : null;

        $q = ReplicationRequest::with(['team','paper','creator'])->orderByDesc('created_at');

        if ($isSuper) {
            if ($status) $q->where('status', $status);
        } else {
            $q->where('created_by', $user->id);
        }

        $replications = $q->paginate(10);

        return view('replications.index', compact('replications', 'isSuper', 'status'));
    }

    public function create(Team $team)
    {
        $paper = Paper::where('team_id', $team->id)->orderByDesc('id')->firstOrFail();

        return view('replications.create', [
            'team'     => $team,
            'paper'    => $paper,
            'returnTo' => url()->previous(),
        ]);
    }

    public function store(Request $request, Team $team)
    {
        $paper = Paper::where('team_id', $team->id)->orderByDesc('id')->firstOrFail();

        // BENAR (hanya mencegah duplikasi pending dari user yg sama pada tim yg sama)
        $existsPending = ReplicationRequest::where('team_id', $team->id)
            ->where('created_by', Auth::id())
            ->where('status', 'pending')
            ->exists();

        if ($existsPending) {
            return back()->with('warning', 'Anda masih memiliki pengajuan replikasi yang pending untuk tim ini.');
        }


        $data = $request->validate([
            'pic_name'      => ['required','string','max:191'],
            'pic_phone'     => ['required','string','max:30'],
            'unit_name'     => ['nullable','string','max:191'],
            'superior_name' => ['nullable','string','max:191'],
            'plant_name'    => ['nullable','string','max:191'],
            'area_location' => ['nullable','string','max:191'],
            'planned_date'  => ['nullable','date'],
            'return_to'     => ['nullable','url'],
        ]);

        $rep = \App\Models\ReplicationRequest::create([
            'team_id'          => $team->id,
            'paper_id'         => $paper->id,
            'innovation_title' => $paper->innovation_title,
            'pic_name'         => $data['pic_name'],
            'pic_phone'        => $data['pic_phone'],
            'unit_name'        => $data['unit_name'] ?? null,
            'superior_name'    => $data['superior_name'] ?? null,
            'plant_name'       => $data['plant_name'] ?? null,
            'area_location'    => $data['area_location'] ?? null,
            'planned_date'     => $data['planned_date'] ?? null,
            'status'           => 'pending',
            'created_by'       => Auth::id(),
        ]);

        // === email (seperti versi kamu sebelumnya) ===
        $leaderMember = $team->pvtMembers()->with('user')->where('status','leader')->first();
        $leaderEmail  = optional(optional($leaderMember)->user)->email;
        $leaderName   = optional(optional($leaderMember)->user)->name;

        $replicatorEmail = Auth::user()->email;
        $replicatorName  = Auth::user()->name;

        $superadmins = User::where('role','Superadmin')->pluck('email')->filter()->values()->all();

        $payload = [
            'innovation_title'   => $paper->innovation_title,
            'team_name'          => $team->team_name ?? ('Team #'.$team->id),
            'leader_name'        => $leaderName,
            'leader_email'       => $leaderEmail,
            'pic_name'           => $rep->pic_name,
            'pic_phone'          => $rep->pic_phone,
            'unit_name'          => $rep->unit_name,
            'superior_name'      => $rep->superior_name,
            'plant_name'         => $rep->plant_name,
            'area_location'      => $rep->area_location,
            'planned_date'       => $rep->planned_date,
            'submitted_by_name'  => $replicatorName,
            'submitted_by_email' => $replicatorEmail,
            'return_to'          => route('replications.index'),
        ];

        // try {
        //     if ($leaderEmail) Mail::to($leaderEmail)->send(new ReplicationSubmitted($payload));
        //     Mail::to($replicatorEmail)->send(new ReplicationSubmitted($payload));
        //     if (!empty($superadmins)) Mail::to($superadmins)->send(new ReplicationSubmitted($payload));
        // } catch (\Throwable $e) {
        //     Log::error('Email replikasi gagal', ['replication_id' => $rep->id, 'error' => $e->getMessage()]);
        //     return redirect()->route('replications.index')
        //         ->with('success', 'Pengajuan replikasi terkirim, namun email gagal dikirim.')
        //         ->with('warning', $e->getMessage());
        // }

        // redirect ke halaman baru
        return redirect()->route('replications.index')
            ->with('success','Pengajuan replikasi berhasil dikirim dan email notifikasi telah dikirim.');
    }

    public function approve(ReplicationRequest $replication)
    {
        abort_unless(Auth::user()->role === 'Superadmin', 403);
        if ($replication->status !== 'pending') {
            return back()->with('warning','Pengajuan ini sudah diproses.');
        }
        $replication->update(['status' => 'approved']);
        return back()->with('success','Pengajuan replikasi disetujui.');
    }

    public function reject(ReplicationRequest $replication)
    {
        abort_unless(Auth::user()->role === 'Superadmin', 403);
        if ($replication->status !== 'pending') {
            return back()->with('warning','Pengajuan ini sudah diproses.');
        }
        $replication->update(['status' => 'rejected']);
        return back()->with('success','Pengajuan replikasi ditolak.');
    }
}
