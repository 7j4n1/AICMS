<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActiveLoans extends Model
{
    use HasFactory;

    protected $fillable = [
        'coopId',
        'loanAmount',
        'loanPaid',
        'loanBalance',
        'userId',
        'loanDate',
        'repaymentDate',
        'lastPaymentDate',
    ];

    public function setPayment($amount, $date=null)
    {
        // Update loanPaid with the new amount
        static::query()->where('id', $this->id)->update([
            'loanPaid' => DB::raw('loanPaid + ' . $amount),
        ]);

        // Update loanBalance directly in the database
        static::query()->where('id', $this->id)->update([
            'loanBalance' => DB::raw('loanAmount - loanPaid'),
            'lastPaymentDate' => $date ?? date('Y-m-d'),
        ]);

        // Refresh the object instance (optional)
        $this->refresh();
    }

    // Get Member by Coop ID
    public function member()
    {
        return $this->belongsTo(Member::class, 'coopId', 'coopId');
    }
}
