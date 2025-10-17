<?php

namespace App\Http\Controllers;

use App\Models\Prosedur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;


class ProsedurController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $data = Prosedur::when($q, fn($s) => $s->where('title','like',"%{$q}%"))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('prosedur.index', compact('data','q'));
    }

    public function create()
    {
        return view('prosedur.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => ['required','string','max:255'],
            'files'   => ['nullable','array'],
            'files.*' => ['file','max:10240'],
        ]);

        $filesMeta = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $f) {
                if (!$f->isValid()) continue;
                $stored = $f->store('prosedur', 'public');
                $filesMeta[] = [
                    'path' => $stored,
                    'name' => $f->getClientOriginalName(),
                    'mime' => $f->getClientMimeType(),
                    'size' => $f->getSize(),
                ];
            }
        }

        Prosedur::create([
            'employee_id' => optional(\Auth::user())->employee_id,
            'title'       => $request->title,
            'file_path'   => $filesMeta, // JSON
        ]);

        return redirect()->route('prosedur.index')->with('success','Prosedur berhasil dibuat.');
    }

    public function edit(Prosedur $prosedur)
    {
        return view('prosedur.edit', compact('prosedur'));
    }

    public function update(Request $request, Prosedur $prosedur)
    {
        $request->validate([
            'title'   => ['required','string','max:255'],
            'files'   => ['nullable','array'],
            'files.*' => ['nullable','file','max:10240'],
        ]);

        $meta = $prosedur->files; 
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $f) {
                if (!$f || !$f->isValid()) continue;
                $stored = $f->store('prosedur', 'public');
                $meta[] = [
                    'path' => $stored,
                    'name' => $f->getClientOriginalName(),
                    'mime' => $f->getClientMimeType(),
                    'size' => $f->getSize(),
                ];
            }
        }

        $prosedur->update([
            'title'     => $request->title,
            'file_path' => $meta,
        ]);

        return redirect()->route('prosedur.index')->with('success','Prosedur diperbarui.');
    }

    public function destroy(Prosedur $prosedur)
    {
        foreach ($prosedur->files as $pf) {
            if (!empty($pf['path'])) \Storage::disk('public')->delete($pf['path']);
        }
        $prosedur->delete();
        return redirect()->route('prosedur.index')->with('success','Prosedur dihapus.');
    }

    public function destroyFile(Prosedur $prosedur, int $index)
    {
        $meta = $prosedur->files;
        if (!array_key_exists($index, $meta)) abort(404);

        $file = $meta[$index];
        if (!empty($file['path'])) \Storage::disk('public')->delete($file['path']);

        array_splice($meta, $index, 1); 
        $prosedur->update(['file_path' => array_values($meta)]);

        return back()->with('success','File dihapus.');
    }

    public function show(Prosedur $prosedur)
    {
        return view('prosedur.show', compact('prosedur'));
    }

    public function getFile(Prosedur $prosedur, int $index)
    {
        $meta = $prosedur->files;
        if (!array_key_exists($index, $meta)) abort(404,'File tidak ditemukan.');

        $f = $meta[$index];
        $path = storage_path('app/public/'.$f['path']);
        if (!file_exists($path)) abort(404,'File fisik tidak ditemukan.');

        return response()->file($path, [
            'Content-Type'        => $f['mime'] ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.($f['name'] ?? basename($path)).'"',
        ]);
    }
}
