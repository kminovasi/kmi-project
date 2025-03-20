<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Judge;
use App\Models\BodEvent;
use App\Models\Company;
use App\Models\BodEventValue;
use App\Models\Team;
use App\Models\Category;
use App\Models\Theme;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\eventRequest;
use App\Http\Requests\judgeRequest;
use Illuminate\Support\Facades\DB;
use App\Models\getEvents;
use Illuminate\Support\Facades\Log as FacadesLog;
use Log;
use Yajra\DataTables\Facades\DataTables;

class ManagamentSystemController extends Controller
{
    public function assignEvent()
    {
        $datas_company = Company::all();
        $datas_event = Event::all();
        $currentYear = Carbon::now()->year;
        $years = [];

        for ($i = $currentYear; $i <= $currentYear + 10; $i++) {
            $years[$i] = $i;
        }
        return view('auth.admin.management_system.assign_event_index', [
            'datas_company' => $datas_company,
            'datas_event' => $datas_event,
            'years'         => $years
        ]);
    }


    public function assignEventCreate()
    {
        $isSuperadmin = Auth::user()->role === 'Superadmin';
        $company_code = Auth::user()->company_code;
        if ($isSuperadmin) {
            $datas_company = Company::all();
        } else {
            $datas_company = Company::where('company_code', $company_code)->get();
        }
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 7, $currentYear + 3);
        return view('auth.admin.management_system.assign_event', [
            'datas_company' => $datas_company,
            'years' => $years
        ]);
    }

    public function getEventsByCompany(Request $request)
    {
        $companyCode = $request->input('company_code');

        // Ambil data event yang sesuai dengan company code
        $datas_event = Event::where('company_code', $companyCode)->get();

        // Kembalikan data event dalam format JSON
        return response()->json($datas_event);
    }

    public function assignEventStore(eventRequest $request)
    {
        try {
            // Validasi data dari request
            $validatedData = $request->validated();
            FacadesLog::info('Validated Data:', $validatedData);

            // Mulai transaksi database
            DB::beginTransaction();

            // Buat event baru
            $event = Event::create([
                'event_name' => $validatedData['event_name'],
                'date_start' => $validatedData['start_date'], // Gunakan 'date_start'
                'date_end' => $validatedData['end_date'],     // Gunakan 'date_end'
                'year' => $validatedData['year'],
                'status' => 'not active',
                'description' => $request['description'] ?? '', // Default jika deskripsi tidak ada
                'type' => $validatedData['type'], // Tambahkan 'type'
            ]);

            // Handle "select_all" option
            $companyIds = array_filter($validatedData['company_code'], function($value) {
                return $value !== 'select_all';
            });

            // Hubungkan perusahaan ke event melalui tabel pivot
            $event->companies()->attach($companyIds);

            // Commit transaksi
            DB::commit();

            return redirect()->route('management-system.assign.event')->with('success', 'Event Telah Berhasil Dibuat');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollback();
            return redirect()->route('management-system.assign.event.create')->withErrors('Error: ' . $e->getMessage());
        }
    }


    public function changeEvent(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Ambil event berdasarkan ID
            $event = Event::findOrFail($id);

            // Ambil semua perusahaan terkait event
            $companies = $event->companies;

            // Jika status yang ingin diubah menjadi 'active'
            if ($request->status === 'active') {
                foreach ($companies as $company) {
                    // Cek apakah ada event lain yang aktif untuk perusahaan yang sama
                    $activeEventExists = DB::table('events')
                        ->join('company_event', 'events.id', '=', 'company_event.event_id')
                        ->where('company_event.company_id', $company->id)
                        ->where('events.status', 'active')
                        ->where('events.id', '!=', $id) // Hindari ambiguitas dengan menambahkan alias tabel
                        ->exists();

                    if ($activeEventExists) {
                        return redirect()->route('management-system.assign.event')
                            ->withErrors('Error: Perusahaan hanya dapat memiliki satu event aktif pada satu waktu.');
                    }
                }
            }

            // Update status event
            $event->update([
                'status' => $request->status,
            ]);

            DB::commit();

            return redirect()->route('management-system.assign.event')->with('success', 'Event Telah Berhasil Diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('management-system.assign.event')->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function updateEvent(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find the event
            $event = Event::findOrFail($id);

            // Basic event data update
            $event->update([
                'event_name' => $request->input('event_name'),
                'date_start' => $request->input('start_date'),
                'date_end' => $request->input('end_date'),
                'year' => $request->input('year'),
                'description' => $request->input('description'),
                'type' => $request->input('type'),
            ]);

            // Handle company relationships based on event type
            if ($request->input('type') === 'AP') {
                // For AP type, handle single company
                $companyId = $request->input('company_code');
                if (!is_array($companyId)) {
                    $companyId = [$companyId];
                }
                $event->companies()->sync($companyId);
            } else {
                // For other types, handle multiple companies
                $companyIds = $request->input('company_code', []);

                // If "select_all" is included, get all company IDs
                if (in_array('select_all', $companyIds)) {
                    $companyIds = Company::pluck('id')->toArray();
                }

                // Remove 'select_all' from the array if it exists
                $companyIds = array_filter($companyIds, function($value) {
                    return $value !== 'select_all';
                });

                $event->companies()->sync($companyIds);
            }

            DB::commit();
            return redirect()->route('management-system.assign.event')
                ->with('success', 'Event Telah Berhasil Diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('management-system.assign.event')
                ->withErrors('Error: ' . $e->getMessage());
        }
    }


    // Assign Role
    public function indexRole()
    {
        return view('auth.admin.management_system.assign-role.index');
    }
    public function roleAssignAdd()
    {
        return view('auth.admin.management_system.assign-role.add');
    }
    public function roleAssignStore(Request $request)
    {
        try {

            $currentUser = auth()->user();

            // Jika user yang melakukan pergantian adalah Superadmin
            if ($currentUser->role === 'Superadmin') {
                // Hitung jumlah Superadmin yang tersisa
                $superadminCount = User::where('role', 'Superadmin')->count();

                // Jika user yang akan diganti adalah Superadmin
                if ($request->role !== 'Superadmin') {
                    // Pastikan masih ada setidaknya 1 Superadmin di database
                    if ($superadminCount <= 1) {
                        return redirect()
                            ->route('management-system.role.assign.add')
                            ->withErrors('Tidak dapat mengganti role. Minimal harus ada 1 Superadmin.');
                    }
                }
            }

            DB::beginTransaction();
            User::where('employee_id', $request->employee_id)
                ->update([
                    'role' => $request->role,
                ]);
            Db::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('management-system.role.assign.add')->withErrors('Error: ' . $e->getMessage());
        }
        return redirect()->route('management-system.role.assign.add')->with('success', 'Pembaruan Role User Berhasil');
    }

    public function indexBOD()
    {
        return view('auth.admin.management_system.assign-role.bod-role-index');
    }

    public function createAssignBOD()
    {
        $data_team = Team::all();
        
        $datas_event = Event::whereHas('companies', function ($query) {
                $query->where('company_code', auth()->user()->company_code);
            })
            ->where('status', ['active', 'not active'])
            ->get();
        return view('auth.admin.management_system.assign-role.bod-role-add', ['datas_event' => $datas_event]);
    }

    public function storeAssignBOD(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi: Periksa apakah employee sudah terdaftar sebagai BOD untuk event yang sama
            $existingBodEvent = BodEvent::where('employee_id', $request->input('employee_id'))
                ->where('event_id', $request->input('event_id'))
                ->first();

            if ($existingBodEvent) {
                return redirect()->route('management-system.role.bod.event.create')
                    ->withErrors('Error: Employee sudah terdaftar sebagai BOD untuk event ini.');
            }

            // Jika tidak ada, lanjutkan untuk menyimpan BOD baru
            BodEvent::create([
                'employee_id' => $request->input('employee_id'),
                'event_id' => $request->input('event_id'),
                'category' => $request->input('category'),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('management-system.role.bod.event.create')->withErrors('Error: ' . $e->getMessage());
        }

        return redirect()->route('management-system.role.bod.event.create')->with('success', 'BOD Event Berhasil Dibuat');
    }

    public function indexInnovator()
    {
        return view('auth.admin.management_system.assign-role.innovator-role-index');
    }

    public function indexAdmin(Request $request)
    {
        // Get list of companies for filter dropdown
        $companies = Company::orderBy('company_name')->get();

        if (request()->ajax()) {
            $query = User::query()
                ->where('role', 'Admin')
                ->select([
                    'id',
                    'name',
                    'company_name',
                    'position_title',
                    'department_name',
                    'job_level',
                ]);

            // Filter based on user role
            if (Auth::user()->role === 'Admin') {
                // If Admin, only show users from same company
                $query->where('company_code', Auth::user()->company_code);
            } else if (Auth::user()->role === 'Superadmin' && $request->company_code) {
                // If Super Admin and company filter is applied
                $query->where('company_code', $request->company_code);
            }

            $users = $query->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($row) {
                    return '';
                })
                ->toJson();
        }

        return view('auth.admin.management_system.assign-role.admin-role-index', compact('companies'));
    }

    public function indexSuperAdmin()
    {
        return view('auth.admin.management_system.assign-role.superadmin-role-index');
    }

    public function categoryIndex()
    {
        return view('auth.admin.management_system.team.category');
    }

    public function categoryStore(Request $request)
    {
        try {
            DB::beginTransaction();
            Category::create([
                'category_name' => $request->input('category_name'),
                'category_parent' => $request->input('category_parent'),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return redirect()->route('management-system.team.category.index');
        }
        return redirect()->route('management-system.team.category.index')->with('success', 'Kategori Telah Berhasil Dibuat');
    }

    public function categoryUpdate(Request $request, $id)
    {
        //dd($request->all());
        try {
            DB::beginTransaction();
            $data = Category::findOrFail($id);

            $data->update([
                // 'point' => $request->point,
                'category_name' => $request->category_name,
                'category_parent' => $request->category_parent,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }

        return redirect()->route('management-system.team.category.index')->with('success', 'Katgori Telah berhasil diperbarui');
    }

    public function categoryDelete($id)
    {

        $data = Category::findOrFail($id);
        if (!$data) {
            return redirect()->route('management-system.team.category.index')->with('error', 'Data tidak ditemukan');
        }
        $data->delete();
        return redirect()->route('management-system.team.category.index')->with('success', 'Kategori Telah berhasil dihapus');
    }

    public function themeIndex()
    {
        return view('auth.admin.management_system.team.theme');
    }

    public function themeStore(Request $request)
    {

        try {
            DB::beginTransaction();
            Theme::create([
                'theme_name' => $request->input('theme_name'),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }
        return redirect()->route('management-system.team.theme.index')->with('success', 'Tema Telah berhasil Dibuat');
    }

    public function themeUpdate(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = Theme::findOrFail($id);

            $data->update([
                'theme_name' => $request->theme_name,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }
        return redirect()->route('management-system.team.theme.index')->with('success', 'Tema Telah Berhasil Diperbarui');
    }

    public function themeDelete($id)
    {

        $data = Theme::findOrFail($id);
        if (!$data) {
            return redirect()->route('management-system.team.theme.index')->with('error', 'Data tidak ditemukan');
        }
        $data->delete();
        return redirect()->route('management-system.team.theme.index')->with('success', 'Tema Telah Berhasil Dihapus');
    }

    public function companyIndex()
    {
        return view('auth.admin.management_system.team.company');
    }
    
    public function companyStore(Request $request)
    {

        try {
            DB::beginTransaction();
            Company::create([
                'company_code' => $request->input('company_code'),
                'company_name' => $request->input('company_name'),
                'group' => $request->input('group'),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return redirect()->route('management-system.team.company.index');
        }
        return redirect()->route('management-system.team.company.index')->with('success', 'Data berhasil disimpan');
    }

    public function companyUpdate(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = Company::findOrFail($id);

            $data->update([
                'company_code' => $request->company_code,
                'company_name' => $request->company_name,
                'group' => $request->group,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }
        return redirect()->route('management-system.team.company.index')->with('success', 'Data berhasil disimpan');
    }

    public function companyDelete($id)
    {

        $data = Company::findOrFail($id);
        if (!$data) {
            return redirect()->route('management-system.team.company.index')->with('error', 'Data tidak ditemukan');
        }
        $data->delete();
        return redirect()->route('management-system.team.company.index')->with('success', 'Data berhasil dihapus');
    }
}