<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSegmentType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];

    public function customerSegments()
    {
        return $this->hasMany(CustomerSegment::class, 'segment_type_id');
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}
