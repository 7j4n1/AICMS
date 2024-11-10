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
        'editDates',
        'editedBy'
    ];

    protected $casts = [
        'editDates' => 'array',
        'editedBy' => 'array'
    ];

    public function updateEditDates()
    {
        // get the current edit dates or initialize an empty array
        $dates = $this->editDates ?? [];

        // add the current date to the beginning of the array
        array_unshift($dates, now());

        // keep only the last 3 edit dates
        $this->editDates = array_slice($dates, 0, 3);

        // get the current edited by or initialize an empty array
        $editedBy = $this->editedBy ?? [];

        // add the current user to the beginning of the array
        array_unshift($editedBy, auth('admin')->user()->name);

        // keep only the last 3 edited by
        $this->editedBy = array_slice($editedBy, 0, 3);
    }

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
