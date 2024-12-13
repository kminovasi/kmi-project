<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Log;
use Yajra\DataTables\DataTables;

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
                        $q->whereRaw("CAST(users.id AS TEXT) LIKE ?", ["%{$search}%"])
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
        $managers = User::whereIn('role', ['Manager', 'Superadmin', 'Admin'])->get();
        return view('management-system.user.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|unique:users,employee_id',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:Superadmin,Admin,Pengelola Inovasi,BOD,5,User'
        ], [
            'employee_id.unique' => 'Employee ID sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role harus dipilih.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userData = $request->except(['password', 'password_confirmation']);
        $userData['password'] = Hash::make($request->password);

        try {
            User::create($userData);
            return redirect()->route('management-system.user.index')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $managers = User::whereIn('role', ['Manager', 'Superadmin', 'Admin'])->get();
        return view('management-system.user.edit', compact('user', 'managers'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|unique:users,employee_id,' . $id,
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'name' => 'required',
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|in:Superadmin,Admin,Pengelola Inovasi,BOD,5,User',
        ], [
            'employee_id.unique' => 'Employee ID sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
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

        try {
            $user->update($userData);
            return redirect()->route('management-system.user.index')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
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
        try {
            $user = User::findOrFail($id);

            // Safely handle manager relationship
            $atasan = null;
            if ($user->manager_id && is_numeric($user->manager_id)) {
                $atasan = User::where('id', $user->manager_id)->first();
            }

            // Safely fetch subordinates
            $bawahan = User::where('manager_id', $user->id)
                ->when(!is_numeric($user->id), function ($query) {
                    return $query->where('1', '=', '0'); // Return empty collection if ID is not numeric
                })
                ->get();

            // Add related data to the user object
            $user->atasan = $atasan;
            $user->bawahan = $bawahan;

            return view('management-system.user.show', compact('user'));
        } catch (\Exception $e) {
            \Log::error('User show error', [
                'message' => $e->getMessage(),
                'user_id' => $id,
                'manager_id' => $user->manager_id ?? 'N/A'
            ]);

            return redirect()->route('management-system.user.index')
                ->with('error', 'User not found or invalid data');
        }
    }
}
