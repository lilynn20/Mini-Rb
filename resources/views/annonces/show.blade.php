<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $annonce->titre }} - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white">
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b sticky top-0 z-50">
        <div class="flex items-center space-x-8">
            <a href="{{ route('home') }}" class="flex items-center space-x-2 text-rose-500 hover:text-rose-600 transition">
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 fill-current"><path d="M16 1c2.008 0 3.463.963 4.751 3.269l.533 1.025c1.954 3.83 6.114 12.54 7.1 14.836l.145.353c.667 1.591.91 2.472.96 3.396l.01.415v.301c0 4.262-2.87 7.405-6.66 7.405-2.008 0-3.463-.963-4.751-3.269l-.533-1.025c-1.954-3.83-6.114-12.54-7.1-14.836l-.145-.353c-.667-1.591-.91-2.472-.96-3.396l-.01-.415v-.301c0-4.262 2.87-7.405 6.66-7.405z"></path></svg>
                <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
            </a>
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
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.index') }}" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold hover:bg-purple-200 transition">Dashboard Admin</a>
                @endif
                <a href="{{ route('reservations.index') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Mes Réservations</a>
                <a href="{{ route('annonces.create') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Publier</a>
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

    <main class="max-w-5xl mx-auto px-8 py-10">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <h1 class="text-3xl font-bold mb-4">{{ $annonce->titre }}</h1>
        <p class="text-gray-600 mb-6 underline font-semibold">{{ $annonce->adresse }}, {{ $annonce->ville }}</p>

        <div class="rounded-2xl overflow-hidden mb-10 h-[500px]">
            @if($annonce->image)
                <img src="{{ Storage::disk('s3')->url($annonce->image) }}" alt="{{ $annonce->titre }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-400 text-xl">Pas d'image</span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="md:col-span-2">
                <div class="flex justify-between items-start mb-2">
                    <h2 class="text-2xl font-bold">Logement proposé par {{ $annonce->user->name }}</h2>
                    @can('update', $annonce)
                        <div class="flex space-x-2">
                            <a href="{{ route('annonces.edit', $annonce) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition">Modifier</a>
                            <form action="{{ route('annonces.destroy', $annonce) }}" method="POST" onsubmit="return confirm('Supprimer cette annonce ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-100 transition">Supprimer</button>
                            </form>
                        </div>
                    @endcan
                </div>
                <p class="text-gray-600 mb-6 border-b pb-6">{{ $annonce->nombre_de_chambres }} chambre(s)</p>

                <h3 class="text-xl font-bold mb-4">À propos de ce logement</h3>
                <p class="text-gray-700 leading-relaxed mb-10">{{ $annonce->description }}</p>

                {{-- Reviews Section --}}
                <div class="border-t pt-8">
                    <h3 class="text-xl font-bold mb-6">
                        Avis
                        @php
                            $allAvis = $annonce->reservations->flatMap->avis;
                            $avgRating = $allAvis->count() ? round($allAvis->avg('rating'), 1) : null;
                        @endphp
                        @if($avgRating)
                            <span class="text-base font-normal text-gray-500 ml-2">
                                ★ {{ $avgRating }} · {{ $allAvis->count() }} avis
                            </span>
                        @endif
                    </h3>

                    @forelse($annonce->reservations->flatMap->avis->sortByDesc('created_at') as $avis)
                        <div class="mb-6 pb-6 border-b last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">{{ $avis->user->name }}</p>
                                    <p class="text-yellow-400 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $avis->rating ? '★' : '☆' }}
                                        @endfor
                                    </p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-gray-400 text-sm">{{ $avis->created_at->format('d/m/Y') }}</span>
                                    @auth
                                        @if(Auth::id() === $avis->user_id)
                                            <form action="{{ route('avis.destroy', $avis->id) }}" method="POST" onsubmit="return confirm('Supprimer cet avis ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 text-sm">Supprimer</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                            <p class="text-gray-700 mt-2">{{ $avis->comment }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500">Aucun avis pour ce logement.</p>
                    @endforelse

                    {{-- Leave a review: only for accepted reservations --}}
                    @auth
                        @php
                            $eligibleReservation = $annonce->reservations
                                ->where('user_id', Auth::id())
                                ->where('status', 'accepted')
                                ->filter(fn($r) => $r->avis->where('user_id', Auth::id())->isEmpty())
                                ->first();
                        @endphp
                        @if($eligibleReservation)
                            <div class="mt-8 bg-gray-50 rounded-xl p-6">
                                <h4 class="font-bold text-lg mb-4">Laisser un avis</h4>
                                <form action="{{ route('avis.store', $eligibleReservation->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-gray-700 font-semibold mb-2">Note</label>
                                        <div class="flex space-x-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="rating" value="{{ $i }}" class="peer hidden" {{ $i == 5 ? 'checked' : '' }}>
                                                    <span class="text-2xl peer-checked:text-yellow-400 text-gray-300 hover:text-yellow-300 transition">★</span>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 font-semibold mb-2">Commentaire</label>
                                        <textarea name="comment" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Décrivez votre séjour..." required></textarea>
                                    </div>
                                    <button type="submit" class="bg-rose-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-rose-600 transition">Publier l'avis</button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Reservation Card --}}
            <div class="md:col-span-1">
                <div class="border rounded-2xl p-6 shadow-xl sticky top-20">
                    <p class="text-2xl font-bold mb-6">
                        <span class="text-gray-900">{{ $annonce->prix_par_nuit }}$</span>
                        <span class="text-gray-500 font-normal text-base"> par nuit</span>
                    </p>

                    @auth
                        @if(Auth::id() !== $annonce->user_id)
                            <form action="{{ route('reservations.store', $annonce->id) }}" method="POST">
                                @csrf
                                <div class="border rounded-lg mb-4">
                                    <div class="grid grid-cols-2 border-b">
                                        <div class="p-3 border-r">
                                            <label class="block text-[10px] font-bold uppercase">Arrivée</label>
                                            <input type="date" name="start_date" min="{{ date('Y-m-d') }}" class="w-full text-sm outline-none" required>
                                        </div>
                                        <div class="p-3">
                                            <label class="block text-[10px] font-bold uppercase">Départ</label>
                                            <input type="date" name="end_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full text-sm outline-none" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition">Réserver</button>
                            </form>
                        @else
                            <div class="text-center py-4 text-gray-500 bg-gray-50 rounded-lg">
                                C'est votre annonce
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition text-center">
                            Connectez-vous pour réserver
                        </a>
                    @endauth

                    <p class="text-center text-gray-500 text-sm mt-4">Aucun montant ne vous sera débité pour le moment</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-100 border-t py-10 px-8 text-center text-gray-500 mt-20">
        <p>&copy; 2026 Mini-Rb, by Imane & Naima.</p>
    </footer>
</body>
</html>