<?php

namespace App\Observers;

use App\Models\AnnonceImage;
use Illuminate\Support\Facades\Storage;

class AnnonceImageObserver
{
    public function deleted(AnnonceImage $image): void
    {
        Storage::disk('s3')->delete($image->image_path);
    }
}
