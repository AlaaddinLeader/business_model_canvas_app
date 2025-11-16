<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationshipType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];

    public function customerRelationships()
    {
        return $this->hasMany(CustomerRelationship::class);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}

