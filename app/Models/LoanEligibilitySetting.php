<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanEligibilitySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_key',
        'name',
        'description',
        'formula',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the active formula
     */
    public static function getActiveFormula()
    {
        return self::where('active', true)->first();
    }

    /**
     * Calculate loan eligibility for a member
     */
    public function calculateEligibility(float $savings, float $shares): float
    {
        // Parse and evaluate the formula
        // Replace variables with actual values
        $formula = str_replace(['savings', 'shares'], [$savings, $shares], $this->formula);
        
        // Use eval cautiously (in production, use a proper expression evaluator)
        try {
            $result = eval("return {$formula};");
            return (float) $result;
        } catch (\Throwable $e) {
            // If evaluation fails, return 0
            return 0.0;
        }
    }

    /**
     * Scope to get only active setting
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
