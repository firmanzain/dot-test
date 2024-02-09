<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProvinces extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    public function cities()
    {
        return $this->hasMany(MasterCities::class);
    }
}
