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
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b sticky top-0 z-50">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2 text-rose-500 hover:text-rose-600 transition">
                <img src="{{ asset('images/logo.svg') }}" class="h-8 w-8" alt="Mini-Rb Logo">
                <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
            </a>

            <!-- Suggestions Menu -->
            <div class="hidden md:flex items-center space-x-6 text-sm font-semibold text-gray-600">
                <div class="group relative py-4">
                    <button class="hover:text-rose-500 transition flex items-center">
                        Pays populaires
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="absolute top-full left-0 w-48 bg-white shadow-xl rounded-xl py-2 border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <a href="/?ville=France" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">France</a>
                        <a href="/?ville=Maroc" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Maroc</a>
                        <a href="/?ville=Espagne" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Espagne</a>
                        <a href="/?ville=Italie" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Italie</a>
                        <a href="/?ville=USA" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">États-Unis</a>
                    </div>
                </div>
                <div class="group relative py-4">
                    <button class="hover:text-rose-500 transition flex items-center">
                        Villes populaires
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="absolute top-full left-0 w-48 bg-white shadow-xl rounded-xl py-2 border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <a href="/?ville=Paris" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Paris</a>
                        <a href="/?ville=Casablanca" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Casablanca</a>
                        <a href="/?ville=Marrakech" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Marrakech</a>
                        <a href="/?ville=Londres" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Londres</a>
                        <a href="/?ville=Barcelone" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Barcelone</a>
                    </div>
                </div>
            </div>
        </div>

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
    <main class="max-w-7xl mx-auto px-8 pb-10">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded my-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Hero Section & Search Bar Overlay -->
        <div class="relative mb-20">
            <!-- Hero Banner -->
            <div class="relative rounded-3xl overflow-hidden h-[400px] flex items-center justify-center text-center">
                <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Vacances" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                <div class="relative z-10 px-4 pb-12">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 drop-shadow-lg">Trouvez des locations de vacances dans le monde entier</h1>
                    <p class="text-xl md:text-2xl text-white font-medium drop-shadow-md">Réservez vos prochaines vacances dès maintenant !</p>
                </div>
            </div>

            <!-- Barre de recherche (Floating Overlay) -->
            <div class="absolute bottom-0 left-1/2 transform translate-x-1/2 -translate-y-1/2 w-full px-4 max-w-4xl" style="left: 50%; transform: translate(-50%, 50%);">
                <div class="bg-white p-2 rounded-full shadow-2xl border flex items-center">
                    <form action="{{ route('home') }}" method="GET" class="flex w-full items-center">
                        <div class="flex-1 px-6 border-r">
                            <label class="block text-[10px] font-bold uppercase text-gray-500">Destination</label>
                            <input type="text" name="ville" value="{{ request('ville') }}" placeholder="Où allez-vous ?" class="w-full outline-none text-sm font-medium">
                        </div>
                        <div class="flex-1 px-6 border-r">
                            <label class="block text-[10px] font-bold uppercase text-gray-500">Prix Max</label>
                            <input type="number" name="prix_max" value="{{ request('prix_max') }}" placeholder="Budget max" class="w-full outline-none text-sm font-medium">
                        </div>
                        <div class="flex-1 px-6 border-r">
                            <label class="block text-[10px] font-bold uppercase text-gray-500">Nb Personne</label>
                            <input type="number" name="nb_personne" value="{{ request('nb_personne') }}" placeholder="Combien ?" class="w-full outline-none text-sm font-medium" min="1">
                        </div>
                        <div class="px-2">
                            <button type="submit" class="bg-rose-500 text-white p-4 rounded-full hover:bg-rose-600 transition flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span class="ml-2 font-bold hidden md:inline">Rechercher</span>
                            </button>
                        </div>
                        @if(request()->anyFilled(['ville', 'prix_max', 'nb_personne']))
                            <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-rose-500 underline ml-2 pr-4">Effacer</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <h1 class="text-3xl font-bold mb-8">Découvrez votre prochain séjour</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @forelse($annonces as $annonce)
                <a href="{{ route('annonces.show', $annonce) }}" class="group block">
                    <div class="aspect-square overflow-hidden rounded-xl mb-3">
                        @if($annonce->image)
                            <img src="{{ Storage::disk('s3')->url($annonce->image) }}" alt="{{ $annonce->titre }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <img src="https://via.placeholder.com/400x400?text=Pas+d+image" alt="{{ $annonce->titre }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @endif
                    </div>
                    <h3 class="font-bold text-gray-900">{{ $annonce->ville }}</h3>
                    <p class="text-gray-500 text-sm truncate">{{ $annonce->titre }}</p>
                    <p class="mt-1 font-semibold"><span class="text-gray-900">{{ $annonce->prix_par_nuit }}$</span> <span class="text-gray-500 font-normal">par nuit</span></p>
                    
                    <!-- Note et bouton détails -->
                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-yellow-400 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="ml-1 text-gray-700 font-medium">4.5</span>
                        </div>
                        <span class="bg-rose-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase group-hover:bg-rose-600 transition shadow-sm">Détails</span>
                    </div>
                </a>
            @empty
                <p class="text-gray-500 col-span-full text-center py-10">Aucune annonce disponible pour le moment.</p>
            @endforelse
        </div>
        <!-- Section d'information -->
        <div class="mt-20 border-t pt-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-2xl font-bold mb-4">Réservez votre logement en toute simplicité</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Envie de voyager sans stress ? Sur MiniRBnB, trouvez et réservez votre hébergement rapidement, en toute sécurité et sans mauvaise surprise. Découvrez un large choix d’appartements, maisons, villas et chambres adaptées à tous les budgets. Toutes les annonces sont vérifiées et les réservations sont simples et sécurisées. Profitez d’une plateforme fiable, transparente et facile à utiliser pour organiser votre prochain séjour en toute confiance.
                    </p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-4">Trouvez le logement qui vous correspond</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Que vous cherchiez une escapade romantique, un séjour en famille ou un voyage entre amis, MiniRBnB vous aide à trouver l’hébergement idéal. Grâce à nos filtres pratiques, choisissez la ville, les dates, le nombre de voyageurs et les équipements dont vous avez besoin. Commencez dès maintenant et réservez le logement parfait pour votre prochaine aventure.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-100 border-t py-10 px-8 text-center text-gray-500 mt-20">
        <p>&copy; 2026 Mini-Rb, by Imane & Naima.</p>
    </footer>
</body>
</html>
