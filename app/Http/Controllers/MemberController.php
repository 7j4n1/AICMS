<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Imports\MembersImport;
use App\Models\Admin;
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


    public function generateLoginDetails()
    {
        $members = Member::all();

        try {
            foreach ($members as $member) {
                // check if the member already has login details
                if (Admin::where('username', $member->coopId)->exists()) {
                    continue;
                }
                $user = Admin::create([
                    'name' => $member->surname ?? 'User',
                    'username' => $member->coopId,
                    'password' => bcrypt('password@'.$member->coopId),
                ])->assignRole('member');
    
            }
        } catch (\Throwable $th) {
            return 'An error occured while generating login details.';
        }
        
        
        return 'Login details generated successfully.';
    }
}
