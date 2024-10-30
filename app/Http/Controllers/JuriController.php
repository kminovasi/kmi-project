<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Event;
use App\Models\Judge;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Storage;

class JuriController extends Controller
{
    function index(Request $request)
    {

        $user = Auth::user();
        $companyCode = $user->company_code;

        if ($user->role == 'Admin') {

            $judges = Judge::with('event')
                ->join('users', 'judges.employee_id', '=', 'users.employee_id')
                ->where('users.company_code', $companyCode)
                ->select(
                    'judges.*',
                    'users.name',
                    'users.employee_id',
                    'users.company_name',
                    'users.company_code',
                    'users.unit_name',
                )
                ->paginate(10);
        } else {

            $company = request()->input('company');
            $event = request()->input('event');
            $search = request()->input('search');

            $judges = Judge::with('event')
                ->join('users', 'judges.employee_id', '=', 'users.employee_id')
                ->select(
                    'judges.*',
                    'users.name',
                    'users.employee_id',
                    'users.company_name',
                    'users.company_code',
                    'users.unit_name',
                );

            if ($company) {
                $judges = $judges->where('company_code', $company);
            }

            if ($event) {
                $judges = $judges->where('event_id', $event);
            }

            if ($search) {
                $judges = $judges->where('users.name', 'ILIKE', '%' . $search . '%');
            }

            $judges = $judges->paginate(10);
        }

        return view('auth.admin.management_system.assign-juri.index', compact('judges'));
    }

    function create()
    {
        return view('auth.admin.management_system.assign-juri.create');
    }

    function store(Request $request)
    {

        $request->validate([
            'event' => 'required|exists:events,id',
            'employee_id' => 'required|exists:users,employee_id',
            'status' => 'required',
            'document' => 'required|mimes:pdf|max:2048',
        ]);

        // Cek apakah sudah terdaftar atau belum
        $exists = Judge::where('employee_id', $request->employee_id)
            ->where('event_id', $request->event)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['error' => 'Pegawai sudah terdaftar di event ini.']);
        }

        try {
            $judge = new Judge();
            $judge->event_id = $request->event;
            $judge->employee_id = $request->employee_id;
            $judge->status = $request->status;

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $path = public_path('/storage/surat-juri');
                $file->move($path, $filename);
                $judge->letter_path = $filename;
            };

            $judge->save();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Juri Gagal Ditambahkan');
        }

        return redirect(route('management-system.juri'))->with('success', 'Juri Berhasil Ditambahkan');
    }

    function destroy(Request $request)
    {

        try {
            $judge = Judge::find($request->id);

            $filePath = 'surat-juri/' . $judge->letter_path;

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            $judge->delete();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Juri Gagal Dihapus');
        }

        return redirect()->back()->with('success', 'Juri Berhasil Dihapus');
    }

    function edit($id, $name)
    {

        $judges = Judge::findOrFail($id);

        // dd($judges);

        return view('auth.admin.management_system.assign-juri.edit', compact('judges', 'name'));
    }

    function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'event' => 'required|exists:events,id',
            'document' => 'nullable|file|mimes:pdf|max:2048',
            'status' => 'required',
        ]);

        try {
            $judge = Judge::findOrFail($id);

            $judge->event_id = $validatedData['event'];
            $judge->status = $validatedData['status'];

            if ($request->hasFile('document')) {

                if ($judge->letter_path) {
                    $filePath = 'surat-juri/' . $judge->letter_path;

                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }

                $file = $request->file('document');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $path = public_path('/storage/surat-juri');
                $file->move($path, $filename);
                $judge->letter_path = $filename;
            };

            $judge->save();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Juri Gagal Diubah');
        }

        return redirect(route('management-system.juri'))->with('success', 'Juri Berhasil Diubah');
    }
}
