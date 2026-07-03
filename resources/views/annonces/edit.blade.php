<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'annonce - Mini-Rb</title>
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
        <h1 class="text-2xl font-bold mb-6">Modifier votre annonce</h1>

        <form action="{{ route('annonces.update', $annonce) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                    <input type="text" name="titre" value="{{ old('titre', $annonce->titre) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" required>{{ old('description', $annonce->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Ville</label>
                        <input type="text" name="ville" value="{{ old('ville', $annonce->ville) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $annonce->adresse) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Prix par nuit (DH)</label>
                        <input type="number" name="prix_par_nuit" value="{{ old('prix_par_nuit', $annonce->prix_par_nuit) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" min="0" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Nombre de chambres</label>
                        <input type="number" name="nombre_de_chambres" value="{{ old('nombre_de_chambres', $annonce->nombre_de_chambres) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" min="1" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Ajouter des images</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none">
                    <p class="text-xs text-gray-500 mt-2">Vous pouvez ajouter plus d'images (JPEG, PNG, etc., max 5MB chacune).</p>

                    @if($annonce->images->count())
                        <p class="text-xs text-gray-500 mt-4 font-semibold">Images actuelles :</p>
                        <div class="flex gap-2 mt-2 flex-wrap">
                            @foreach($annonce->images as $img)
                                <div class="relative">
                                    <img src="{{ Storage::disk('s3')->url($img->image_path) }}" class="h-24 w-24 object-cover rounded border" alt="Preview">
                                    @if($img->is_primary)
                                        <span class="text-xs bg-rose-500 text-white px-2 py-1 rounded absolute top-0 left-0">Principale</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-3">Équipements</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="rounded"
                                    {{ $annonce->amenities->contains('id', $amenity->id) ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">{{ $amenity->icon }} {{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex space-x-4 mt-8">
                <a href="{{ route('annonces.show', $annonce) }}" class="w-1/3 bg-gray-100 text-center py-3 rounded-lg font-bold hover:bg-gray-200 transition">Annuler</a>
                <button type="submit" class="w-2/3 bg-rose-500 text-white py-3 rounded-lg font-bold hover:bg-rose-600 transition">Enregistrer les modifications</button>
            </div>
        </form>
    </main>
</body>
</html>