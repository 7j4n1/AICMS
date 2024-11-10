<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemCapture extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    protected $fillable = [
        'coopId',
        'quantity',
        'buyingDate',
        'payment_timeframe',
        'payment_status',
        'userId',
        'repaymentDate',
        'category_id',
        'loanPaid',
        'loanBalance',
        'editDates',
        'editedBy'
    ];

    protected $casts = [
        'editDates' => 'array',
        'editedBy' => 'array'
    ];

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

    public function category()
    {
        return $this->belongsTo(ItemCategory::class,'category_id');
    }

    // has many repayments
    public function repayments()
    {
        return $this->hasMany(RepayCapture::class,'item_capture_id', 'id');
    }

    // member
    public function member()
    {
        return $this->belongsTo(Member::class, 'coopId', 'coopId');
    }
}
