<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b sticky top-0 z-50">
        <div class="flex items-center space-x-8">
            <a href="{{ route('home') }}" class="flex items-center space-x-2 text-rose-500 hover:text-rose-600 transition">
                <img src="{{ asset('images/logo.png') }}" class="h-8 w-8" alt="Mini-Rb Logo">
                <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
            </a>
        </div>

        <div class="flex items-center space-x-4">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.index') }}" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold hover:bg-purple-200 transition">Dashboard Admin</a>
                @endif
                <a href="{{ route('reservations.index') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Mes Réservations</a>
                <a href="{{ route('annonces.create') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Publier</a>
                <span class="text-gray-400">|</span>
                <a href="{{ route('profile') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">{{ Auth::user()->name }}</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-rose-500 transition font-semibold text-sm focus:outline-none">Déconnexion</button>
                </form>
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

        <h1 class="text-3xl font-bold mb-8">Mes Favoris</h1>

        @if($favorites->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 mb-12">
                @foreach($favorites as $annonce)
                    <div class="group">
                        <a href="{{ route('annonces.show', $annonce) }}" class="block">
                            <div class="aspect-square overflow-hidden rounded-xl mb-3 relative">
                                @php
                                    if($annonce->images->count()) {
                                        $imgUrl = \Storage::disk('s3')->url($annonce->images->where('is_primary', true)->first()->image_path ?? $annonce->images->first()->image_path);
                                    } else {
                                        $imgUrl = $annonce->image
                                            ? (\Illuminate\Support\Str::startsWith($annonce->image, 'http') ? $annonce->image : \Storage::disk('s3')->url($annonce->image))
                                            : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&q=80&fit=crop';
                                    }
                                @endphp
                                <img src="{{ $imgUrl }}" alt="{{ $annonce->titre }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                                <form action="{{ route('favorites.toggle', $annonce->id) }}" method="POST" class="absolute top-2 right-2" onclick="event.stopPropagation()">
                                    @csrf
                                    <button type="submit" class="p-2 bg-white rounded-full shadow hover:bg-rose-50 transition">
                                        <svg class="w-5 h-5 fill-rose-500 text-rose-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                    </button>
                                </form>
                            </div>
                        </a>
                        <h3 class="font-bold text-gray-900">{{ $annonce->ville }}</h3>
                        <p class="text-gray-500 text-sm truncate">{{ $annonce->titre }}</p>
                        @if($annonce->amenities->count())
                            <div class="flex gap-1 mt-1 flex-wrap">
                                @foreach($annonce->amenities->take(2) as $amenity)
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $amenity->icon }}</span>
                                @endforeach
                            </div>
                        @endif
                        <p class="mt-2 font-semibold"><span class="text-gray-900">{{ $annonce->prix_par_nuit }} DH</span> <span class="text-gray-500 font-normal">par nuit</span></p>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $favorites->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
                <p class="text-gray-500 text-lg">Aucun favori pour le moment</p>
                <a href="{{ route('home') }}" class="inline-block mt-4 text-rose-500 hover:text-rose-600 font-semibold">Découvrir des annonces</a>
            </div>
        @endif
    </main>
</body>
</html>
