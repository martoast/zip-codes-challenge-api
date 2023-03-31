<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZipCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'zip_code',
        'locality',
        'federal_entity_id',
        'municipality_id'
    ];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array<string>
     */
    protected $visible = [
        'zip_code',
        'locality'
    ];

    /**
     * Get the federal entity that owns the zip code.
     */
    public function federalEntity()
    {
        return $this->belongsTo(FederalEntity::class);
    }

    /**
     * Get the municipality that owns the zip code.
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * The settlements that belong to the zip code.
     */
    public function settlements()
    {
        return $this->belongsToMany(Settlement::class)->orderBy('key');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'zip_code';
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation  $query
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return $query->with([
            'federalEntity', 
            'settlements', 
            'municipality'
        ])->where($field ?? $this->getRouteKeyName(), $value);
    }
}
