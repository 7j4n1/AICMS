<?php

namespace App\Http\Controllers;

use App\Models\ActiveLoans;
use Exception;
use SplFileObject;
use App\Models\Member;
use App\Models\LoanCapture;
use App\Models\PreviousLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }
    public function import(Request $request)
    {
      set_time_limit(300);
        if ($request->hasFile('csvfile')) {
            $file = $request->file('csvfile');
            $filePath = $file->getRealPath();
      
            // Open CSV file for reading
            $csv = new SplFileObject($filePath, 'r');
            $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY); // Set CSV reading flags
      
            // Skip header row (optional)
            if ($csv->fgets()) { // Read and discard the first line (assuming header)
            }

            $updateFlag = 0;
      
            $dataArray = [];
            while (!$csv->eof()) {
              $row = $csv->fgetcsv();
              if ($row) {
                // Assuming same number of columns in all rows
                $uniqueId = $row[0]; // Replace with your actual unique field index
      
                // Check if record already exists
                $existingRecord = Member::where('coopId', $uniqueId)->first();
      
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
                }else {
                  // Update existing record
                  $year = null;
                  if (!empty($row[11])) {
                      $year = $row[11];
                  }

                  $existingRecord->update([
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
                  ]);
                  // increase the update flag
                  $updateFlag++;
                }
              }
            }
      
            // Insert new records (if any)
            if (!empty($dataArray) || $updateFlag > 0) {
              Member::insert($dataArray);
              return redirect()->route('importMembers')->with('success', 'CSV data imported successfully!');
            } else {
              return redirect()->route('importMembers')->with('error', 'No data found in the CSV file.');
            }
        }
      
        return redirect()->route('importMembers')->with(['error' => 'No csv file selected...']); // View for uploading the CSV file
    }

    
    public function importLoan(Request $request)
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
                // Insert new record
                
                $coop = null;

                
                if($row[0] == '-' || $row[0] == null)
                {
                  $coop = null;
                }

                if (!empty($row[0])) {
                  $coop = ltrim($row[0], '0');
                }

                $dataArray[] = [
                'coopId' => $coop,
                'loanAmount' => $row[1],
                'loanDate' => date('Y-m-d', strtotime($row[2])),
                'guarantor1' => ($row[3] == '-') ? null : ltrim($row[3], '0'),
                'guarantor2' => ($row[4] == '-') ? null : ltrim($row[4], '0'),
                'guarantor3' => ($row[5] == '-') ? null : ltrim($row[5], '0'),
                'guarantor4' => ($row[6] == '-') ? null : ltrim($row[6], '0'),
                'status' => $row[7],
                'userId' => auth('admin')->user()->id, // Retrieve admin id
                'repaymentDate' => null
                ];
                
                
              }
            }
      
            // Insert new records (if any)
            if (!empty($dataArray)) {
              PreviousLoan::insert($dataArray);
              return redirect()->route('importMembers')->with('success', 'Loan CSV data imported successfully!');
            } else {
              return redirect()->route('importMembers')->with('error', 'No data found in the Loan CSV file.');
            }
        }
      
        return redirect()->route('importMembers')->with(['error' => 'No loan csv file selected...']); // View for uploading the CSV file
    }


    public function importLoanFromCsv(Request $request)
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
      
            set_time_limit(300);
            // Read each CSV row
            while (!$csv->eof()) {
              $row = $csv->fgetcsv();
              if ($row) {
                // Insert new record
                
                $coop = null;

                
                if($row[0] == '-' || is_null($row[0]) || empty($row[0]))
                {
                  // skip the row
                  continue;
                }

                if (!empty($row[0])) {
                  $coop = ltrim($row[0], '0');
                }

                // Check for existing loan
                $existLoan = LoanCapture::where('coopId', $coop)->first();
                // if exists, update the details
                if($existLoan)
                {
                  $existLoan->update([
                    'loanAmount' => $row[1],
                    'loanDate' => date('Y-m-d', strtotime($row[2])),
                    'guarantor1' => ($row[3] == '-') ? $coop : ($this->checkIfMemberExists(ltrim($row[3], '0')) ? ltrim($row[3], '0') : $coop),
                    'guarantor2' => $this->otherGuarantorCheckNullorFilter($row[4]),
                    'guarantor3' => $this->otherGuarantorCheckNullorFilter($row[5]),
                    'guarantor4' => $this->otherGuarantorCheckNullorFilter($row[6]),
                    'status' => $row[7],
                    'repaymentDate' => date('Y-m-d', strtotime($row[2]. ' + 540 days'))
                  ]);
      
                  // search in ActiveLoan and update
                  $act = ActiveLoans::where('coopId', $coop)->first();
                  if($act){
                    $act->update([
                      'loanAmount' => $row[1],
                      'loanBalance' => $row[1],
                      'loanDate' => date('Y-m-d', strtotime($row[2])),
                      'repaymentDate' => date('Y-m-d', strtotime($row[2]. ' + 540 days')),
                      'lastPaymentDate' => date('Y-m-d', strtotime($row[2]))
                    ]);
                  } else {
                    if($existLoan->status == 1)
                      $existLoan->scopeAddToActiveLoanWithDate();
                  }
      
                } else {
                  // check if member with coopId exists
                  $member = $this->checkIfMemberExists($coop);
                  if(!$member)
                    continue;
      
                  $g1 = null;
                  
                  if($row[3] == null || empty($row[3]) || $row[3] == '-')
                  {
                    $g1 = $coop;
                  }else {
                    // check for guarantor 1 and if exists
                    $tempId = ltrim($row[3], '0');
                    $guarantor1 = $this->checkIfMemberExists($tempId);
                    if($guarantor1)
                      $g1 = $tempId;
                    else
                      $g1 = $coop;
                  }
                  
                  
                  $loanC = LoanCapture::create([
                    'coopId' => $coop,
                    'loanAmount' => $row[1],
                    'loanDate' => date('Y-m-d', strtotime($row[2])),
                    'guarantor1' => $g1,
                    'guarantor2' => $this->otherGuarantorCheckNullorFilter($row[4]),
                    'guarantor3' => $this->otherGuarantorCheckNullorFilter($row[5]),
                    'guarantor4' => $this->otherGuarantorCheckNullorFilter($row[6]),
                    'status' => $row[7],
                    'userId' => auth('admin')->user()->id,
                    'repaymentDate' => date('Y-m-d', strtotime($row[2]. ' + 540 days'))
                  ]);

                  if(!$loanC){
                    // throw new Exception("Error Processing Loan Request into db", 1);
                    return redirect()->route('importMembers')->with('error', 'Error Processing Loan Request into db');
                  }

                  try {
                    if($loanC->status == 1)
                      $loanC->scopeAddToActiveLoanWithDate();
                  } catch (Exception $th) {
                      // throw new Exception("Error Processing Loan Request: ". $th->getMessage(), 1);
                      return redirect()->route('importMembers')->with('error', "Error Processing Loan Request: ". $th->getMessage());
                  }
                }
                
              }
            }
      
            return redirect()->route('importMembers')->with('success', 'Loan data imported successfully');
        }
      
        return redirect()->route('importMembers')->with(['error' => 'No loan csv file selected...']); // View for uploading the CSV file
    }

    public function checkIfMemberExists($coopId)
    {
      $member = Member::where('coopId', $coopId)->first();
      if(!$member)
        return false;
      return true;
    }

    public function otherGuarantorCheckNullorFilter($guarantor)
    {
      $g0 = null;
          
      if($guarantor == null || empty($guarantor) || $guarantor == '-')
      {
        $g0 = null;
      }else {
        $tempId = ltrim($guarantor, '0');
        // check for guarantor 1 and if exists
        $guarantor1 = $this->checkIfMemberExists($tempId);
        if($guarantor1)
          $g0 = $tempId;
      }

      return $g0;
    }
}
