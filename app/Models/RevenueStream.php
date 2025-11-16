<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RevenueStream extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_model_id',
        'version',
        'stream_type_id',
        'details',
        'projected_amount',
        'currency_code',
    ];

    protected $casts = [
        'version' => 'integer',
        'projected_amount' => 'decimal:2',
    ];

    public function businessModel()
    {
        return $this->belongsTo(BusinessModel::class);
    }

    public function streamType()
    {
        return $this->belongsTo(RevenueStreamType::class);
    }

    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }

    public function scopeByCurrency($query, $currencyCode)
    {
        return $query->where('currency_code', $currencyCode);
    }

    public function getFormattedAmountAttribute()
    {
        return $this->currency_code . ' ' . number_format($this->projected_amount, 2);
    }
}
