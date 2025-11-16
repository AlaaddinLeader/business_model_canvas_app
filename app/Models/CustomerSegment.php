<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSegment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_model_id',
        'version',
        'segment_type_id',
        'age_group',
        'region',
        'problems',
        'needs',
        'notes',
    ];

    protected $casts = [
        'version' => 'integer',
    ];

    public function businessModel()
    {
        return $this->belongsTo(BusinessModel::class);
    }

    public function segmentType()
    {
        return $this->belongsTo(CustomerSegmentType::class);
    }

    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    public function scopeByAgeGroup($query, $ageGroup)
    {
        return $query->where('age_group', $ageGroup);
    }
}
