<?php

namespace App\Http\Controllers;

use SplFileObject;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use App\Models\Member;
use App\Models\PaymentCapture as ModelsPaymentCapture;
use Illuminate\Http\Request;
use App\Models\PreviousLedger2023;

class HomeController extends Controller
{
    public function setLastLedger(Request $request) {
        if ($request->hasFile('csvfile')) {
          set_time_limit(300);
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
                
                
                if($row[0] == '-' || empty($row[0]))
                {
                  continue;
                }

                if (!empty($row[0])) {
                  $coop = ltrim($row[0], '0');
                
                  $loan = (($row[5] == '-') || (empty($row[5]))) ? 0 : ltrim($row[5], '0');
                  $share = (($row[3] == '-') || (empty($row[3]))) ? 0 : ltrim($row[3], '0');
                  $saving = (($row[4] == '-') || (empty($row[4]))) ? 0 : ltrim($row[4], '0');
                  $others = (($row[6] == '-') || (empty($row[6]))) ? 0 : ltrim($row[6], '0');
                  $total = (float)$loan + (float)$share + (float)$saving + (float)$others;

                  $checkIfExist = Member::where('coopId', $coop)->first();
                  if(!$checkIfExist)
                  {
                    continue;
                  }
                  $dataArray[] = [
                  'coopId' => $coop,
                  'shareAmount' => $share,
                  'savingAmount' => $saving,
                  'loanAmount' => $loan,
                  'others' => $others,
                  'userId' => auth('admin')->user()->id, // Retrieve admin id
                  'adminCharge' => 0,
                  'totalAmount' => $total,
                  'paymentDate' => date('Y-m-d', strtotime($row[7])),
                  ];
                  
                  // update the member activeloans balance with the new loan amount
                  $activeLoan = ActiveLoans::where('coopId', $coop)->first();
                  if($activeLoan) {
                    $paymentDate = date('Y-m-d', strtotime($row[7]));
                    if($activeLoan->loanAmount >= $loan){
                      // $activeLoan->loanPaid = (float)$activeLoan->loanAmount - (float)$loan;
                      // $activeLoan->loanBalance = (float)$loan;

                      $activeLoan->setPayment((float)$loan, $paymentDate);
                    } else {
                      $activeLoan->loanPaid = (float)$activeLoan->loanAmount;
                      $activeLoan->loanBalance = 0;
                      $activeLoan->lastPaymentDate = $paymentDate;
                      $activeLoan->save();
                    }
                    
                  }
                }
              }
            }
      
            // Insert new records (if any)
            if (!empty($dataArray)) {
              // PreviousLedger2023::insert($dataArray);
              ModelsPaymentCapture::insert($dataArray);

              return redirect()->route('importMembers')->with('success', 'Ledger CSV data imported successfully!');
            } else {
              return redirect()->route('importMembers')->with('error', 'No data found in the ledger CSV file.');
            }
        }
      
        return redirect()->route('importMembers')->with(['error' => 'No ledger csv file selected...']); // View for uploading the CSV file
    }

    public function setPaymentDate()
    {
      $paymentDate = date('Y-m-d', strtotime('2023-12-31'));

      ModelsPaymentCapture::where('paymentDate', null)
        ->update(['paymentDate' => $paymentDate]);

      echo "<h2>Payment date set successfully!</h2>";
    }

    public function clearLoans()
    {
      // Delete * from ActiveLoans 
      ActiveLoans::truncate();
      LoanCapture::truncate();
      ModelsPaymentCapture::truncate();
      PreviousLedger2023::truncate();

    }
}
