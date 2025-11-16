<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'version',
        'currency_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'version' => 'integer',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function valuePropositions()
    {
        return $this->hasMany(ValueProposition::class);
    }

    public function customerSegments()
    {
        return $this->hasMany(CustomerSegment::class);
    }

    public function customerRelationships()
    {
        return $this->hasMany(CustomerRelationship::class);
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function keyActivities()
    {
        return $this->hasMany(KeyActivity::class);
    }

    public function keyResources()
    {
        return $this->hasMany(KeyResource::class);
    }

    public function keyPartnerships()
    {
        return $this->hasMany(KeyPartnership::class);
    }

    public function revenueStreams()
    {
        return $this->hasMany(RevenueStream::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function modelChanges()
    {
        return $this->hasMany(ModelChange::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }

    public function scopeLatestVersion($query)
    {
        return $query->orderBy('version', 'desc');
    }

    // Methods
    public function getTotalExpenses()
    {
        return $this->expenses()->sum('total');
    }

    public function getTotalRevenue()
    {
        return $this->revenueStreams()->sum('projected_amount');
    }

    public function createNewVersion()
    {
        $newVersion = $this->replicate();
        $newVersion->version = $this->version + 1;
        $newVersion->is_active = true;
        $newVersion->save();

        // Deactivate current version
        $this->is_active = false;
        $this->save();

        return $newVersion;
    }
}
