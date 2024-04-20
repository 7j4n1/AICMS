<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
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
        'yearJoined'
    ]; 
}
