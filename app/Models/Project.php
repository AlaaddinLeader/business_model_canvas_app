<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'industry',
        'notes',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessModels()
    {
        return $this->hasMany(BusinessModel::class);
    }

    public function activeBusinessModel()
    {
        return $this->hasOne(BusinessModel::class)->where('is_active', true);
    }

    public function latestBusinessModel()
    {
        return $this->hasOne(BusinessModel::class)->latestOfMany('version');
    }

    // Scopes
    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getHasActiveModelAttribute()
    {
        return $this->businessModels()->where('is_active', true)->exists();
    }
}
