<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCities extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'province_id', 'name', 'type', 'postal_code'
    ];

    public function province()
    {
        return $this->belongsTo(MasterProvinces::class);
    }
}
