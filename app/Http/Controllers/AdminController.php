<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Annonce;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        $annonces = Annonce::with('user')->latest()->paginate(10);
        $reservations = Reservation::with(['user', 'annonce'])->latest()->paginate(10);
        $recentReservations = Reservation::with(['user', 'annonce'])->latest()->limit(6)->get();

        $stats = [
            'totalUsers' => User::count(),
            'totalAnnonces' => Annonce::count(),
            'totalReservations' => Reservation::count(),
            'acceptedReservations' => Reservation::where('status', 'accepted')->count(),
            'pendingReservations' => Reservation::where('status', 'pending')->count(),
            'totalRevenue' => Reservation::where('status', 'accepted')->sum('total_price'),
            'activeHosts' => User::whereHas('annonces')->count(),
            'averageBasket' => (float) Reservation::where('status', 'accepted')->avg('total_price'),
        ];

        $stats['acceptanceRate'] = $stats['totalReservations'] > 0
            ? round(($stats['acceptedReservations'] / $stats['totalReservations']) * 100, 1)
            : 0;

        $monthlyRevenue = collect(range(5, 0))
            ->map(function ($monthsAgo) {
                $month = Carbon::now()->subMonths($monthsAgo);
                $label = $month->translatedFormat('M');

                $amount = Reservation::where('status', 'accepted')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_price');

                return [
                    'label' => $label,
                    'amount' => $amount,
                ];
            })
            ->push([
                'label' => Carbon::now()->translatedFormat('M'),
                'amount' => Reservation::where('status', 'accepted')
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->sum('total_price'),
            ]);

        $topCities = Reservation::query()
            ->join('annonces', 'reservations.annonce_id', '=', 'annonces.id')
            ->where('reservations.status', 'accepted')
            ->selectRaw('annonces.ville as ville, COUNT(*) as reservations_count, SUM(reservations.total_price) as revenue')
            ->groupBy('annonces.ville')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('admin.index', compact('users', 'annonces', 'reservations', 'stats', 'monthlyRevenue', 'topCities', 'recentReservations'));
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