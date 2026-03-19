<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b">
        <a href="{{ route('home') }}" class="text-rose-500 font-bold text-2xl">Mini-Rb</a>
        <div class="flex items-center space-x-4">
            <a href="{{ route('reservations.index') }}" class="text-rose-500 font-semibold border-b-2 border-rose-500">Mes Réservations</a>
            <a href="{{ route('annonces.create') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Publier</a>
            <span class="text-gray-400">|</span>
            <a href="{{ route('profile') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">{{ Auth::user()->name }}</a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-rose-500 transition font-semibold text-sm">Déconnexion</button>
            </form>
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

        <h1 class="text-3xl font-bold mb-10">Mes Réservations</h1>

        {{-- TAB: Mes voyages --}}
        <div class="mb-14">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <span>✈️</span> Mes voyages
                <span class="text-sm font-normal text-gray-400">({{ $mesReservations->count() }})</span>
            </h2>

            @forelse($mesReservations as $reservation)
                <div class="bg-white rounded-2xl shadow-sm border mb-4 p-6 flex flex-col md:flex-row gap-6">
                    {{-- Image --}}
                    <div class="w-full md:w-36 h-28 rounded-xl overflow-hidden flex-shrink-0">
                        @if($reservation->annonce->image)
                            <img src="{{ Storage::disk('s3')->url($reservation->annonce->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-sm">Pas d'image</div>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="flex-1">
                        <a href="{{ route('annonces.show', $reservation->annonce) }}" class="text-lg font-bold hover:text-rose-500 transition">
                            {{ $reservation->annonce->titre }}
                        </a>
                        <p class="text-gray-500 text-sm">{{ $reservation->annonce->ville }}</p>
                        <p class="text-gray-600 text-sm mt-1">
                            Du <strong>{{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}</strong>
                            au <strong>{{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}</strong>
                        </p>
                        <p class="text-gray-600 text-sm">Total : <strong>{{ $reservation->total_price }}$</strong></p>
                    </div>

                    {{-- Status + Actions --}}
                    <div class="flex flex-col items-end justify-between gap-3">
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
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-500' }}">
                            {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                        </span>

                        @if(in_array($reservation->status, ['pending', 'accepted']))
                            <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" onsubmit="return confirm('Annuler cette réservation ?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-semibold underline">Annuler</button>
                            </form>
                        @endif

                        {{-- Leave review if accepted and no review yet --}}
                        @if($reservation->status === 'accepted' && $reservation->avis->where('user_id', Auth::id())->isEmpty())
                            <a href="{{ route('annonces.show', $reservation->annonce) }}#avis"
                               class="text-sm text-rose-500 hover:text-rose-700 font-semibold underline">
                                Laisser un avis
                            </a>
                        @elseif($reservation->avis->where('user_id', Auth::id())->isNotEmpty())
                            <span class="text-sm text-gray-400">✓ Avis publié</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border p-10 text-center text-gray-400">
                    Vous n'avez pas encore effectué de réservation.
                    <a href="{{ route('home') }}" class="block mt-2 text-rose-500 font-semibold hover:underline">Découvrir des logements</a>
                </div>
            @endforelse
        </div>

        {{-- TAB: Réservations reçues (host) --}}
        @if($reservationsRecues->count() > 0)
        <div>
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <span>🏠</span> Réservations reçues sur mes annonces
                <span class="text-sm font-normal text-gray-400">({{ $reservationsRecues->count() }})</span>
            </h2>

            @foreach($reservationsRecues as $reservation)
                <div class="bg-white rounded-2xl shadow-sm border mb-4 p-6 flex flex-col md:flex-row gap-6">
                    {{-- Image --}}
                    <div class="w-full md:w-36 h-28 rounded-xl overflow-hidden flex-shrink-0">
                        @if($reservation->annonce->image)
                            <img src="{{ Storage::disk('s3')->url($reservation->annonce->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-sm">Pas d'image</div>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="flex-1">
                        <a href="{{ route('annonces.show', $reservation->annonce) }}" class="text-lg font-bold hover:text-rose-500 transition">
                            {{ $reservation->annonce->titre }}
                        </a>
                        <p class="text-gray-500 text-sm">Voyageur : <strong>{{ $reservation->user->name }}</strong></p>
                        <p class="text-gray-600 text-sm mt-1">
                            Du <strong>{{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}</strong>
                            au <strong>{{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}</strong>
                        </p>
                        <p class="text-gray-600 text-sm">Total : <strong>{{ $reservation->total_price }}$</strong></p>
                    </div>

                    {{-- Status + Actions --}}
                    <div class="flex flex-col items-end justify-between gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-500' }}">
                            {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                        </span>

                        @if($reservation->status === 'pending')
                            <div class="flex gap-2">
                                <form action="{{ route('reservations.accept', $reservation->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-500 text-white px-4 py-1 rounded-lg text-sm font-semibold hover:bg-green-600 transition">Accepter</button>
                                </form>
                                <form action="{{ route('reservations.refuse', $reservation->id) }}" method="POST" onsubmit="return confirm('Refuser cette réservation ?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-red-100 text-red-600 px-4 py-1 rounded-lg text-sm font-semibold hover:bg-red-200 transition">Refuser</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

    </main>

    <footer class="bg-gray-100 border-t py-10 px-8 text-center text-gray-500 mt-20">
        <p>&copy; 2026 Mini-Rb, by Imane & Naima.</p>
    </footer>
</body>
</html>
