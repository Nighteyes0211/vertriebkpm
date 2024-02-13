<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Facilty extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->created_by = $model->created_by ?: Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = $model->updated_by ?: Auth::id();
        });
    }

    /**
     * Relationship
     */
    public function notes()
    {
        return $this->morphMany(Noteable::class, 'notable');
    }

    public function branches()
    {
        return $this->hasMany(FacilityBranch::class);
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }

    public function statuses()
    {
        return $this->belongsToMany(FacilityStatus::class, 'facilty_facility_status');
    }

    public function products()
    {
        return $this->morphMany(Productable::class, 'productable');
    }

    public function type()
    {
        return $this->belongsTo(FacilityType::class, 'facility_type_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * Soft delete model
     */
    public function softDelete()
    {
        return $this->update([
            'is_deleted' => true,
            'deleted_at' => now(),
            'deleted_by' => Auth::id()
        ]);
    }


    /**
     * Scopes
     */
    public function scopeavailable($query)
    {
        return $query->where('facilties.is_deleted', false);
    }

    public function scopesoftDelete($query)
    {
        return $query->update([
            'is_deleted' => true,
            'deleted_at' => now(),
            'deleted_by' => Auth::id()
        ]);
    }
}
