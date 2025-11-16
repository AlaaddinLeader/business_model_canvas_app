<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ============================================
// CustomerSegmentType Model
// ============================================
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

// ============================================
// RelationshipType Model
// ============================================
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

// ============================================
// ChannelType Model
// ============================================
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

// ============================================
// ResourceType Model
// ============================================
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

// ============================================
// RevenueStreamType Model
// ============================================
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
