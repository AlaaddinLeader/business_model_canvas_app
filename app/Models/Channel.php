<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_model_id',
        'version',
        'channel_type_id',
        'details',
    ];

    protected $casts = [
        'version' => 'integer',
    ];

    public function businessModel()
    {
        return $this->belongsTo(BusinessModel::class);
    }

    public function channelType()
    {
        return $this->belongsTo(ChannelType::class);
    }

    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }
}
