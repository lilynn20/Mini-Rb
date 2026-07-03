<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $guarded = [];

    public function annonces()
    {
        return $this->belongsToMany(Annonce::class, 'annonce_amenity');
    }
}

