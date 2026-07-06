<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm py-3 sm:py-4 px-4 sm:px-8 flex justify-between items-center border-b sticky top-0 z-50">
        <a href="{{ route('home') }}" class="flex items-center space-x-2 text-rose-500 hover:text-rose-600 transition">
            <img src="{{ asset('images/logo.png') }}" class="h-8 w-8" alt="Mini-Rb Logo">
            <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
        </a>
        <div class="flex items-center space-x-2 sm:space-x-4">
            <span class="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-xs sm:text-sm font-semibold">Admin</span>
            <a href="{{ route('profile') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition text-sm sm:text-base">{{ Auth::user()->name }}</a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-rose-500 font-semibold text-xs sm:text-sm">Déconnexion</button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-8 py-6 sm:py-10">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-8 sm:mb-10">
            <div class="mb-4 flex flex-col gap-1">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tableau de Bord Administrateur</h1>
                <p class="text-sm text-gray-500">Vue d'ensemble de l'activité de la plateforme.</p>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-6">
                <div class="bg-white rounded-2xl p-5 shadow-sm border">
                    <p class="text-sm text-gray-500">Utilisateurs</p>
                    <p class="mt-2 text-3xl font-bold text-rose-500">{{ $stats['totalUsers'] }}</p>
                    <p class="mt-2 text-xs text-gray-400">Hôtes actifs: {{ $stats['activeHosts'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border">
                    <p class="text-sm text-gray-500">Annonces</p>
                    <p class="mt-2 text-3xl font-bold text-blue-500">{{ $stats['totalAnnonces'] }}</p>
                    <p class="mt-2 text-xs text-gray-400">Capacité totale publiée</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border">
                    <p class="text-sm text-gray-500">Réservations</p>
                    <p class="mt-2 text-3xl font-bold text-green-500">{{ $stats['totalReservations'] }}</p>
                    <p class="mt-2 text-xs text-gray-400">Taux d'acceptation: {{ $stats['acceptanceRate'] }}%</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border">
                    <p class="text-sm text-gray-500">Chiffre d'affaires</p>
                    <p class="mt-2 text-3xl font-bold text-purple-500">{{ number_format($stats['totalRevenue'], 0, ',', ' ') }} DH</p>
                    <p class="mt-2 text-xs text-gray-400">Panier moyen: {{ number_format($stats['averageBasket'], 0, ',', ' ') }} DH</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-gray-900">Revenus des 6 derniers mois</h2>
                        <span class="text-xs text-gray-400">Réservations acceptées</span>
                    </div>
                    @php
                        $maxRevenue = max(1, $monthlyRevenue->max('amount'));
                    @endphp
                    <div class="space-y-3">
                        @foreach($monthlyRevenue as $item)
                            <div>
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                    <span>{{ $item['label'] }}</span>
                                    <span>{{ number_format($item['amount'], 0, ',', ' ') }} DH</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full bg-rose-500" style="width: {{ round(($item['amount'] / $maxRevenue) * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border">
                    <h2 class="font-semibold text-gray-900 mb-4">Santé de la plateforme</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">En attente</span>
                            <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">{{ $stats['pendingReservations'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Acceptées</span>
                            <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 font-semibold">{{ $stats['acceptedReservations'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Taux d'acceptation</span>
                            <span class="font-semibold text-gray-900">{{ $stats['acceptanceRate'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mt-6">
                <div class="bg-white rounded-2xl p-5 shadow-sm border">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-gray-900">Top villes (revenus)</h2>
                        <span class="text-xs text-gray-400">Top 5</span>
                    </div>
                    @if($topCities->isEmpty())
                        <p class="text-sm text-gray-400">Aucune donnée disponible pour le moment.</p>
                    @else
                        <div class="space-y-3">
                            @php
                                $maxCityRevenue = max(1, $topCities->max('revenue'));
                            @endphp
                            @foreach($topCities as $city)
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ $city->ville }}</span>
                                        <span class="text-gray-500">{{ number_format($city->revenue, 0, ',', ' ') }} DH</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden mb-1">
                                        <div class="h-full bg-purple-500" style="width: {{ round(($city->revenue / $maxCityRevenue) * 100) }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-400">{{ $city->reservations_count }} réservation(s) acceptée(s)</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-gray-900">Activité récente</h2>
                        <span class="text-xs text-gray-400">6 dernières</span>
                    </div>
                    <div class="space-y-3">
                        @foreach($recentReservations as $r)
                            @php
                                $activityColor = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'accepted' => 'bg-emerald-100 text-emerald-700',
                                    'refused' => 'bg-red-100 text-red-700',
                                    'cancelled' => 'bg-gray-100 text-gray-600',
                                ][$r->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $r->annonce->titre }}</p>
                                    <p class="text-xs text-gray-400">{{ $r->user->name }} • {{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $activityColor }}">{{ ucfirst($r->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Users --}}
        <div class="bg-white rounded-2xl shadow-sm border mb-10">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">👥 Utilisateurs</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Nom</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Rôle</th>
                            <th class="px-6 py-3 text-left">Inscrit le</th>
                            <th class="px-6 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' :
                                       ($user->role === 'hote' ? 'bg-blue-100 text-blue-700' :
                                       'bg-green-100 text-green-700') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                @if($user->id !== Auth::id())
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Supprimer</button>
                                </form>
                                @else
                                <span class="text-gray-300 text-xs">Vous</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- Annonces --}}
        <div class="bg-white rounded-2xl shadow-sm border mb-10">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">🏠 Annonces</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Titre</th>
                            <th class="px-6 py-3 text-left">Ville</th>
                            <th class="px-6 py-3 text-left">Prix/nuit</th>
                            <th class="px-6 py-3 text-left">Propriétaire</th>
                            <th class="px-6 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($annonces as $annonce)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">
                                <a href="{{ route('annonces.show', $annonce) }}" class="hover:text-rose-500">
                                    {{ $annonce->titre }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $annonce->ville }}</td>
                            <td class="px-6 py-4">{{ $annonce->prix_par_nuit }} DH</td>
                            <td class="px-6 py-4 text-gray-500">{{ $annonce->user->name }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.annonces.delete', $annonce->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cette annonce ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($annonces->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $annonces->links() }}
                </div>
            @endif
        </div>

        {{-- Reservations --}}
        <div class="bg-white rounded-2xl shadow-sm border">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">📅 Réservations</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Annonce</th>
                            <th class="px-6 py-3 text-left">Voyageur</th>
                            <th class="px-6 py-3 text-left">Dates</th>
                            <th class="px-6 py-3 text-left">Total</th>
                            <th class="px-6 py-3 text-left">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($reservations as $reservation)
                        @php
                            $statusColors = [
                                'pending'   => 'bg-yellow-100 text-yellow-700',
                                'accepted'  => 'bg-green-100 text-green-700',
                                'refused'   => 'bg-red-100 text-red-600',
                                'cancelled' => 'bg-gray-100 text-gray-500',
                            ];
                            $statusLabels = [
                                'pending'   => 'En attente',
                                'accepted'  => 'Acceptée',
                                'refused'   => 'Refusée',
                                'cancelled' => 'Annulée',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">{{ $reservation->annonce->titre }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $reservation->user->name }}</td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}
                                → {{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">{{ $reservation->total_price }} DH</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$reservation->status] ?? '' }}">
                                    {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($reservations->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>

    </main>

    <footer class="bg-gray-100 border-t py-8 sm:py-10 px-4 sm:px-8 text-center text-gray-500 mt-12 sm:mt-20">
        <p>&copy; 2026 Mini-Rb, by Imane & Naima.</p>
    </footer>
</body>
</html>