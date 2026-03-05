<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnonceController extends Controller
{
    public function index()
    {
        $annonces = Annonce::latest()->get();
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
            $imagePath = $request->file('image')->store('annonces', 'public');
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
                Storage::disk('public')->delete($annonce->image);
            }
            $data['image'] = $request->file('image')->store('annonces', 'public');
        }

        $annonce->update($data);

        return redirect()->route('annonces.show', $annonce)->with('success', 'Annonce mise à jour !');
    }

    public function destroy(Annonce $annonce)
    {
        $this->authorize('delete', $annonce);
        
        if ($annonce->image) {
            Storage::disk('public')->delete($annonce->image);
        }
        
        $annonce->delete();

        return redirect()->route('home')->with('success', 'Annonce supprimée !');
    }
}
