<?php

namespace App\Http\Controllers;

use App\Models\AssessmentMatrixImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssessmentMatrixController extends Controller
{
    public function index()
    {
        $images = AssessmentMatrixImage::latest()->paginate(10);
        return view('admin.assessment-matrix.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assessment-matrix', $fileName, 'public');

            AssessmentMatrixImage::create([
                'path' => $path
            ]);

            return redirect()->route('management-system.assessment-matrix.index')
                ->with('success', 'Gambar berhasil diunggah');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengunggah gambar');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:assessment_matrix_images,id'
        ]);

        $image = AssessmentMatrixImage::findOrFail($request->id);

        // Hapus file dari storage
        if (Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        // Hapus record dari database
        $image->delete();

        return redirect()->route('management-system.assessment-matrix.index')
            ->with('success', 'Gambar berhasil dihapus');
    }
}
