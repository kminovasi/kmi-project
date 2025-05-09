<?php

namespace App\Http\Controllers;

use App\Models\ReplicationInnovation;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;

class ReplicationController extends Controller
{
    public function index()
    {
        return view('auth.admin.replication.index');
    }

    public function autocompleteEmployee(Request $request)
    {
        $query = $request->get('query');
        $data = User::where('name', 'like', '%' . $query . '%')
            ->orWhere('employee_id', 'like', '%' . $query . '%')
            ->select('id', 'name', 'employee_id', 'company_code', 'company_name')
            ->take(7)
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        
        try{
            $validated = $request->validate([
                'title_id' => 'required|exists:papers,id',
                'pic_id' => 'required|exists:users,id',
                'company_code' => 'required|exists:companies,company_code',
            ]);
            ReplicationInnovation::create([
                'paper_id' => $validated['title_id'],
                'person_in_charge' => $validated['pic_id'],
                'company_code' => $validated['company_code'],
                'replication_status' => 'Pengajuan',
            ]);
            
            return redirect()->back()->with('success', 'Data Pengajuan Berhasil Ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}