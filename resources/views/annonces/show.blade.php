<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $annonce->titre }} - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b">
        <a href="{{ route('home') }}" class="text-rose-500 font-bold text-2xl">Mini-Rb</a>
    </nav>

    <main class="max-w-5xl mx-auto px-8 py-10">
        <h1 class="text-3xl font-bold mb-4">{{ $annonce->titre }}</h1>
        <p class="text-gray-600 mb-6 underline font-semibold">{{ $annonce->adresse }}, {{ $annonce->ville }}</p>

        <div class="rounded-2xl overflow-hidden mb-10 h-[500px]">
            @if($annonce->image)
                <img src="{{ Storage::url($annonce->image) }}" alt="{{ $annonce->titre }}" class="w-full h-full object-cover">
            @else
                <img src="https://via.placeholder.com/1200x800?text=Pas+d+image" alt="{{ $annonce->titre }}" class="w-full h-full object-cover">
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="md:col-span-2">
                <div class="flex justify-between items-start mb-2">
                    <h2 class="text-2xl font-bold">Logement entier proposé par {{ $annonce->user->name }}</h2>
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
                <p class="text-gray-700 leading-relaxed">{{ $annonce->description }}</p>
            </div>

            <div class="md:col-span-1">
                <div class="border rounded-2xl p-6 shadow-xl sticky top-10">
                    <p class="text-2xl font-bold mb-6"><span class="text-gray-900">{{ $annonce->prix_par_nuit }}$</span> <span class="text-gray-500 font-normal text-base">par nuit</span></p>
                    
                    <form action="#" method="POST">
                        @csrf
                        <div class="border rounded-lg mb-4">
                            <div class="grid grid-cols-2 border-b">
                                <div class="p-3 border-r">
                                    <label class="block text-[10px] font-bold uppercase">Arrivée</label>
                                        <input type="date" name="start_date" class="w-full text-sm outline-none" required>
                                </div>
                                <div class="p-3">
                                    <label class="block text-[10px] font-bold uppercase">Départ</label>6
                                        <input type="date" name="end_date" class="w-full text-sm outline-none" required>
                                </div>
                            </div>
                            <div class="p-3">
                                <label class="block text-[10px] font-bold uppercase">Voyageurs</label>
                                <select class="w-full text-sm outline-none bg-transparent">
                                    <option>1 voyageur</option>
                                    <option>2 voyageurs</option>
                                    <option>3 voyageurs</option>
                                    <option>plus..</option>
                                </select>
                            </div>
                        </div>

                            <button type="submit" class="w-full bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition">Réserver</button>
                    </form>

                    <p class="text-center text-gray-500 text-sm mt-4">Aucun montant ne vous sera débité pour le moment</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
