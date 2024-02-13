<?php

namespace App\Models;

use App\Enum\Contact\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Contact extends Model
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
     * Relationships
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facilty::class);
    }

    public function notes()
    {
        return $this->morphMany(Noteable::class, 'notable');
    }

    public function products()
    {
        return $this->morphMany(Productable::class, 'productable');
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
        return $query->where('is_deleted', false);
    }


    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
