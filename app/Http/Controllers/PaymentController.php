<?php

namespace App\Http\Controllers;

use App\Models\ActiveLoans;
use Illuminate\Http\Request;
use App\Models\PaymentCapture;
use App\Exports\PaymentsExport;
use App\Imports\PaymentsImport;
use App\Exports\ActiveLoansExport;
use App\Imports\ActiveLoansImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class PaymentController extends Controller
{
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
        Schema::disableForeignKeyConstraints();
        PaymentCapture::truncate();
        Excel::import(new PaymentsImport, $file);

        Schema::enableForeignKeyConstraints();

        return back()->with('success', 'Payment Ledgers imported successfully.');
    }

    // Export Payment capture data to excel
    public function export()
    {
        return Excel::download(new PaymentsExport, date('Y-m-d').'payments.xlsx');
    }

    // Export Active Loans data to excel
    public function exportActiveLoans()
    {
        return Excel::download(new ActiveLoansExport, date('Y-m-d').'activeloans.xlsx');
    }

    public function importActiveLoans(Request $request)
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
        Schema::disableForeignKeyConstraints();
        ActiveLoans::truncate();
        Excel::import(new ActiveLoansImport, $file);

        Schema::enableForeignKeyConstraints();

        return back()->with('success', 'Active Loans data imported successfully.');
    }
}
