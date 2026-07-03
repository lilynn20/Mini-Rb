<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AnnonceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Annonce::latest();

        if ($request->filled('ville')) {
            $query->where('ville', 'like', '%' . $request->ville . '%');
        }

        if ($request->filled('prix_max')) {
            $query->where('prix_par_nuit', '<=', $request->prix_max);
        }

        if ($request->filled('nb_personne')) {
            // On considère que le nombre de chambres est un indicateur de capacité (ex: 2 personnes par chambre)
            $query->where('nombre_de_chambres', '>=', ceil($request->nb_personne / 2));
        }

        $annonces = $query->paginate(9)->withQueryString();
        return view('annonces.index', compact('annonces'));
    }

    public function create()
    {
        $amenities = Amenity::all();
        return view('annonces.create', compact('amenities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'prix_par_nuit' => 'required|numeric|min:0',
            'nombre_de_chambres' => 'required|integer|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $annonce = Annonce::create([
            'user_id' => Auth::id(),
            'titre' => $request->titre,
            'description' => $request->description,
            'adresse' => $request->adresse,
            'ville' => $request->ville,
            'prix_par_nuit' => $request->prix_par_nuit,
            'nombre_de_chambres' => $request->nombre_de_chambres,
        ]);

        if ($request->hasFile('images')) {
            $isPrimary = true;
            foreach ($request->file('images') as $image) {
                $path = $image->store('annonces', 's3');
                $annonce->images()->create([
                    'image_path' => $path,
                    'is_primary' => $isPrimary,
                    'sort_order' => $annonce->images->count(),
                ]);
                $isPrimary = false;
            }
        }

        if ($request->filled('amenities')) {
            $annonce->amenities()->sync($request->amenities);
        }

        return redirect()->route('home')->with('success', 'Annonce publiée avec succès !');
    }

    public function show(Annonce $annonce)
    {
        $annonce->load(['reservations.avis.user', 'reservations.user']);
        return view('annonces.show', compact('annonce'));
    }

    public function edit(Annonce $annonce)
    {
        $this->authorize('update', $annonce);
        $amenities = Amenity::all();
        return view('annonces.edit', compact('annonce', 'amenities'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        $this->authorize('update', $annonce);

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'prix_par_nuit' => 'required|numeric|min:0',
            'nombre_de_chambres' => 'required|integer|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $annonce->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'adresse' => $request->adresse,
            'ville' => $request->ville,
            'prix_par_nuit' => $request->prix_par_nuit,
            'nombre_de_chambres' => $request->nombre_de_chambres,
        ]);

        if ($request->hasFile('images')) {
            $sortOrder = $annonce->images->max('sort_order') ?? 0;
            foreach ($request->file('images') as $image) {
                $path = $image->store('annonces', 's3');
                $annonce->images()->create([
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => ++$sortOrder,
                ]);
            }
        }

        if ($request->filled('amenities')) {
            $annonce->amenities()->sync($request->amenities);
        } else {
            $annonce->amenities()->detach();
        }

        return redirect()->route('annonces.show', $annonce)->with('success', 'Annonce mise à jour !');
    }

    public function destroy(Annonce $annonce)
    {
        $this->authorize('delete', $annonce);
        
        if ($annonce->image) {
            Storage::disk('s3')->delete($annonce->image);
        }
        
        $annonce->delete();

        return redirect()->route('home')->with('success', 'Annonce supprimée !');
    }
}
