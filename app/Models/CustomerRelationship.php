<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerRelationship extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_model_id',
        'version',
        'relationship_type_id',
        'details',
    ];

    protected $casts = [
        'version' => 'integer',
    ];

    public function businessModel()
    {
        return $this->belongsTo(BusinessModel::class);
    }

    public function relationshipType()
    {
        return $this->belongsTo(RelationshipType::class);
    }

    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }
}
