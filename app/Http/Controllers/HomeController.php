<?php

namespace App\Http\Controllers;

use App\Models\PreviousLedger2023;
use SplFileObject;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function setLastLedger(Request $request) {
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
                // Insert new record
                
                $coop = null;

                
                if($row[0] == '-' || $row[0] == null)
                {
                  $coop = null;
                }

                if (!empty($row[0])) {
                  $coop = ltrim($row[0], '0');
                }
                $loan = (($row[5] == '-') || (empty($row[5]))) ? 0 : ltrim($row[5], '0');
                $share = (($row[3] == '-') || (empty($row[3]))) ? 0 : ltrim($row[3], '0');
                $saving = (($row[4] == '-') || (empty($row[4]))) ? 0 : ltrim($row[4], '0');
                $others = (($row[6] == '-') || (empty($row[6]))) ? 0 : ltrim($row[6], '0');
                $total = (float)$loan + (float)$share + (float)$saving + (float)$others;

                $dataArray[] = [
                'coopId' => $coop,
                'shareAmount' => $share,
                'savingAmount' => $saving,
                'loanAmount' => $loan,
                'others' => $others,
                'userId' => auth('admin')->user()->id, // Retrieve admin id
                'adminCharge' => 0,
                'totalAmount' => $total,
                ];
                
                
              }
            }
      
            // Insert new records (if any)
            if (!empty($dataArray)) {
              PreviousLedger2023::insert($dataArray);
              return redirect()->route('importMembers')->with('success', '2023 Ledger CSV data imported successfully!');
            } else {
              return redirect()->route('importMembers')->with('error', 'No data found in the 2023_ledger CSV file.');
            }
        }
      
        return redirect()->route('importMembers')->with(['error' => 'No ledger_2023 csv file selected...']); // View for uploading the CSV file
    }
}
