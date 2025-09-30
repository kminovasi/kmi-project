<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Log;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('management-system.user.index');
    }

    public function getData()
    {
        $query = User::select('users.*'); // Hilangkan eager loading 'atasan' jika tidak diperlukan
        $isSuperadmin = Auth::user()->role === 'Superadmin';
        $company_code = Auth::user()->company_code;

        // Jika bukan superadmin, tambahkan kondisi untuk membatasi data berdasarkan company_code
        if (!$isSuperadmin) {
            $query->where('users.company_code', $company_code);
        }

        // Handle DataTables server-side processing
        return DataTables::of($query)
            ->addColumn('actions', function ($user) {
                return view('management-system.user.actions', compact('user'));
            })
            ->addColumn('manager_name', function ($user) {
                return $user->atasan ? $user->atasan->name : '-';
            })
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request('search')['value'])) {
                    $search = request('search')['value'];

                    // Apply search filters, excluding search on 'atasan.name'
                    $query->where(function ($q) use ($search) {
                        $q->where('users.id', ["%{$search}%"])
                            ->orWhere('users.employee_id', 'LIKE', "%{$search}%")
                            ->orWhere('users.name', 'LIKE', "%{$search}%")
                            ->orWhere('users.email', 'LIKE', "%{$search}%")
                            ->orWhere('users.position_title', 'LIKE', "%{$search}%")
                            ->orWhere('users.role', 'LIKE', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['actions'])
            ->make(true);
    }


    public function create()
    {   
        $companies = \App\Models\Company::select('company_code', 'company_name')->get();
        $managers = User::whereIn('role', ['Manager', 'Superadmin', 'Admin'])->get();
        return view('management-system.user.create', compact('managers', 'companies'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|unique:users,employee_id',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4|confirmed',
            'role' => 'required|in:Superadmin,Admin,Pengelola Inovasi,BOD,User'
        ], [
            'employee_id.unique' => 'Employee ID sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 4 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role harus dipilih.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Ambil semua data kecuali password & konfirmasi
        $userData = $request->except(['password', 'password_confirmation']);

        // Hash password dan generate UUID
        $userData['password'] = Hash::make($request->password);
        $userData['uuid'] = Str::uuid()->toString();

        try {
            User::create($userData);
            return redirect()->route('management-system.user.index')
                ->with('success', 'User berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $managers = User::whereIn('role', ['Manager', 'Superadmin', 'Admin'])->get();
        $companies = \App\Models\Company::select('company_code', 'company_name')->get(); 
        return view('management-system.user.edit', compact('user', 'managers', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => [
                'required',
                Rule::unique('users', 'employee_id')->ignore($id),
            ],
            'username' => [
                'required',
                Rule::unique('users', 'username')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'name' => 'required',
            'password' => 'nullable|min:4|confirmed',
            'role' => 'required|in:Superadmin,Admin,Pengelola Inovasi,BOD,Juri,User',
        ], [
            'employee_id.unique' => 'Employee ID sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 4 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role harus dipilih.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->except(['password', 'password_confirmation']);

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        DB::beginTransaction();
        try {
            $oldEmployeeId = $user->employee_id;
            $newEmployeeId = $userData['employee_id'];
        
            // Update user duluan (wajib, karena FK tergantung pada dia)
            $user->update($userData);
        
            if ($oldEmployeeId !== $newEmployeeId) {
                // Update semua FK setelah user diganti
                DB::table('pvt_members')->where('employee_id', $oldEmployeeId)->update(['employee_id' => $newEmployeeId]);
                DB::table('judges')->where('employee_id', $oldEmployeeId)->update(['employee_id' => $newEmployeeId]);
                DB::table('bod_events')->where('employee_id', $oldEmployeeId)->update(['employee_id' => $newEmployeeId]);
                DB::table('teams')->where('gm_id', $oldEmployeeId)->update(['gm_id' => $newEmployeeId]);
                DB::table('users')->where('manager_id', $oldEmployeeId)->update(['manager_id' => $newEmployeeId]);
            }
        
            DB::commit();
            return back()->with('success', 'User berhasil diupdate.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal update: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

     public function show($id)
{
    // TRACE ID biar gampang cari rangkaian log di laravel.log
    $traceId = (string) \Illuminate\Support\Str::uuid();
    // \Log::debug('User.show:start', ['trace' => $traceId, 'param_id' => $id]);

    $user = null;

    try {
        $user = User::findOrFail($id);

        // --- LOG field penting & yang sering bikin null ---
        $interestingFields = [
            'id','employee_id','manager_id','name','email',
            'company_code','company_name','unit_name','department_name','directorate_name',
            'position_title','role'
        ];
        $nullOrEmpty = [];
        foreach ($interestingFields as $f) {
            $v = $user->{$f} ?? null;
            if (is_null($v) || $v === '') {
                $nullOrEmpty[] = $f;
            }
        }
        // \Log::debug('User.show:user_loaded', [
        //     'trace' => $traceId,
        //     'user_id' => $user->id,
        //     'employee_id' => $user->employee_id,
        //     'manager_id' => $user->manager_id,
        //     'company_code' => $user->company_code,
        //     'company_name' => $user->company_name,
        //     'null_or_empty_fields' => $nullOrEmpty,
        // ]);

        // --- Cek & log relasi ATASAN ---
        $atasan = null;
        $managerIdRaw = $user->manager_id;
        $managerIdIsNumeric = is_numeric($managerIdRaw);

        // \Log::debug('User.show:manager_check', [
        //     'trace' => $traceId,
        //     'manager_id_raw' => $managerIdRaw,
        //     'is_numeric' => $managerIdIsNumeric,
        //     'is_empty' => empty($managerIdRaw),
        // ]);

        if (!empty($managerIdRaw) && $managerIdIsNumeric) {
            $atasan = User::where('employee_id', $managerIdRaw)->first();
            // \Log::debug('User.show:atasan_query_result', [
            //     'trace'  => $traceId,
            //     'found'  => (bool) $atasan,
            //     'lookup' => ['by' => 'employee_id', 'value' => $managerIdRaw],
            // ]);
        } else {
            // \Log::warning('User.show:atasan_skipped_or_mismatch', [
            //     'trace'  => $traceId,
            //     'reason' => empty($managerIdRaw) ? 'manager_id_empty' : 'manager_id_not_numeric',
            // ]);
        }

        // --- Cek & log relasi BAWAHAN ---
        $empId = $user->employee_id;
        $empIdIsNumeric = is_numeric($empId);
        // \Log::debug('User.show:bawahan_prefetch', [
        //     'trace' => $traceId,
        //     'employee_id' => $empId,
        //     'is_numeric' => $empIdIsNumeric,
        // ]);

        $bawahanQuery = User::where('manager_id', $empId);
        if (!$empIdIsNumeric) {
            // Ini meniru logic-mu sekarang (blokir jika bukan numeric)
            $bawahanQuery->whereRaw('1 = 0');
            // \Log::warning('User.show:bawahan_blocked_non_numeric_empid', [
            //     'trace' => $traceId,
            //     'employee_id' => $empId,
            // ]);
        }

        $bawahan = $bawahanQuery->get();
        // \Log::debug('User.show:bawahan_result', [
        //     'trace' => $traceId,
        //     'count' => $bawahan->count(),
        //     'sample_ids' => $bawahan->pluck('id')->take(5),
        // ]);

        // Tempelkan ke object (buat view)
        $user->atasan  = $atasan ?? 'Tidak ada atasan';
        $user->bawahan = $bawahan;

        // \Log::debug('User.show:render_view', ['trace' => $traceId, 'view' => 'management-system.user.show']);
        return view('management-system.user.show', compact('user'));
    } catch (\Throwable $e) {
        // \Log::error('User.show:error', [
        //     'trace'    => $traceId,
        //     'message'  => $e->getMessage(),
        //     'param_id' => $id,
        //     // Jangan akses $user->xxx kalau belum terbentuk
        //     'user_loaded' => (bool) $user,
        // ]);
        return redirect()->route('management-system.user.index')
            ->with('error', 'User not found or invalid data');
    }
}

    public function getUserEvents($companyCode, $teamId)
{
    try {

        if($teamId == 153){
            $events = Event::where('status', 'active')->get();
        } else {
            if (!$companyCode) {
                return response()->json(['error' => 'User tidak terhubung ke perusahaan'], 404);
            }
                
            // Ambil event berdasarkan company_code
            $events = Event::whereHas('companies', function ($query) use ($companyCode) {
                $query->where('company_code', $companyCode);
            })
            ->where('status', 'active')
            ->get();
        }

        return response()->json([
            'success' => true,
            'events' => $events,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function getUsersWithCompany(Request $request)
{
    $employeeId = $request->query('employee_id');

    $user = User::where('employee_id', $employeeId)
        ->join('companies', 'companies.company_code', '=', 'users.company_code')
        ->select(
            'companies.company_name as co_name',
            'users.unit_name as unit_name',
            'users.department_name as department_name',
            'users.directorate_name as directorate_name'
        )
        ->first();

    if ($user) {
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 404);
    }
}
}