<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetodologiPaper;

class MetodologiPaperController extends Controller
{
    public function index()
    {
        $metodologiPapers = MetodologiPaper::all();
        return view('metodologi_papers.index', compact('metodologiPapers'));
    }

    public function create()
    {
        return view('metodologi_papers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'step' => 'required|integer',
            'max_user' => 'required|integer',
        ]);

        MetodologiPaper::create($request->all());

        return redirect()->route('management-system.metodologi_papers.index')->with('success', 'Metodologi Makalah berhasil dibuat.');
    }

    public function edit(MetodologiPaper $metodologiPaper)
    {
        return view('metodologi_papers.edit', compact('metodologiPaper'));
    }

    public function update(Request $request, MetodologiPaper $metodologiPaper)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'step' => 'required|integer',
            'max_user' => 'required|integer',
        ]);

        $metodologiPaper->update($request->all());

        return redirect()->route('management-system.metodologi_papers.index')->with('success', 'Metodologi Makalah berhasil diperbarui.');
    }

    public function destroy(MetodologiPaper $metodologiPaper)
    {
        $metodologiPaper->delete();

        return redirect()->route('management-system.metodologi_papers.index')->with('success', 'Metodologi Makalah berhasil dihapus.');
    }
}
