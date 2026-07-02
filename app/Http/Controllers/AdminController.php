<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Annonce;
use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        $annonces = Annonce::with('user')->latest()->get();
        $reservations = Reservation::with(['user', 'annonce'])->latest()->get();

        return view('admin.index', compact('users', 'annonces', 'reservations'));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Impossible de supprimer le dernier administrateur.');
        }

        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }

    public function deleteAnnonce($id)
    {
        $annonce = Annonce::findOrFail($id);
        $annonce->delete();
        return back()->with('success', 'Annonce supprimée.');
    }
}