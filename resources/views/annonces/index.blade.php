<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini-Rb - Airbnb Clone</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b">
        <a href="{{ route('home') }}" class="text-rose-500 font-bold text-2xl">Mini-Rb</a>
        <div class="flex items-center space-x-4">
                @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.index') }}" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold hover:bg-purple-200 transition">Dashboard Admin</a>
                @endif
                <a href="{{ route('reservations.index') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Mes Réservations</a>
                <a href="{{ route('annonces.create') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Mettre mon logement sur Mini-Rb</a>
                <span class="text-gray-400">|</span>
                <span class="text-gray-700 font-semibold">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-rose-500 transition font-semibold text-sm focus:outline-none">Déconnexion</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Connexion</a>
                <a href="{{ route('register') }}" class="bg-rose-500 text-white px-4 py-2 rounded-full font-semibold hover:bg-rose-600 transition">Inscription</a>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-8 py-10">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Barre de recherche -->
        <div class="mb-10 bg-white p-4 rounded-full shadow-lg border flex items-center max-w-4xl mx-auto">
            <form action="{{ route('home') }}" method="GET" class="flex w-full items-center">
                <div class="flex-1 px-4 border-r">
                    <label class="block text-[10px] font-bold uppercase text-gray-500">Destination</label>
                    <input type="text" name="ville" value="{{ request('ville') }}" placeholder="Où allez-vous ?" class="w-full outline-none text-sm">
                </div>
                <div class="flex-1 px-4 border-r">
                    <label class="block text-[10px] font-bold uppercase text-gray-500">Prix Max</label>
                    <input type="number" name="prix_max" value="{{ request('prix_max') }}" placeholder="Budget max" class="w-full outline-none text-sm">
                </div>
                <div class="px-4">
                    <button type="submit" class="bg-rose-500 text-white p-3 rounded-full hover:bg-rose-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
                @if(request()->anyFilled(['ville', 'prix_max']))
                    <a href="{{ route('home') }}" class="text-xs text-gray-500 underline ml-2">Effacer</a>
                @endif
            </form>
        </div>

        <h1 class="text-3xl font-bold mb-8">Découvrez votre prochain séjour</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @forelse($annonces as $annonce)
                <a href="{{ route('annonces.show', $annonce) }}" class="group">
                    <div class="aspect-square overflow-hidden rounded-xl mb-3">
                        @if($annonce->image)
                            <img src="{{ Storage::url($annonce->image) }}" alt="{{ $annonce->titre }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <img src="https://via.placeholder.com/400x400?text=Pas+d+image" alt="{{ $annonce->titre }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @endif
                    </div>
                    <h3 class="font-bold text-gray-900">{{ $annonce->ville }}</h3>
                    <p class="text-gray-500 text-sm truncate">{{ $annonce->titre }}</p>
                    <p class="mt-1 font-semibold"><span class="text-gray-900">{{ $annonce->prix_par_nuit }}$</span> <span class="text-gray-500 font-normal">par nuit</span></p>
                </a>
            @empty
                <p class="text-gray-500 col-span-full text-center py-10">Aucune annonce disponible pour le moment.</p>
            @endforelse
        </div>
    </main>

    <footer class="bg-gray-100 border-t py-10 px-8 text-center text-gray-500 mt-20">
        <p>&copy; 2026 Mini-Rb, by Imane & Naima.</p>
    </footer>
</body>
</html>
