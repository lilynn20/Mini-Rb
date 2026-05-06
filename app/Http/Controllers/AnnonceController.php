<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\AnnonceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AnnonceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Annonce::with('images')->latest();

        if ($request->filled('ville')) {
            $query->where('ville', 'like', '%' . $request->ville . '%');
        }

        if ($request->filled('prix_max')) {
            $query->where('prix_par_nuit', '<=', $request->prix_max);
        }

        if ($request->filled('nb_personne')) {
            $query->where('nombre_de_chambres', '>=', ceil($request->nb_personne / 2));
        }

        $annonces = $query->paginate(12)->through(fn ($a) => $this->formatAnnonce($a));

        return response()->json($annonces);
    }

    public function show(Annonce $annonce)
    {
        $annonce->load(['user', 'images', 'reservations.avis.user', 'reservations.user']);

        $allAvis = $annonce->reservations->flatMap->avis;

        $avis = $allAvis->sortByDesc('created_at')->values()->map(function ($a) {
            return [
                'id'         => $a->id,
                'rating'     => $a->rating,
                'comment'    => $a->comment,
                'created_at' => $a->created_at,
                'user_id'    => $a->user_id,
                'user_name'  => $a->user->name,
            ];
        });

        $userId = Auth::id();
        $eligibleReservation = null;
        if ($userId) {
            $r = $annonce->reservations
                ->where('user_id', $userId)
                ->where('status', 'accepted')
                ->filter(fn ($r) => $r->avis->where('user_id', $userId)->isEmpty())
                ->first();
            if ($r) {
                $eligibleReservation = ['id' => $r->id];
            }
        }

        return response()->json([
            'annonce' => array_merge($this->formatAnnonce($annonce), [
                'description'        => $annonce->description,
                'adresse'            => $annonce->adresse,
                'nombre_de_chambres' => $annonce->nombre_de_chambres,
                'user_id'            => $annonce->user_id,
                'host_name'          => $annonce->user->name,
            ]),
            'avis'                  => $avis,
            'avg_rating'            => $allAvis->count() ? round($allAvis->avg('rating'), 1) : null,
            'avis_count'            => $allAvis->count(),
            'eligible_reservation'  => $eligibleReservation,
            'can_update'            => $userId ? Auth::user()->can('update', $annonce) : false,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'              => 'required|string|max:255',
            'description'        => 'required|string',
            'adresse'            => 'required|string|max:255',
            'ville'              => 'required|string|max:255',
            'prix_par_nuit'      => 'required|numeric|min:0',
            'nombre_de_chambres' => 'required|integer|min:1',
            'images'             => 'nullable|array|max:10',
            'images.*'           => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $annonce = Annonce::create([
            'user_id'            => Auth::id(),
            'titre'              => $request->titre,
            'description'        => $request->description,
            'adresse'            => $request->adresse,
            'ville'              => $request->ville,
            'prix_par_nuit'      => $request->prix_par_nuit,
            'nombre_de_chambres' => $request->nombre_de_chambres,
        ]);

        $this->saveImages($annonce, $request->file('images') ?? []);

        return response()->json([
            'message' => 'Annonce publiée avec succès !',
            'annonce' => $this->formatAnnonce($annonce->load('images')),
        ], 201);
    }

    public function update(Request $request, Annonce $annonce)
    {
        $this->authorize('update', $annonce);

        $request->validate([
            'titre'              => 'required|string|max:255',
            'description'        => 'required|string',
            'adresse'            => 'required|string|max:255',
            'ville'              => 'required|string|max:255',
            'prix_par_nuit'      => 'required|numeric|min:0',
            'nombre_de_chambres' => 'required|integer|min:1',
            'images'             => 'nullable|array|max:10',
            'images.*'           => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'deleted_image_ids'  => 'nullable|array',
            'deleted_image_ids.*' => 'integer',
        ]);

        $annonce->update($request->only([
            'titre', 'description', 'adresse', 'ville', 'prix_par_nuit', 'nombre_de_chambres',
        ]));

        if ($request->filled('deleted_image_ids')) {
            $toDelete = $annonce->images()->whereIn('id', $request->deleted_image_ids)->get();
            foreach ($toDelete as $img) {
                Storage::disk('s3')->delete($img->path);
                $img->delete();
            }
        }

        $this->saveImages($annonce, $request->file('images') ?? []);

        return response()->json([
            'message' => 'Annonce mise à jour !',
            'annonce' => $this->formatAnnonce($annonce->fresh('images')),
        ]);
    }

    public function destroy(Annonce $annonce)
    {
        $this->authorize('delete', $annonce);

        foreach ($annonce->images as $img) {
            Storage::disk('s3')->delete($img->path);
        }

        $annonce->delete();

        return response()->json(['message' => 'Annonce supprimée !']);
    }

    private function saveImages(Annonce $annonce, array $files): void
    {
        $nextPosition = $annonce->images()->max('position') + 1;
        foreach ($files as $file) {
            $path = $file->store('annonces', 's3');
            AnnonceImage::create([
                'annonce_id' => $annonce->id,
                'path'       => $path,
                'position'   => $nextPosition++,
            ]);
        }
    }

    private function formatAnnonce(Annonce $a): array
    {
        $images = $a->relationLoaded('images') ? $a->images : $a->images()->get();

        $imageUrls = $images->map(fn ($img) => [
            'id'  => $img->id,
            'url' => Storage::disk('s3')->url($img->path),
        ])->values();

        return [
            'id'                 => $a->id,
            'titre'              => $a->titre,
            'description'        => $a->description,
            'adresse'            => $a->adresse,
            'ville'              => $a->ville,
            'prix_par_nuit'      => $a->prix_par_nuit,
            'nombre_de_chambres' => $a->nombre_de_chambres,
            'images'             => $imageUrls,
            'image_url'          => $imageUrls->first()['url'] ?? null,
            'user_id'            => $a->user_id,
            'created_at'         => $a->created_at,
        ];
    }
}
