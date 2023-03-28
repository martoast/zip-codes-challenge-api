<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name', 
        'key', 
        'zone_type', 
        'settlement_type_id'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['settlementType'];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array<string>
     */
    protected $visible = [
        'key',
        'name',
        'zone_type'
    ];

    /**
     * Get the settlement type that owns the zip code.
     */
    public function settlementType()
    {
        return $this->belongsTo(SettlementType::class);
    }

    /**
     * The zipcodes that belong to this settlement.
     */
    public function zipCodes()
    {
        return $this->belongsToMany(ZipCode::class)->orderBy('zip_code');
    }
}
