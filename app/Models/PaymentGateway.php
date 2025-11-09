<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'enabled',
        'public_key',
        'secret_key',
        'merchant_email',
        'additional_config',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'additional_config' => 'array',
    ];

    /**
     * Scope to get only enabled gateways
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Get gateway by name
     */
    public static function getByName(string $name)
    {
        return self::where('name', $name)->first();
    }
}
