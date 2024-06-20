<?php

namespace App\Http\Controllers;

use App\Exports\ItemCaptureExport;
use App\Imports\ItemCaptureImport;
use App\Models\ItemCapture;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class ItemCaptureController extends Controller
{
    // Display the import form
    public function index()
    {
        return view('import_business');
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
        ItemCapture::truncate();
        Excel::import(new ItemCaptureImport, $file);

        Schema::enableForeignKeyConstraints();

        return back()->with('success', 'Item Captures imported successfully.');
    }

    // Export data to excel
    public function export()
    {
        return Excel::download(new ItemCaptureExport, date('Y-m-d').'itemcaptures.xlsx');
    }
}
