<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Imports\MembersImport;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    // Display the import form
    public function index()
    {
        return view('import');
    }
    // Import excel file to database
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        // check if the file is valid
        if (!$file->isValid()) {
            return back()->with('error', 'Invalid file.');
        }
        // truncate the table before importing new data
        // this is to avoid duplicate entries
        // do not check for foreign key constraints
        Schema::disableForeignKeyConstraints();
        Member::truncate();
        Excel::import(new MembersImport, $file);

        Schema::enableForeignKeyConstraints();

        return back()->with('success', 'Members imported successfully.');
    }

    // Export data to excel
    public function export()
    {
        return Excel::download(new MembersExport, date('Y-m-d').'members.xlsx');
    }
}
