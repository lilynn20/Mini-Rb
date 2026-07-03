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
        $users = User::latest()->paginate(10);
        $annonces = Annonce::with('user')->latest()->paginate(10);
        $reservations = Reservation::with(['user', 'annonce'])->latest()->paginate(10);

        $stats = [
            'totalUsers' => User::count(),
            'totalAnnonces' => Annonce::count(),
            'totalReservations' => Reservation::count(),
            'acceptedReservations' => Reservation::where('status', 'accepted')->count(),
            'pendingReservations' => Reservation::where('status', 'pending')->count(),
            'totalRevenue' => Reservation::where('status', 'accepted')->sum('total_price'),
        ];

        return view('admin.index', compact('users', 'annonces', 'reservations', 'stats'));
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