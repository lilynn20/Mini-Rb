<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Annonce $annonce)
    {
        $user = Auth::user();

        if ($user->id === $annonce->user_id) {
            return response()->json(['error' => 'Vous ne pouvez pas ajouter votre propre annonce aux favoris'], 403);
        }

        $isFavorited = $user->favorites()->where('annonce_id', $annonce->id)->exists();

        if ($isFavorited) {
            $user->favorites()->detach($annonce->id);
            $message = 'Annonce retirée de vos favoris';
        } else {
            $user->favorites()->attach($annonce->id);
            $message = 'Annonce ajoutée à vos favoris';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'isFavorited' => !$isFavorited,
        ]);
    }

    public function index(Request $request)
    {
        $favorites = Auth::user()->favorites()
            ->with(['images', 'amenities'])
            ->latest('favorites.created_at')
            ->paginate(12)
            ->withQueryString();

        return view('favorites.index', compact('favorites'));
    }
}
