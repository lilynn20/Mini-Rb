<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $annonce->titre }} - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>
</head>
<body class="bg-white">
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b sticky top-0 z-50">
        <div class="flex items-center space-x-8">
            <a href="{{ route('home') }}" class="flex items-center space-x-2 text-rose-500 hover:text-rose-600 transition">
                <img src="{{ asset('images/logo.png') }}" class="h-8 w-8" alt="Mini-Rb Logo">
                <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
            </a>
            <div class="hidden md:flex items-center space-x-6 text-sm font-semibold text-gray-600">
                <div class="group relative py-4">
                    <button class="hover:text-rose-500 transition flex items-center">
                        Pays populaires
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="absolute top-full left-0 w-48 bg-white shadow-xl rounded-xl py-2 border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <a href="/?ville=Marrakech" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Marrakech</a>
                        <a href="/?ville=Fès" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Fès</a>
                        <a href="/?ville=Casablanca" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Casablanca</a>
                        <a href="/?ville=Rabat" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Rabat</a>
                        <a href="/?ville=Tanger" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Tanger</a>
                    </div>
                </div>
                <div class="group relative py-4">
                    <button class="hover:text-rose-500 transition flex items-center">
                        Villes populaires
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="absolute top-full left-0 w-48 bg-white shadow-xl rounded-xl py-2 border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <a href="/?ville=Marrakech" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Marrakech</a>
                        <a href="/?ville=Casablanca" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Casablanca</a>
                        <a href="/?ville=Chefchaouen" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Chefchaouen</a>
                        <a href="/?ville=Agadir" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Agadir</a>
                        <a href="/?ville=Essaouira" class="block px-4 py-2 hover:bg-gray-50 hover:text-rose-500">Essaouira</a>
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
                <a href="{{ route('profile') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">{{ Auth::user()->name }}</a>
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

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $annonce->titre }}</h1>
                    <p class="text-gray-600 underline font-semibold">{{ $annonce->adresse }}, {{ $annonce->ville }}</p>
                </div>
                <div class="flex flex-col gap-2 items-end">
                    @auth
                        @if(Auth::id() !== $annonce->user_id)
                            <div x-data="{ isFavorited: {{ Auth::user()->favorites()->where('annonce_id', $annonce->id)->exists() ? 'true' : 'false' }} }">
                                <button @click="
                                    fetch('{{ route('favorites.toggle', $annonce->id) }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                    }).then(r => r.json()).then(data => {
                                        if (data.success) isFavorited = data.isFavorited;
                                    }).catch(e => console.error('Error:', e));
                                " class="flex items-center space-x-2 px-4 py-2 border rounded-lg hover:bg-rose-50 transition">
                                    <svg class="w-5 h-5 transition" :class="isFavorited ? 'fill-rose-500 text-rose-500' : 'text-gray-400'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                    <span x-text="isFavorited ? 'Retirer' : 'Ajouter'"></span> à favoris
                                </button>
                            </div>
                        @endif
                    @endauth
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
            </div>
        </div>

        {{-- Two-Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_1.3fr] gap-12">
            {{-- LEFT COLUMN: Carousel + Description + Amenities + Reviews --}}
            <div>
                {{-- Carousel --}}
                @if($annonce->images->count())
                    <div class="rounded-2xl overflow-hidden mb-10 h-[500px] relative" x-data="{ currentImage: 0, images: {{ json_encode($annonce->images->pluck('image_path')) }} }">
                        <img :src="'{{ \Config::get('filesystems.disks.s3.url') }}/' + images[currentImage]" :alt="'Image ' + (currentImage + 1)" class="w-full h-full object-cover">
                        @if($annonce->images->count() > 1)
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 z-10">
                                <template x-for="(img, index) in images">
                                    <button @click="currentImage = index" class="w-2 h-2 rounded-full transition" :class="currentImage === index ? 'bg-rose-500' : 'bg-white'"></button>
                                </template>
                            </div>
                            <button @click="currentImage = (currentImage - 1 + images.length) % images.length" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white px-3 py-2 rounded-full hover:bg-gray-100 z-10">←</button>
                            <button @click="currentImage = (currentImage + 1) % images.length" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white px-3 py-2 rounded-full hover:bg-gray-100 z-10">→</button>
                        @endif
                    </div>
                @else
                    <div class="rounded-2xl overflow-hidden mb-10 h-[500px]">
                        @php
                            $imgUrl = $annonce->image
                                ? (\Illuminate\Support\Str::startsWith($annonce->image, 'http') ? $annonce->image : \Storage::disk('s3')->url($annonce->image))
                                : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&q=80&fit=crop';
                        @endphp
                        <img src="{{ $imgUrl }}" alt="{{ $annonce->titre }}" class="w-full h-full object-cover">
                    </div>
                @endif

                {{-- Description --}}
                <div class="mb-10">
                    <p class="text-gray-600 mb-6 border-b pb-6">{{ $annonce->nombre_de_chambres }} chambre(s)</p>
                    <h3 class="text-xl font-bold mb-4">À propos de ce logement</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $annonce->description }}</p>
                </div>

                {{-- Amenities --}}
                @if($annonce->amenities->count())
                    <div class="mb-10">
                        <h3 class="text-xl font-bold mb-6">Équipements</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            @foreach($annonce->amenities as $amenity)
                                <div class="flex items-center">
                                    <span class="text-3xl mr-3">{{ $amenity->icon }}</span>
                                    <span class="text-gray-700">{{ $amenity->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

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

            {{-- RIGHT COLUMN: Sticky Reservation Card --}}
            <div class="lg:sticky lg:top-24 h-fit">
                <div class="border rounded-2xl p-6 shadow-xl">
                    <p class="text-2xl font-bold mb-6">
                        <span class="text-gray-900">{{ $annonce->prix_par_nuit }} DH</span>
                        <span class="text-gray-500 font-normal text-base"> par nuit</span>
                    </p>

                    @auth
                        @if(Auth::id() !== $annonce->user_id)
                            <form action="{{ route('reservations.store', $annonce->id) }}" method="POST">
                                @csrf
                                <div class="border rounded-lg mb-4 relative">
                                    <div class="grid grid-cols-2 border-b">
                                        <div class="p-3 border-r">
                                            <label class="block text-[10px] font-bold uppercase">Arrivée</label>
                                            <input type="date" id="start_date" name="start_date" placeholder="dd/mm/yyyy" class="w-full text-sm outline-none" required>
                                        </div>
                                        <div class="p-3">
                                            <label class="block text-[10px] font-bold uppercase">Départ</label>
                                            <input type="date" id="end_date" name="end_date" placeholder="dd/mm/yyyy" class="w-full text-sm outline-none" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition">Réserver</button>
                            </form>
                            <script>
                                flatpickr("#start_date", {
                                    mode: "range",
                                    minDate: "today",
                                    disable: @json($blockedDates),
                                    plugins: [new rangePlugin({
                                        input: "#end_date"
                                    })],
                                    dateFormat: "Y-m-d",
                                    locale: "fr",
                                    showMonths: 2
                                });
                            </script>
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