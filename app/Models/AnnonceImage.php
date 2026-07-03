<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnonceImage extends Model
{
    protected $fillable = ['image_path', 'sort_order', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }
}

