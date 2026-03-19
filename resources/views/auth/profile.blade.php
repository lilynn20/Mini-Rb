<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b">
        <a href="{{ route('home') }}" class="flex items-center space-x-2 text-rose-500 hover:text-rose-600 transition">
            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 fill-current"><path d="M16 1c2.008 0 3.463.963 4.751 3.269l.533 1.025c1.954 3.83 6.114 12.54 7.1 14.836l.145.353c.667 1.591.91 2.472.96 3.396l.01.415v.301c0 4.262-2.87 7.405-6.66 7.405-2.008 0-3.463-.963-4.751-3.269l-.533-1.025c-1.954-3.83-6.114-12.54-7.1-14.836l-.145-.353c-.667-1.591-.91-2.472-.96-3.396l-.01-.415v-.301c0-4.262 2.87-7.405 6.66-7.405z"></path></svg>
            <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
        </a>
        <div class="flex items-center space-x-4">
            <a href="{{ route('reservations.index') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Mes Réservations</a>
            <a href="{{ route('annonces.create') }}" class="text-gray-700 font-semibold hover:text-rose-500 transition">Publier</a>
            <span class="text-gray-400">|</span>
            <span class="text-rose-500 font-semibold">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-rose-500 font-semibold text-sm">Déconnexion</button>
            </form>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-8 py-10">

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

        <h1 class="text-3xl font-bold mb-10">Mon Profil</h1>

        {{-- Profile Info Card --}}
        <div class="bg-white rounded-2xl shadow-sm border p-8 mb-8">
            <div class="flex items-center gap-6 mb-8">
                <div class="w-20 h-20 rounded-full bg-rose-100 flex items-center justify-center text-rose-500 text-3xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if($user->hasVerifiedEmail())
                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">✓ Email vérifié</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-1 rounded-full">⚠ Email non vérifié</span>
                        @endif
                        <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-1 rounded-full">{{ ucfirst($user->role) }}</span>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4 mb-8 border-t pt-6">
                <div class="text-center">
                    <p class="text-2xl font-bold text-rose-500">{{ $user->annonces->count() }}</p>
                    <p class="text-gray-500 text-sm">Annonces publiées</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-rose-500">{{ \App\Models\Reservation::where('user_id', $user->id)->count() }}</p>
                    <p class="text-gray-500 text-sm">Réservations effectuées</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-rose-500">{{ $user->created_at->format('Y') }}</p>
                    <p class="text-gray-500 text-sm">Membre depuis</p>
                </div>
            </div>

            {{-- Edit Form --}}
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <h3 class="text-lg font-bold mb-4">Modifier mes informations</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Nom</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" required>
                        <p class="text-xs text-gray-400 mt-1">Si vous changez votre email, vous devrez le vérifier à nouveau.</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Nouveau mot de passe <span class="text-gray-400 font-normal">(laisser vide pour ne pas changer)</span></label>
                        <input type="password" name="password"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                </div>

                <button type="submit" class="mt-6 bg-rose-500 text-white px-8 py-3 rounded-lg font-bold hover:bg-rose-600 transition">
                    Enregistrer les modifications
                </button>
            </form>
        </div>

        {{-- My Annonces --}}
        @if($user->annonces->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border p-8">
            <h3 class="text-lg font-bold mb-6">Mes annonces</h3>
            <div class="space-y-4">
                @foreach($user->annonces as $annonce)
                    <div class="flex items-center justify-between border rounded-xl p-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                                @if($annonce->image)
                                    <img src="{{ Storage::disk('s3')->url($annonce->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-xs">No img</div>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('annonces.show', $annonce) }}" class="font-semibold hover:text-rose-500">{{ $annonce->titre }}</a>
                                <p class="text-gray-500 text-sm">{{ $annonce->ville }} · {{ $annonce->prix_par_nuit }}$/nuit</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('annonces.edit', $annonce) }}" class="text-sm bg-gray-100 px-3 py-1 rounded-lg hover:bg-gray-200 font-semibold">Modifier</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </main>

    <footer class="bg-gray-100 border-t py-10 px-8 text-center text-gray-500 mt-20">
        <p>&copy; 2026 Mini-Rb, by Imane & Naima.</p>
    </footer>
</body>
</html>