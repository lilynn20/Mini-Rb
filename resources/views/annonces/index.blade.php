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
