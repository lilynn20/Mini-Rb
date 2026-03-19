<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
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

        $annonces = $query->get();
        return view('annonces.index', compact('annonces'));
    }

    public function create()
    {
        return view('annonces.create');
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('annonces', 's3');
        }

        Annonce::create([
            'user_id' => Auth::id(),
            'titre' => $request->titre,
            'description' => $request->description,
            'adresse' => $request->adresse,
            'ville' => $request->ville,
            'prix_par_nuit' => $request->prix_par_nuit,
            'nombre_de_chambres' => $request->nombre_de_chambres,
            'image' => $imagePath,
        ]);

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
        return view('annonces.edit', compact('annonce'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'titre' => $request->titre,
            'description' => $request->description,
            'adresse' => $request->adresse,
            'ville' => $request->ville,
            'prix_par_nuit' => $request->prix_par_nuit,
            'nombre_de_chambres' => $request->nombre_de_chambres,
        ];

        if ($request->hasFile('image')) {
            if ($annonce->image) {
                Storage::disk('s3')->delete($annonce->image);
            }
            $data['image'] = $request->file('image')->store('annonces', 's3');
        }

        $annonce->update($data);

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
