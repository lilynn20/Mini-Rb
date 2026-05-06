<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $favorites = Annonce::with('images')
            ->whereIn('id', Favorite::where('user_id', $userId)->pluck('annonce_id'))
            ->latest()
            ->get()
            ->map(function ($a) {
                $first = $a->images->first();
                return [
                    'id'            => $a->id,
                    'titre'         => $a->titre,
                    'ville'         => $a->ville,
                    'prix_par_nuit' => $a->prix_par_nuit,
                    'image_url'     => $first ? Storage::disk('s3')->url($first->path) : null,
                    'is_favorited'  => true,
                ];
            });

        return response()->json($favorites);
    }

    public function store(Request $request, Annonce $annonce)
    {
        Favorite::firstOrCreate([
            'user_id'    => Auth::id(),
            'annonce_id' => $annonce->id,
        ]);

        return response()->json(['message' => 'Ajouté aux favoris.', 'is_favorited' => true]);
    }

    public function destroy(Request $request, Annonce $annonce)
    {
        Favorite::where('user_id', Auth::id())
            ->where('annonce_id', $annonce->id)
            ->delete();

        return response()->json(['message' => 'Retiré des favoris.', 'is_favorited' => false]);
    }
}
