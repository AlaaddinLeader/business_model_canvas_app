<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChannelType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}
