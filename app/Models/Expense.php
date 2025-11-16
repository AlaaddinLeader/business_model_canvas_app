<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_model_id',
        'version',
        'expense_type',
        'description',
        'unit_cost',
        'quantity',
        'currency_code',
        'display_order',
    ];

    protected $casts = [
        'version' => 'integer',
        'unit_cost' => 'decimal:2',
        'quantity' => 'decimal:2',
        'total' => 'decimal:2',
        'display_order' => 'integer',
    ];

    public function businessModel()
    {
        return $this->belongsTo(BusinessModel::class);
    }

    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    public function scopeByCurrency($query, $currencyCode)
    {
        return $query->where('currency_code', $currencyCode);
    }

    public function getFormattedTotalAttribute()
    {
        return $this->currency_code . ' ' . number_format($this->total, 2);
    }

    public function getFormattedUnitCostAttribute()
    {
        return $this->currency_code . ' ' . number_format($this->unit_cost, 2);
    }
}
