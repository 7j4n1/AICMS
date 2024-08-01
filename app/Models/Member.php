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
        'userId'
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

}
