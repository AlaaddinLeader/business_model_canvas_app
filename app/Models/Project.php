<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'user_id',
        'project_name',
        'project_description',
        'industry',
        'revenue_method',
        'notes',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function businessModels()
    {
        return $this->hasMany(BusinessModel::class, 'project_id', 'project_id');
    }
}
