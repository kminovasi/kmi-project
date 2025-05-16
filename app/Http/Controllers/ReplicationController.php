<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User;
use App\Models\ReplicationInnovation;

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

    public function updateStatus($replicationId, Request $request)
    {
        $replication = ReplicationInnovation::findOrFail($replicationId);
        // Authorization check
        $this->authorize('update', $replication);
        
        $validated = $request->validate([
            'replication-status' => 'required|in:Pengajuan,Progres,Replikasi Berhasi,Replikasi Gagal',
        ]);

        try{
            DB::transaction(function () use ($replication, $validated) {
                // Update the replication status
                $replication->replication_status = $validated['replication-status'];
                
                $replication->save();
            });
        } catch( \Exception $e) {
            return redirect()->route('replication.index')->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('replication.index')->with('success', 'Status Berhasil Diupdate');
    }

    public function uploadNewsLetter($replicationId, Request $request)
    {
        $replication = ReplicationInnovation::findOrFail($replicationId);
        // Authorization check
        $this->authorize('update', $replication);

        $validated = $request->validate([
            'news-letter' => 'required|mimes:pdf|max:5120',
        ]);

        $fileExtension = $request->file('news-letter')->getClientOriginalExtension();
        $fileName = $this->generateFileName($replication, 'news_letter', $fileExtension);
        
        try{
            DB::transaction(function () use ($replication, $request, $fileName) {
                $filePath = $request->file('news-letter')->storeAs('replication/news_letter/', $fileName, 'private');
                
                $replication->event_news = $filePath;
                
                $replication->save();
            });
            
        } catch( \Exception $e) {
            return redirect()->route('replication.index')->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('replication.index')->with('success', 'Berita Acara Berhasil Diupload');
    }

    public function uploadBenefitAndEvidence($replicationId, Request $request)
    {
        $replication = ReplicationInnovation::findOrFail($replicationId);
        // Authorization check
        $this->authorize('update', $replication);
        
        $validated = $request->validate([
            'evidence' => 'required|mimes:pdf|max:5120',
            'benefit' => 'required|numeric',
        ]);

        $fileExtension = $request->file('evidence')->getClientOriginalExtension();
        $fileName = $this->generateFileName($replication, 'evidence', $fileExtension);

        try{
            DB::transaction(function () use ($replication, $validated, $request, $fileName) {
                // Store the evidence file
                $filePath = $request->file('evidence')->storeAs('replication/evidence/', $fileName, 'private');
                
                $replication->financial_benefit = $validated['benefit'];
                $replication->evidence = $filePath;
                
                $replication->save();
            });
        } catch( \Exception $e) {
            return redirect()->route('replication.index')->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('replication.index')->with('success', 'Berita Acara Berhasil Diupload');
    }

    public function uploadRewardDesc($replicationId, Request $request)
    {
        $replication = ReplicationInnovation::findOrFail($replicationId);
        // Authorization check
        $this->authorize('update', $replication);
        
        $validated = $request->validate([
            'reward' => 'required|mimes:pdf,png,jpg,jpeg|max:5120',
            'description' => 'required|string'
        ]);

        $fileExtension = $request->file('reward')->getClientOriginalExtension();

        $fileName = $this->generateFileName($replication, 'reward', $fileExtension);
        
        try{
            DB::transaction(function () use ($replication, $validated, $request, $fileName) {
                // Store the reward file
                $filePath = $request->file('reward')->storeAs('replication/reward/', $fileName, 'private');
                
                $replication->reward = $filePath;
                $replication->description = $validated['description'];
                
                $replication->save();
            });
        } catch( \Exception $e) {
            return redirect()->route('replication.index')->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('replication.index')->with('success', 'Reward dan Keterangan Berhasil Diupload');
    }

    public function viewDocument($replicationId, $type)
    {
        $replication = ReplicationInnovation::findOrFail($replicationId);
        // Authorization check
        $this->authorize('view', $replication);

        switch ($type) {
            case 'news_letter':
                $file = ltrim($replication->event_news, '/'); // hilangkan leading slash kalau ada
                $filePath = storage_path("app/private/{$file}");

                if (!file_exists($filePath)) {
                    abort(404, 'File not found.');
                }

                return response()->file($filePath);
            
            case 'evidence':
                $file = ltrim($replication->evidence, '/'); // hilangkan leading slash kalau ada
                $filePath = storage_path("app/private/{$file}");

                if (!file_exists($filePath)) {
                    abort(404, 'File not found.');
                }

                return response()->file($filePath);

            case 'reward':
                $file = ltrim($replication->reward, '/'); // hilangkan leading slash kalau ada
                $filePath = storage_path("app/private/{$file}");

                if (!file_exists($filePath)) {
                    abort(404, 'File not found.');
                }

                return response()->file($filePath);

            case 'description':
                return response()->json([
                    'description' => $replication->description,
                ]);

            default:
                abort(404);
        }
    }

    private function generateFileName($replication, $prefix, $extension)
    {
        $picName = str_replace(' ', '_', $replication->personInCharge->name);
        $picCompany = str_replace(' ', '_', $replication->company->company_name);
        $randomNumber = mt_rand(1000, 9999);
        return "{$picName}_{$picCompany}_{$prefix}_{$randomNumber}.{$extension}";
    }

}