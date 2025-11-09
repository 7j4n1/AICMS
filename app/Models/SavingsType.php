<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'active',
        'minimum_amount',
        'maximum_amount',
    ];

    protected $casts = [
        'active' => 'boolean',
        'minimum_amount' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
    ];

    /**
     * Get payments of this savings type
     */
    public function payments()
    {
        return $this->hasMany(PaymentCapture::class, 'savings_type_id');
    }

    /**
     * Scope to get only active savings types
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
