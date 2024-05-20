<?php

namespace App\Imports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\ToModel;

class MembersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Member([
            'id' => $row[0],
            'coopId' => $row[1],
            'surname' => $row[2],
            'otherNames' => $row[3],
            'occupation' => $row[4],
            'gender' => $row[5],
            'religion' => $row[6],
            'phoneNumber' => $row[7],
            'bankName' => $row[8],
            'accountNumber' => $row[9],
            'nextOfKinName' => $row[10],
            'nextOfKinPhoneNumber' => $row[11],
            'yearJoined' => $row[12],
            'userId' => $row[13]
        ]);
    }
}
