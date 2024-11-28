<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('management-system.user.index');
    }

    public function getData()
    {
        $query = User::with('atasan')->select('users.*');

        return DataTables::of($query)
            ->addColumn('actions', function ($user) {
                return view('management-system.user.actions', compact('user'));
            })
            ->addColumn('manager_name', function ($user) {
                return $user->atasan ? $user->atasan->name : '-';
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
        $validatedData = $request->validate([
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
        if ($validatedData->fails()) {
            return redirect()->back()
                ->withErrors($validatedData)
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
        $user = User::with(['atasan', 'bawahan'])->findOrFail($id);
        return view('management-system.user.show', compact('user'));
    }
}
