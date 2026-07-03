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
        'prix_par_nuit',
        'image',
        'nombre_de_chambres'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($annonce) {
            $annonce->images()->each(fn($image) => $image->delete());
        });
    }

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
        return $this->hasMany(AnnonceImage::class)->orderBy('sort_order');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'annonce_amenity');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function getDisplayImage()
    {
        return $this->images->where('is_primary', true)->first()
            ?? $this->images->first()
            ?? ($this->image ? $this->image : null);
    }

    public function isFavoritedBy(User $user): bool
    {
        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }
}
