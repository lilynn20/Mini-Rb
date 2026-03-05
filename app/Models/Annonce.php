<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'adresse',
        'ville',
        'prix_par_nuit',
        'image',
        'nombre_de_chambres'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
