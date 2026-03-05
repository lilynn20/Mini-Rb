<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'annonce - Mini-Rb</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm py-4 px-8 flex justify-between items-center border-b">
        <a href="{{ route('home') }}" class="text-rose-500 font-bold text-2xl">Mini-Rb</a>
    </nav>

    <main class="max-w-2xl mx-auto px-8 py-10 bg-white shadow-md rounded-xl mt-10">
        <h1 class="text-2xl font-bold mb-6">Modifier votre annonce</h1>

        <form action="{{ route('annonces.update', $annonce) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                        <label class="block text-gray-700 font-semibold mb-1">Prix par nuit ($)</label>
                        <input type="number" name="prix_par_nuit" value="{{ old('prix_par_nuit', $annonce->prix_par_nuit) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" min="0" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Nombre de chambres</label>
                        <input type="number" name="nombre_de_chambres" value="{{ old('nombre_de_chambres', $annonce->nombre_de_chambres) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" min="1" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Image du logement (laisser vide pour conserver l'actuelle)</label>
                    <input type="file" name="image" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none" accept="image/*">
                    @if($annonce->image)
                        <p class="text-xs text-gray-500 mt-2 text-center">Image actuelle :</p>
                        <img src="{{ Storage::url($annonce->image) }}" class="h-20 mx-auto rounded mt-1">
                    @endif
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
