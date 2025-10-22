<?php

namespace App\Http\Controllers;

use App\Models\LearnShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use App\Mail\LearnShareSubmitted;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
// use Log;

class LearnShareController extends Controller
{
    public function index()
    {
        $items = LearnShare::latest('created_at')->paginate(10);

        $items->getCollection()->transform(function ($row) {
            $ids = array_filter($row->speakers ?? []);
            $row->speaker_users = empty($ids) ? collect()
                : User::whereIn('employee_id', $ids)->orderBy('name')->get(['employee_id','name','unit_name','department_name']);
            return $row;
        });

        return view('learnshare.index', compact('items'));
    }

    public function create()
    {
        $speakerOptions = User::query()
            ->whereNotNull('employee_id')->where('employee_id','!=','')
            ->orderBy('name')
            ->get(['employee_id','name','unit_name','department_name']);

        $auth = auth()->user();
        $currentOrgLabel = $auth
            ? collect([
                $auth->group_function_name,
                $auth->department_name,
                $auth->unit_name,
                $auth->section_name,
            ])->filter()->implode(' / ')
            : null;

        $orgOptions = Cache::remember('ls_org_options_v1', 60 * 60 * 12, function () {
            return User::query()
                ->select('group_function_name','department_name','unit_name','section_name')
                ->where(function($q){
                    $q->whereNotNull('group_function_name')
                    ->orWhereNotNull('department_name')
                    ->orWhereNotNull('unit_name')
                    ->orWhereNotNull('section_name');
                })
                ->distinct()
                ->orderBy('group_function_name')
                ->orderBy('department_name')
                ->orderBy('unit_name')
                ->orderBy('section_name')
                ->get()
                ->map(function ($u) {
                    $label = collect([
                        $u->group_function_name,
                        $u->department_name,
                        $u->unit_name,
                        $u->section_name,
                    ])->filter()->implode(' / ');
                    $label = $label !== '' ? $label : '(Tidak terdefinisi)';
                    return ['value' => $label, 'label' => $label];
                })
                ->unique('value')
                ->values();
        });
        return view('learnshare.create', compact('speakerOptions','orgOptions','currentOrgLabel'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'                                  => ['required','string','max:255'],
            'job_function'                           => ['nullable','string','max:255'],
            'competency'                             => ['nullable','string','max:255'],
            'requesting_department'                  => ['required','string','max:255'],
            'scheduled_at'                           => ['required','date'],
            'objective'                              => ['required','string'],
            'opening_speech_employee_id'             => ['nullable','string','exists:users,employee_id'],
            'speakers'                               => ['nullable','array'],
            'speakers.*'                             => ['string','max:50','exists:users,employee_id'],
            'speakers_payload'                       => ['nullable','array'],
            'speakers_payload.*.type'                => ['nullable','in:employee,outsource'],
            'speakers_payload.*.employee_id'         => ['nullable','string','max:50'],
            'speakers_payload.*.name'                => ['nullable','string','max:255'],
            'speakers_payload.*.institution'         => ['nullable','string','max:255'],
            'speakers_payload.*.title'               => ['nullable','string','max:255'],
            'speakers_payload.*.email'               => ['nullable','email','max:255'],
            'ls_files'   => ['nullable'],
            'ls_files.*' => ['file','max:10240','mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/png,image/jpeg,application/zip,application/x-rar-compressed'],
        ]);

        // Opening Speech
        $data['employee_id']    = optional(Auth::user())->employee_id;
        $data['opening_speech'] = $request->input('opening_speech_employee_id') ?: null;
        unset($data['opening_speech_employee_id']);

        //  Pembicara
        $normalizedSpeakers = [];
        if (is_array($request->input('speakers_payload'))) {
            foreach ($request->input('speakers_payload') as $item) {
                $type = $item['type'] ?? null;
                if ($type === 'employee') {
                    $emp = trim((string)($item['employee_id'] ?? ''));
                    if ($emp !== '' && User::where('employee_id', $emp)->exists()) {
                        $normalizedSpeakers[] = $emp;
                    }
                } elseif ($type === 'outsource') {
                    $name  = trim((string)($item['name'] ?? ''));
                    $inst  = trim((string)($item['institution'] ?? ''));
                    $title = trim((string)($item['title'] ?? ''));
                    $email = trim((string)($item['email'] ?? ''));
                    if ($name !== '' || $email !== '') {
                        $normalizedSpeakers[] = 'OUT::'.$name.'::'.$inst.'::'.$title.'::'.$email;
                    }
                }
            }
        } else {
            $normalizedSpeakers = array_values(array_filter($data['speakers'] ?? []));
        }
        $data['speakers'] = $normalizedSpeakers;
        unset($data['speakers_payload'], $data['speakers']); 
        $data['speakers'] = $normalizedSpeakers;

        // Status
        $data['status'] = 'Pending';

        $row = LearnShare::create($data);

        $stored   = [];
        $uploaded = Arr::wrap($request->file('ls_files')); 
        foreach ($uploaded as $file) {
            if ($file) {
                $stored[] = $file->store("learnshare/{$row->id}", 'public');
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn($row->getTable(), 'attachments')) {
            $row->attachments = array_values(array_filter((array)($row->attachments ?? [])));
            $row->attachments = array_merge($row->attachments, $stored);
            $row->save();
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn($row->getTable(), 'files')) {
            $row->files = array_values(array_filter((array)($row->files ?? [])));
            $row->files = array_merge($row->files, $stored);
            $row->save();
        }
        
        try {
            $emails = User::query()
                ->whereIn('role', ['Superadmin', 'Admin'])
                ->whereNotNull('email')
                ->pluck('email')
                ->unique()
                ->values()
                ->all();

            if (!empty($emails)) {
                Mail::to($emails)->queue(new LearnShareSubmitted($row));
            }
        } catch (\Throwable $e) {
            \Log::warning('Notif L&S email skipped: '.$e->getMessage());
        }

        return redirect()->route('learnshare.show', $row)
            ->with('success', 'Pengajuan L&S tersimpan.');
    }

    public function show(LearnShare $learnShareRequest)
    {
        $learnshare = $learnShareRequest->load('requester'); 
        $ids = array_filter($learnshare->speakers ?? []);
        $speakerUsers = empty($ids) ? collect()
            : User::whereIn('employee_id', $ids)->orderBy('name')->get(['employee_id','name','unit_name','department_name']);

        return view('learnshare.show', compact('learnshare','speakerUsers'));
    }

    public function updateStatus(Request $request, LearnShare $learnshare)
    {
        if (!auth()->check() || auth()->user()->role !== 'Superadmin') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status'       => ['required', 'in:Pending,Approved,Rejected'],
            'comment'      => ['required', 'string', 'min:3'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $payload = [
            'status'         => $validated['status'],
            'status_comment' => $validated['comment'],
        ];
        if (!empty($validated['scheduled_at'])) {
            $payload['scheduled_at'] = $validated['scheduled_at'];
        }

        $learnshare->update($payload);

        return back()->with('success', "Status diperbarui menjadi {$validated['status']}.");
    }

    public function file(LearnShare $learnshare, string $token)
    {
        try {
            $path = decrypt($token);
        } catch (\Throwable $e) {
            abort(404);
        }

        $expectedPrefix = "learnshare/{$learnshare->id}/";
        if (!Str::startsWith($path, $expectedPrefix)) {
            abort(403, 'Forbidden');
        }

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        $filename = basename($path);
        $mime     = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';

        return Storage::disk('public')->response($path, $filename, [
            'Content-Type' => $mime,
        ]);
    }

    public function edit(LearnShare $learnShareRequest)
    {
        $learnshare = $learnShareRequest->load('requester');
        $files = [];
        try {
            if (Schema::hasColumn($learnshare->getTable(),'attachments') && is_array($learnshare->attachments)) {
                $files = $learnshare->attachments;
            } elseif (Schema::hasColumn($learnshare->getTable(),'files') && is_array($learnshare->files)) {
                $files = $learnshare->files;
            } else {
                $files = Storage::disk('public')->files("learnshare/{$learnshare->id}");
            }
        } catch (\Throwable $e) {
            $files = [];
        }

        return view('learnshare.edit', compact('learnshare','files'));
    }

    public function update(Request $request, LearnShare $learnShareRequest)
    {
        $row = $learnShareRequest;
        $data = $request->validate([
            'title'                 => ['required','string','max:255'],
            'job_function'          => ['nullable','string','max:255'],
            'competency'            => ['nullable','string','max:255'],
            'requesting_department' => ['required','string','max:255'],
            'scheduled_at'          => ['nullable','date'],
            'objective'             => ['required','string'],
            'opening_speech_employee_id' => ['nullable','string','exists:users,employee_id'],
            'speakers_payload'                      => ['nullable','array'],
            'speakers_payload.*.type'               => ['nullable','in:employee,outsource'],
            'speakers_payload.*.employee_id'        => ['nullable','string','max:50'],
            'speakers_payload.*.name'               => ['nullable','string','max:255'],
            'speakers_payload.*.institution'        => ['nullable','string','max:255'],
            'speakers_payload.*.title'              => ['nullable','string','max:255'],
            'speakers_payload.*.email'              => ['nullable','email','max:255'],

            'participants'   => ['nullable','array'],
            'participants.*' => ['nullable','string','max:255'],

            'ls_files'   => ['nullable'],
            'ls_files.*' => ['file','max:10240','mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/png,image/jpeg,application/zip,application/x-rar-compressed'],

            'delete_files'   => ['nullable','array'],
            'delete_files.*' => ['string'],

            'status'         => ['nullable', Rule::in(['Pending','Approved','Rejected'])],
            'status_comment' => ['nullable','string','max:10000'],
        ]);

        $opening = $request->input('opening_speech_employee_id') ?: null;
        $normalizedSpeakers = [];
        if (is_array($request->input('speakers_payload'))) {
            foreach ($request->input('speakers_payload') as $item) {
                $type = $item['type'] ?? null;
                if ($type === 'employee') {
                    $emp = trim((string)($item['employee_id'] ?? ''));
                    if ($emp !== '' && User::where('employee_id', $emp)->exists()) {
                        $normalizedSpeakers[] = $emp;
                    }
                } elseif ($type === 'outsource') {
                    $name  = trim((string)($item['name'] ?? ''));
                    $inst  = trim((string)($item['institution'] ?? ''));
                    $title = trim((string)($item['title'] ?? ''));
                    $email = trim((string)($item['email'] ?? ''));
                    if ($name !== '' || $email !== '') {
                        $normalizedSpeakers[] = 'OUT::'.$name.'::'.$inst.'::'.$title.'::'.$email;
                    }
                }
            }
        } else {
            $normalizedSpeakers = array_values(array_filter((array)$row->speakers ?? []));
        }

        $row->title                 = $data['title'];
        $row->job_function          = $data['job_function'] ?? null;
        $row->competency            = $data['competency'] ?? null;
        $row->requesting_department = $data['requesting_department'];
        $row->objective             = $data['objective'];
        $row->opening_speech        = $opening;
        $row->participants          = array_values(array_filter($data['participants'] ?? []));
        $row->speakers              = $normalizedSpeakers;

        if (auth()->user()->role === 'Superadmin') {
            if (!empty($data['scheduled_at'])) {
                $row->scheduled_at = $data['scheduled_at'];
            }
            if (!empty($data['status'])) {
                $row->status = $data['status'];
            }
            $row->status_comment = $data['status_comment'] ?? $row->status_comment;
        }

        $row->save();

        $toDelete = Arr::wrap($request->input('delete_files'));
        foreach ($toDelete as $path) {
            if (!Str::startsWith($path, "learnshare/{$row->id}/")) continue;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $stored   = [];
        $uploaded = Arr::wrap($request->file('ls_files'));
        foreach ($uploaded as $file) {
            if ($file) {
                $stored[] = $file->store("learnshare/{$row->id}", 'public');
            }
        }

        if (Schema::hasColumn($row->getTable(), 'attachments')) {
            $current = array_values(array_filter((array)($row->attachments ?? [])));
            $current = array_values(array_diff($current, $toDelete));
            $row->attachments = array_values(array_merge($current, $stored));
            $row->save();
        } elseif (Schema::hasColumn($row->getTable(), 'files')) {
            $current = array_values(array_filter((array)($row->files ?? [])));
            $current = array_values(array_diff($current, $toDelete));
            $row->files = array_values(array_merge($current, $stored));
            $row->save();
        }

        return redirect()->route('learnshare.show', $row->id)
            ->with('success', 'Pengajuan L&S berhasil diperbarui.');
    }

}