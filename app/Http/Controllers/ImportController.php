<?php

namespace App\Http\Controllers;

use SplFileObject;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; // Laravel Excel package

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }
    public function import(Request $request)
    {
        if ($request->hasFile('csvfile')) {
            $file = $request->file('csvfile');
            $filePath = $file->getRealPath();
      
            // Open CSV file for reading
            $csv = new SplFileObject($filePath, 'r');
            $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY); // Set CSV reading flags
      
            // Skip header row (optional)
            if ($csv->fgets()) { // Read and discard the first line (assuming header)
            }
      
            $dataArray = [];
            while (!$csv->eof()) {
              $row = $csv->fgetcsv();
              if ($row) {
                // Assuming same number of columns in all rows
                $uniqueId = $row[0]; // Replace with your actual unique field index
      
                // Check if record already exists
                $existingRecord = DB::table('members')
                  ->where('coopId', $uniqueId)
                  ->first();
      
                if (!$existingRecord) {
                  // Insert new record
                    if((!is_null($row[0]) && !empty($row[0])) || (!is_null($row[1]) && !empty($row[1])))
                    {
                        $year = null;
                        if (!empty($row[11])) {
                            $year = $row[11];
                        }
                        $dataArray[] = [
                        'coopId' => $row[0],
                        'surname' => $row[1],
                        'otherNames' => $row[2],
                        'occupation' => $row[3],
                        'gender' => $row[4],
                        'religion' => $row[5],
                        'phoneNumber' => $row[6],
                        'accountNumber' => $row[7],
                        'bankName' => $row[8],
                        'nextOfKinName' => $row[9],
                        'nextOfKinPhoneNumber' => $row[10],
                        'yearJoined' => $year,
                        'userId' => auth('admin')->user()->id, // Retrieve admin id
                        ];
                    }
                }
              }
            }
      
            // Insert new records (if any)
            if (!empty($dataArray)) {
              Member::insert($dataArray);
              return redirect()->route('importMembers')->with('success', 'CSV data imported successfully!');
            } else {
              return redirect()->route('importMembers')->with('error', 'No data found in the CSV file.');
            }
        }
      
        return redirect()->route('importMembers')->with(['error' => 'No csv file selected...']); // View for uploading the CSV file
    }
    
}
