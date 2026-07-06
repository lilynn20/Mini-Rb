<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mettre mon logement - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b sticky top-0 z-50">
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2 text-rose-500 hover:text-rose-600 transition">
                <img src="{{ asset('images/logo.png') }}" class="h-8 w-8" alt="Mini-Rb Logo">
                <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
            </a>
        </div>
    </nav>

    <main class="max-w-2xl mx-auto px-8 py-10 bg-white shadow-md rounded-xl mt-10">
        <h1 class="text-2xl font-bold mb-6">Mettez votre logement sur Mini-Rb</h1>

        <form action="{{ route('annonces.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Titre de l'annonce</label>
                    <input type="text" name="titre" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Ex: Bel appartement au centre ville" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Décrivez votre logement..." required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Ville</label>
                        <input type="text" name="ville" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Ex: Paris" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Adresse</label>
                        <input type="text" name="adresse" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" placeholder="Ex: 12 rue de la Paix" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Prix par nuit (DH)</label>
                        <input type="number" name="prix_par_nuit" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" min="0" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Nombre de chambres</label>
                        <input type="number" name="nombre_de_chambres" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" min="1" value="1" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Images du logement</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none">
                    <p class="text-xs text-gray-500 mt-1">Vous pouvez télécharger plusieurs images (JPEG, PNG, etc., max 5MB chacune).</p>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-3">Équipements</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="rounded">
                                <span class="ml-2 text-gray-700 flex items-center gap-2">
                                    <x-amenity-icon :name="$amenity->name" class="w-5 h-5" />
                                    {{ $amenity->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-rose-500 text-white py-3 rounded-lg font-bold mt-8 hover:bg-rose-600 transition">Publier mon annonce</button>
        </form>
    </main>
</body>
</html>