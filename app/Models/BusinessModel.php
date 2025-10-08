<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'model_id';

    protected $fillable = [
        'project_id',
        'generated_at',
        'version',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function modelBlocks()
    {
        return $this->hasMany(ModelBlock::class, 'model_id', 'model_id');
    }
}
