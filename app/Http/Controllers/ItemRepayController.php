<?php

namespace App\Http\Controllers;

use App\Exports\RepayCaptureExport;
use App\Imports\RepayCaptureImport;
use App\Models\RepayCapture;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class ItemRepayController extends Controller
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
        RepayCapture::truncate();
        Excel::import(new RepayCaptureImport, $file);

        Schema::enableForeignKeyConstraints();

        return back()->with('success', 'Item Repayments imported successfully.');
    }

    // Export data to excel
    public function export()
    {
        return Excel::download(new RepayCaptureExport, date('Y-m-d').'item_repays.xlsx');
    }
}
