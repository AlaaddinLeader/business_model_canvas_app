<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeyResource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_model_id',
        'version',
        'resource_type_id',
        'details',
    ];

    protected $casts = [
        'version' => 'integer',
    ];

    public function businessModel()
    {
        return $this->belongsTo(BusinessModel::class);
    }

    public function resourceType()
    {
        return $this->belongsTo(ResourceType::class);
    }

    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }

    public function scopeByType($query, $typeCode)
    {
        return $query->whereHas('resourceType', function ($q) use ($typeCode) {
            $q->where('code', $typeCode);
        });
    }
}
