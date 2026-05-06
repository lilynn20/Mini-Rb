<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Reservation;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'adresse',
        'ville',
        'latitude',
        'longitude',
        'prix_par_nuit',
        'image',
        'nombre_de_chambres'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function images()
    {
        return $this->hasMany(AnnonceImage::class)->orderBy('position');
    }
}
