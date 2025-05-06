<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ImportUserData;
use Maatwebsite\Excel\Facades\Excel;


class ImportUserDataExcel extends Controller
{
    public function importUserExcel(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls',
    ]);

    Excel::import(new ImportUserData, $request->file('file'));

    return back()->with('success', 'User berhasil diimpor atau diperbarui.');
}
}