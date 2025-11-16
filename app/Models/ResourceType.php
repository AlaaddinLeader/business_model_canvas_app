<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ResourceType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];

    public function keyResources()
    {
        return $this->hasMany(KeyResource::class);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}

