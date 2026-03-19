<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b">
        <a href="{{ route('home') }}" class="text-rose-500 font-bold text-2xl">Mini-Rb</a>
        <div class="flex items-center space-x-4">
            <span class="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-sm font-semibold">Admin</span>
            <a href="{{ route('profile') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">{{ Auth::user()->name }}</a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-rose-500 font-semibold text-sm">Déconnexion</button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-8 py-10">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-2xl p-6 shadow-sm border text-center">
                <p class="text-4xl font-bold text-rose-500">{{ $users->count() }}</p>
                <p class="text-gray-500 mt-1">Utilisateurs</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border text-center">
                <p class="text-4xl font-bold text-rose-500">{{ $annonces->count() }}</p>
                <p class="text-gray-500 mt-1">Annonces</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border text-center">
                <p class="text-4xl font-bold text-rose-500">{{ $reservations->count() }}</p>
                <p class="text-gray-500 mt-1">Réservations</p>
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
                            <td class="px-6 py-4">{{ $annonce->prix_par_nuit }}$</td>
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
                            <td class="px-6 py-4">{{ $reservation->total_price }}$</td>
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
        </div>

    </main>

    <footer class="bg-gray-100 border-t py-10 px-8 text-center text-gray-500 mt-20">
        <p>&copy; 2026 Mini-Rb, by Imane & Naima.</p>
    </footer>
</body>
</html>