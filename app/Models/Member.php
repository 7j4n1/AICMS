<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    protected $fillable = [
        'coopId',
        'surname',
        'otherNames',
        'occupation',
        'gender',
        'religion',
        'phoneNumber',
        'bankName',
        'accountNumber',
        'nextOfKinName',
        'nextOfKinPhoneNumber',
        'yearJoined',
        'userId','editDates',
        'editedBy'
    ];

    protected $casts = [
        'editDates' => 'array',
        'editedBy' => 'array'
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class, 'coopId', 'coopId');
    }

    // create relationship with paymentcapture
    public function payment_captures()
    {
        return $this->hasMany(PaymentCapture::class, 'coopId', 'coopId');
    }

    // create relationship with annualfee
    public function annual_fees()
    {
        return $this->hasMany(AnnualFee::class, 'coopId', 'coopId');
    }

    // update the edit dates
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

}
