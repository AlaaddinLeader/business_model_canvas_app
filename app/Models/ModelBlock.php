<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelBlock extends Model
{
    use HasFactory;

    protected $primaryKey = 'block_id';

    protected $fillable = [
        'model_id',
        'block_name',
        'block_content',
    ];

    // Block name constants for Business Model Canvas
    public const BLOCK_VALUE_PROPOSITION = 'Value Proposition';
    public const BLOCK_CUSTOMER_SEGMENTS = 'Customer Segments';
    public const BLOCK_CUSTOMER_RELATIONSHIPS = 'Customer Relationships';
    public const BLOCK_CHANNELS = 'Channels';
    public const BLOCK_REVENUE_STREAMS = 'Revenue Streams';
    public const BLOCK_KEY_RESOURCES = 'Key Resources';
    public const BLOCK_KEY_ACTIVITIES = 'Key Activities';
    public const BLOCK_KEY_PARTNERSHIPS = 'Key Partnerships';
    public const BLOCK_COST_STRUCTURE = 'Cost Structure';

    // Relationships
    public function businessModel()
    {
        return $this->belongsTo(BusinessModel::class, 'model_id', 'model_id');
    }
}
