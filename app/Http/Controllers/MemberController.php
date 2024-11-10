<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Exports\MembersExport;
use App\Imports\MembersImport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

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
        set_time_limit(0);
        Member::whereDoesntHave('user')->chunk(100, function ($members) {
            DB::beginTransaction();
            try {
                foreach ($members as $member) {
                    
                    User::create([
                        'name' => $member->surname ?? 'User',
                        'username' => 'albirru'.$member->coopId,
                        'password' => Hash::make('password@'.$member->coopId),
                        'coopId' => $member->coopId,
                    ])->assignRole('member');
        
                }

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return 'An error occured while generating login details.';
            }
        });

        
        
        return 'Login details generated successfully.';
    }

    public function generateLoginDetailsOld()
    {
        $members = Member::all();

        try {
            foreach ($members as $member) {
                // check if the member already has login details
                if (User::where('username', $member->coopId)->exists()) {
                    continue;
                }
                $user = User::create([
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
