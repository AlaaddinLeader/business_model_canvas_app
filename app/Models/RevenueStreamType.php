<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RevenueStreamType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];

    public function revenueStreams()
    {
        return $this->hasMany(RevenueStream::class, 'stream_type_id');
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}
