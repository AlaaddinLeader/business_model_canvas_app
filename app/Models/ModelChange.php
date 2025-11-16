<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ModelChange extends Model
{
     use HasFactory;

    // No timestamps columns (we have changed_at)
    public $timestamps = false;

    protected $fillable = [
        'business_model_id',
        'user_id',
        'change_type',
        'table_name',
        'record_id',
        'field_name',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'record_id' => 'integer',
    ];

    // Relationships
    public function businessModel()
    {
        return $this->belongsTo(BusinessModel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByChangeType($query, $type)
    {
        return $query->where('change_type', $type);
    }

    public function scopeByTable($query, $tableName)
    {
        return $query->where('table_name', $tableName);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('changed_at', '>=', now()->subDays($days));
    }

    public function scopeForRecord($query, $tableName, $recordId)
    {
        return $query->where('table_name', $tableName)
                     ->where('record_id', $recordId);
    }

    // Static method to log changes
    public static function logChange($businessModelId, $changeType, $tableName, $recordId, $fieldName = null, $oldValue = null, $newValue = null)
    {
        return self::create([
            'business_model_id' => $businessModelId,
            'user_id' => auth()->id,
            'change_type' => $changeType,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changed_at' => now(),
        ]);
    }

    // Accessor for formatted change description
    public function getChangeDescriptionAttribute()
    {
        $user = $this->user ? $this->user->name : 'System';
        $action = ucfirst($this->change_type);

        if ($this->field_name) {
            return "{$user} {$action} {$this->field_name} in {$this->table_name}";
        }

        return "{$user} {$action} {$this->table_name} record";
    }
}
