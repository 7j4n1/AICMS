<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanCapture extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    protected $fillable = [
        'coopId',
        'loanAmount',
        'loanDate',
        'guarantor1',
        'guarantor2',
        'guarantor3',
        'guarantor4',
        'status',
        'userId'
    ];

    public function scopeAddToActiveLoan()
    {
        // Wrap in a database transaction
        DB::transaction(function() {
            $activeLoan = ActiveLoans::create([
                'coopId' => $this->coopId,
                'loanAmount' => $this->loanAmount,
                'loanPaid' => 0.0,
                'loanBalance' => $this->loanAmount
            ]);

            if(!$activeLoan)
                throw new Exception("Failed to create an active loan.");
                
        });
        
    }
}
