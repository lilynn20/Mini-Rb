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
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 fill-current"><path d="M16 1c2.008 0 3.463.963 4.751 3.269l.533 1.025c1.954 3.83 6.114 12.54 7.1 14.836l.145.353c.667 1.591.91 2.472.96 3.396l.01.415v.301c0 4.262-2.87 7.405-6.66 7.405-2.008 0-3.463-.963-4.751-3.269l-.533-1.025c-1.954-3.83-6.114-12.54-7.1-14.836l-.145-.353c-.667-1.591-.91-2.472-.96-3.396l-.01-.415v-.301c0-4.262 2.87-7.405 6.66-7.405zm0 2.249c-2.316 0-3.969 1.942-3.969 4.407 0 .793.183 1.54.526 2.366l.135.293c.895 1.91 4.708 9.814 6.69 13.82l.533 1.025c1.025 1.91 1.761 2.41 2.41 2.41.648 0 1.384-.5 2.41-2.41l.533-1.025c1.982-4.006 5.795-11.91 6.69-13.82l.135-.293c.343-.826.526-1.573.526-2.366 0-2.465-1.653-4.407-3.969-4.407-1.353 0-2.355.672-3.327 2.41l-.533 1.025c-1.606 3.104-4.802 9.537-6.31 12.607l-.145.31c-.512 1.03-.895 1.488-1.255 1.488-.36 0-.743-.458-1.255-1.488l-.145-.31c-1.508-3.07-4.704-9.503-6.31-12.607l-.533-1.025c-.972-1.738-1.974-2.41-3.327-2.41zm0 7.842c.648 0 1.384.5 2.41 2.41l.533 1.025c1.606 3.104 4.802 9.537 6.31 12.607l.145.31c.512 1.03.895 1.488 1.255 1.488.36 0 .743-.458 1.255-1.488l.145-.31c1.508-3.07 4.704-9.503 6.31-12.607l.533-1.025c1.025-1.91 1.761-2.41 2.41-2.41s1.384.5 2.41 2.41l.533 1.025c1.982 4.006 5.795 11.91 6.69 13.82l.135.293c.343.826.526 1.573.526 2.366 0 2.465-1.653 4.407-3.969 4.407-2.316 0-3.969-1.942-3.969-4.407 0-.793.183-1.54.526-2.366l.135-.293c.895-1.91 4.708-9.814 6.69-13.82l.533-1.025c1.025-1.91 1.761-2.41 2.41-2.41z"></path></svg>
                <span class="font-bold text-2xl tracking-tighter">Mini-Rb</span>
            </a>
        </div>
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
                        <img src="{{ Storage::disk('s3')->url($annonce->image) }}" class="h-20 mx-auto rounded mt-1">
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
