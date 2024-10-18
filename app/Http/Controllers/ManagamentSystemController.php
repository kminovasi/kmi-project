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

class ManagamentSystemController extends Controller
{
    //
    public function index()
    {
        return 'ok';
    }
    public function assignJuri()
    {
        $datas_event = Event::all();
        return view('auth.admin.management_system.assign_juri_index', ['datas_event' => $datas_event]);
    }
    public function assignJuriCreate()
    {
        $datas_user = User::all();
        $datas_event = Event::where('status', 'active')->get();
        return view('auth.admin.management_system.assign_juri', ['datas_user' => $datas_user, 'datas_event' => $datas_event]);
    }

    public function assignJuriStore(Judge $newAssignJudge, judgeRequest $request)
    {
        // dd($request->all());
        try {
            $year = Carbon::now()->year;
            DB::beginTransaction();

            $idEmployee = $request->input('employee_id');
            $userData = User::where('employee_id', $idEmployee)->select('role')->first();

            $newAssignJudge = Judge::create([
                'employee_id' => $request->input('employee_id'),
                'event_id' => $request->input('event_id'),
                'description' => $request->input('description'),
                // 'year' => $year
            ]);

            if ($userData->role == 'User') {
                $updateRole = User::where('employee_id', $request->input('employee_id'))
                    ->update([
                        'role' => 'Juri',
                    ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return redirect()->route('management-system.assign.juri.create')->withErrors('Error: ' . $e->getMessage());
        }
        return redirect()->route('management-system.assign.juri.create')->with('success', 'Assign Judge successful'); // masih belom tau
    }

    public function updateJuri(Request $request, $id)
    {
        dd($request->all());
    }
    public function revokeJuri(Request $request, $id)
    {
        try {

            $idEmployee = $request->input('employee_id');
            $userData = User::where('employee_id', $idEmployee)->select('role')->first();

            $dataTemplate = Judge::where('id', $id)
                ->update([
                    'status' => 'nonactive',
                ]);

            if ($userData->role == 'Juri') {
                $updateRole = User::where('employee_id', $request->input('employee_id'))
                    ->update([
                        'role' => 'User',
                    ]);
            }
            return redirect()->route('management-system.assign.juri')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('management-system.assign.juri')->withErrors('Error: ' . $e->getMessage());
        }
    }
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
        $datas_company = Company::all();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear, $currentYear + 10);
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
            // Validate the form data
            $validatedData = $request->validated();

            // Begin database transaction
            DB::beginTransaction();

            // Ensure 'company_code' is an array, even if only one value is provided
            $companyCodes = (array)$validatedData['company_code'];

            // Join the filtered company codes into a comma-separated string
            $companyCodesString = implode(',', $companyCodes);

            // Create the event with the combined company codes
            Event::create([
                'event_name' => $validatedData['event_name'],
                'date_start' => $validatedData['start_date'],
                'date_end' => $validatedData['end_date'],
                'year' => $validatedData['year'],
                'company_code' => $companyCodesString, // Store as comma-separated string
                'status' => 'not active',
                'description' => $validatedData['description'] ?? '', // Provide default value if not set
            ]);

            // Commit the transaction
            DB::commit();
            return redirect()->route('management-system.assign.event')->with('success', 'Event assigned successfully');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();
            return redirect()->route('management-system.assign.event.create')->withErrors('Error: ' . $e->getMessage());
        }
    }


    public function changeEvent(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $event = Event::findOrFail($id);  // Mengambil event berdasarkan ID
            $companyCode = $event->company_code;  // Mengambil kode perusahaan dari event

            // Jika status yang ingin diubah menjadi 'active'
            if ($request->status === 'active') {
                // Cek apakah ada event lain yang aktif pada perusahaan yang sama
                $activeEvent = Event::where('status', 'active')
                    ->where('company_code', $companyCode)
                    ->where('id', '!=', $id)
                    ->first();

                if ($activeEvent) {
                    return redirect()->route('management-system.assign.event')->withErrors('Error: Perusahaan hanya dapat memiliki satu event aktif pada satu waktu.');
                }
            }

            // Mengupdate status event
            $event->update([
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('management-system.assign.event')->with('success', 'Change Status Event successful');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('management-system.assign.event')->withErrors('Error: ' . $e->getMessage());
        }
    }



    public function updateEvent(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $event = Event::findOrFail($id);  // Using findOrFail to ensure the event exists
            $event->update([
                'event_name' => $request->input('event_name'),
                'date_start' => $request->input('start_date'),
                'date_end' => $request->input('end_date'),
                'year' => $request->input('year'),
                'company_code' => $request->input('company_code'),
                'status' => $request->input('status'),
                'description' => $request->input('description'),
            ]);
            DB::commit();
            return redirect()->route('management-system.assign.event')->with('success', 'Data Event updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('management-system.assign.event')->withErrors('Error: ' . $e->getMessage());
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
        // dd($request->all());
        try {
            // dd($request->all());
            DB::beginTransaction();
            User::where('employee_id', $request->employee_id)
                ->update([
                    'role' => $request->role,
                ]);
            Db::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return redirect()->route('management-system.role.assign.add')->withErrors('Error: ' . $e->getMessage());
        }
        return redirect()->route('management-system.role.assign.add')->with('success', 'Change  successful');
    }
    public function indexBOD()
    {
        return view('auth.admin.management_system.assign-role.bod-role-index');
    }
    public function createAssignBOD()
    {
        $data_team = Team::all();
        $datas_event = Event::whereIn('status', ['active', 'not active'])->get();
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

        return redirect()->route('management-system.role.bod.event.create')->with('success', 'Change successful');
    }

    public function indexInnovator()
    {
        return view('auth.admin.management_system.assign-role.innovator-role-index');
    }
    public function indexAdmin()
    {
        return view('auth.admin.management_system.assign-role.admin-role-index');
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
        return redirect()->route('management-system.team.category.index')->with('success', 'Data berhasil disimpan');
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

        return redirect()->route('management-system.team.category.index')->with('success', 'Data berhasil diperbarui');
    }
    public function categoryDelete($id)
    {

        $data = Category::findOrFail($id);
        if (!$data) {
            return redirect()->route('management-system.team.category.index')->with('error', 'Data tidak ditemukan');
        }
        $data->delete();
        return redirect()->route('management-system.team.category.index')->with('success', 'Data berhasil dihapus');
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
        return redirect()->route('management-system.team.theme.index')->with('success', 'Data berhasil disimpan');
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
        return redirect()->route('management-system.team.theme.index')->with('success', 'Data berhasil disimpan');
    }
    public function themeDelete($id)
    {

        $data = Theme::findOrFail($id);
        if (!$data) {
            return redirect()->route('management-system.team.theme.index')->with('error', 'Data tidak ditemukan');
        }
        $data->delete();
        return redirect()->route('management-system.team.theme.index')->with('success', 'Data berhasil dihapus');
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
