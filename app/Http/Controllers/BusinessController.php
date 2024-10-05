<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use SplFileObject;
use App\Models\ItemCapture;
use App\Models\ItemCategory;
use App\Models\RepayCapture;
use Illuminate\Http\Request;

class BusinessController extends Controller
{

    public function index()
    {
        return view('business.import.index');
    }

    public function importBusinessLoanFromCsv(Request $request)
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

                    // validate coopId
                    if($row[0] == '-' || is_null($row[0]) || empty($row[0]))
                    {
                    // skip the row
                    continue;
                    }

                    // validate item name
                    if($row[1] == '-' || is_null($row[1]) || empty($row[1]))
                    {
                    // skip the row
                    continue;
                    }

                    if (!empty($row[0])) {
                    $coop = ltrim($row[0], '0');
                    }

                    // Check for existing ItemCategory by name and price
                    $item_id = null;

                    // Check for existing loan
                    $existItem = ItemCategory::where('name', $row[1])->where('price', $row[2])->first();
                    // if exists, update the details
                    if($existItem){
                    $item_id = $existItem->id; // get the id of the existing item

                    } else {
                    // create new item category
                    $item = ItemCategory::create([
                        'name' => $row[1],
                        'price' => $row[2]
                    ]);

                    if(!$item){
                        // throw new Exception("Error Processing Loan Request into db", 1);
                        return redirect()->route('prev_repay_import')->with('error', 'Error creating Category item');
                    }

                        $item_id = $item->id; // get the id of the created item
                    }

                    // Check for existing ItemCapture by coopId and category_id and payment_status
                    $existLoan = ItemCapture::where('coopId', $coop)->where('category_id', $item_id)->where('payment_status', 1)->first();
                    // if exists, update the details

                    $loanId = null;

                    if($existLoan){
                        $loanId = $existLoan->id; // get the id of the existing loan
                    } else {
                    // create new loan
                    $loan = ItemCapture::create([
                        'coopId' => $coop,
                        'category_id' => $item_id,
                        'quantity' => 1, // default to '1' for now
                        'buyingDate' => $row[6],
                        'payment_timeframe' => $row[7],
                        'payment_status' => 1,
                        'userId' => auth('admin')->user()->id,
                        'repaymentDate' => Carbon::parse($row[6])->addMonths($row[7]),
                        'loanPaid' => 0,
                        'loanBalance' => $row[2]
                    ]);

                    if(!$loan){
                        // throw new Exception("Error Processing Loan Request into db", 1);
                        return redirect()->route('prev_repay_import')->with('error', 'Error creating Loan item');
                    }

                        $loanId = $loan->id; // get the id of the created loan
                    }

                    // update loan payment status
                    try {
                        $loanBalance = (float)$row[2] - (float)$row[3];
                        $repayment = RepayCapture::create([
                            'coopId' => $coop,
                            'item_capture_id' => $loanId,
                            'amountToRepay' => $row[2],
                            'repaymentDate' => date('Y-m-d', strtotime($row[4])),
                            'loanBalance' => $loanBalance,
                            'serviceCharge' => 0,
                            'userId' => auth('admin')->user()->id,
                        ]);

                        if($repayment){
                            $repayment->updateLoanBalance();
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                        return redirect()->route('prev_repay_import')->with('error', 'Error creating Loan repayment');
                    }
                    
                }
            }
      
            return redirect()->route('prev_repay_import')->with('success', 'Business data imported successfully');
        }
      
        return redirect()->route('prev_repay_import')->with(['error' => 'No ledger csv file selected...']); // View for uploading the CSV file
    }
}
